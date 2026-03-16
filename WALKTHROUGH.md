# LearnFlow LMS — Implementation Walkthrough

**Document Version:** 2.0  
**Last Updated:** March 2026  
**PRD Reference:** LearnFlow LMS v1.0

This document provides a detailed walkthrough of the LearnFlow LMS implementation progress, comparing the current codebase against the Product Requirements Document (PRD).

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Authentication & User Management](#2-authentication--user-management)
3. [Course Management](#3-course-management)
4. [Enrollment & Progress Tracking](#4-enrollment--progress-tracking)
5. [Quiz Engine](#5-quiz-engine)
6. [Payments & Monetisation](#6-payments--monetisation)
7. [Certificates](#7-certificates)
8. [Admin Dashboard](#8-admin-dashboard)
9. [Notifications](#9-notifications)
10. [REST API](#10-rest-api)
11. [Non-Functional Requirements](#11-non-functional-requirements)
12. [Testing](#12-testing)
13. [Summary Matrix](#13-summary-matrix)

---

## 1. Executive Summary

### Overall Progress: ~55% Complete

Significant progress since v1.0. The enrollment flow is wired end-to-end for free courses and paid courses now support real Stripe Checkout with webhook fulfilment (Order → Paid → enrol), order history, and PDF invoices. The curriculum builder is fully functional with section/lesson CRUD and reordering, the lesson editor supports all content types including S3 video upload, and the student dashboard tracks real progress + wishlist. The admin and instructor surfaces are largely complete. Key remaining gaps are the learn-page completion flow + access gate, quiz taking UI + auto-grading, certificate issuance on completion, and the REST API.

| Category | Status | Completion |
|----------|--------|------------|
| Authentication & Users | ✅ Mostly Complete | 90% |
| Course Management | ✅ Mostly Complete | 75% |
| Enrollment & Progress | ⚠️ Partial | 55% |
| Quiz Engine | ⚠️ Partial | 25% |
| Payments | ✅ Mostly Complete | 70% |
| Certificates | ⚠️ Partial | 25% |
| Admin Dashboard | ✅ Mostly Complete | 75% |
| Notifications | ⚠️ Partial | 40% |
| REST API | ❌ Not Started | 0% |
| Testing | ⚠️ Partial | 40% |

---

## 2. Authentication & User Management

### PRD Requirements (Section 5.1)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Registration / Login | P0 | ✅ Complete | Email + password, username login, email verification |
| Social Login (Google) | P1 | ✅ Complete | Google OAuth via Laravel Socialite |
| Social Login (GitHub) | P1 | ❌ Not Started | Not implemented |
| Forgot Password | P0 | ✅ Complete | Token-based password reset via email |
| Profile Management | P0 | ⚠️ Partial | Name, email, password change work; avatar upload, bio, social links UI incomplete |
| Role Assignment | P0 | ✅ Complete | Admin can assign roles via user management |
| Account Suspension | P0 | ✅ Complete | `suspended_at` field + admin toggle implemented; middleware to block suspended users still missing |
| Wishlist | P1 | ✅ Complete | JSON field on user, `WishlistButton` Livewire component, dashboard wishlist tab |

### Implemented Files

```
app/Http/Controllers/Auth/
├── AuthenticatedSessionController.php    # Login/logout (email or username)
├── RegisteredUserController.php          # Registration with username
├── GoogleAuthController.php              # Google OAuth
├── VerifyEmailController.php             # Email verification
├── EmailVerificationNotificationController.php
└── ConfirmablePasswordController.php

app/Models/User.php                       # Roles, suspension, wishlist, avatar, social links
app/Notifications/QueuedVerifyEmail.php   # Queued email verification
app/Livewire/WishlistButton.php           # Toggle wishlist from course cards
```

### What's Missing

1. **GitHub OAuth** — Only Google is available
2. **Profile avatar upload** — Spatie Media Library wired in model; no upload UI
3. **Bio and social links** — Fields exist but not editable in profile UI
4. **Suspended user middleware** — `suspended_at` is set by admin but no middleware blocks login/access

---

## 3. Course Management

### PRD Requirements (Section 5.2)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Course CRUD | P0 | ✅ Complete | Create, edit, delete, soft-delete with `InstructorCourseForm` |
| Course Status Workflow | P0 | ✅ Complete | Draft → Review → Published → Archived; submit/unpublish actions |
| Sections & Lessons CRUD | P0 | ✅ Complete | Full CRUD via `CourseCurriculum` Livewire component |
| Lesson Types (Video/Text/PDF/Embed) | P0 | ✅ Complete | All types handled in `LessonEditor`; PDF via Spatie Media Library |
| Video Upload (S3) | P0 | ✅ Complete | Presigned S3 URL generation in `LessonEditor`; client-side direct upload |
| Drag-and-drop reordering | P1 | ✅ Complete | `reorderSections` and `reorderLessons` methods in `CourseCurriculum` |
| Move lesson between sections | P1 | ✅ Complete | `moveLessonToSection` method implemented |
| Drip Content | P1 | ✅ Complete | `unlock_after_days` field on lessons; migration added; editor exposes it |
| Course Cloning | P2 | ✅ Complete | `duplicateCourse` in `InstructorCourseIndex` clones sections and lessons |
| Prerequisites | P1 | ❌ Not Started | No prerequisite fields or enrollment validation |
| Thumbnail Upload | P0 | ⚠️ Partial | Spatie Media Library collection registered; no upload UI in form |

### Implemented Files

```
app/Models/
├── Course.php                # Full model with relationships, scopes, computed attributes
├── Section.php               # Global ordered scope
└── Lesson.php                # All types, drip field, media collection

app/Enums/
├── CourseStatus.php          # Draft, Review, Published, Archived
├── CourseLevel.php           # Beginner, Intermediate, Advanced
└── LessonType.php            # Video, Text, PDF, Embed

app/Livewire/
├── InstructorCourseIndex.php # List, search, filter, delete, duplicate, submit, unpublish
├── InstructorCourseForm.php  # Create/edit course details with slug auto-generation
├── CourseCurriculum.php      # Full section/lesson CRUD, reorder, move, inline edit
├── LessonEditor.php          # Edit all lesson types, S3 upload, PDF upload, drip
├── CourseIndex.php           # Public course listing with search, filter, sort
└── AdminCourseReview.php     # Approve/reject review queue

app/Http/Requests/
├── StoreCourseRequest.php    # Validation + policy check for create
└── UpdateCourseRequest.php   # Validation + policy check for update

app/Policies/CoursePolicy.php # viewAny, view, create, update, delete, publish
```

### What's Missing

1. **Thumbnail upload UI** — Media collection exists but the form has no file input wired
2. **Prerequisites** — No prerequisite course fields or enrollment gate

---

## 4. Enrollment & Progress Tracking

### PRD Requirements (Section 5.3)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Free Enrolment | P0 | ✅ Complete | `EnrolmentController` + `EnrolmentService` handle free courses end-to-end |
| Paid Enrolment | P0 | ✅ Complete | Stripe Checkout session creation + webhook fulfilment (Order → Paid → enrol) |
| Duplicate enrolment guard | P0 | ✅ Complete | `isAlreadyEnrolled` check in service and controller |
| Enrolment validation | P0 | ✅ Complete | Checks course is Published before enrolling |
| Progress Calculation | P0 | ✅ Complete | `progress_percentage` on `Enrollment` model (bug fixed: ambiguous `pluck`) |
| Resume Learning | P0 | ✅ Complete | `StudentDashboard` computes `next_lesson` (first incomplete lesson) |
| Lesson Completion | P0 | ❌ Not Started | No "mark complete" action wired; learn page sidebar button is static |
| Enrolment Expiry | P1 | ⚠️ Partial | `expires_at` field exists; not enforced on access |
| Instructor Auto-Enrol | P0 | ❌ Not Started | Instructors not auto-enrolled in own courses |
| Wishlist | P1 | ✅ Complete | Toggle from course cards; dashboard wishlist tab shows real data |

### Implemented Files

```
app/Http/Controllers/EnrolmentController.php   # store: free enrol or redirect to checkout
app/Http/Controllers/PaymentController.php    # Stripe Checkout session + pending Order
app/Http/Controllers/StripeWebhookController.php # Stripe webhook fulfilment + idempotency
app/Services/EnrolmentService.php              # enrol(), isAlreadyEnrolled(), canEnrol()
app/Services/CouponService.php                 # Validate/apply coupons for checkout
app/Events/UserEnrolled.php                    # Fired on successful enrolment
app/Jobs/SendEnrolmentConfirmationEmail.php    # Queued job dispatched on enrolment
app/Jobs/SendPaymentReceiptEmail.php           # Queued receipt email
app/Jobs/SendPaymentFailedEmail.php            # Queued payment failed email
app/Jobs/GenerateInvoicePdf.php                # Queued invoice PDF generation (DomPDF)

app/Models/
├── Enrollment.php            # progress_percentage computed attribute (fixed)
└── LessonProgress.php        # Lesson completion records

app/Livewire/
├── StudentDashboard.php      # Real in-progress/completed/wishlist tabs, resume learning
└── LessonPlayer.php          # Loads real course sections and lessons; lesson switching
```

### What's Missing

1. **Lesson completion action** — `LessonPlayer` loads real data but the "Mark complete" button is static HTML; no Livewire action creates `LessonProgress` records
2. **Learn page access control** — Route doesn't verify the user is enrolled before showing content
3. **Expiry enforcement** — `expires_at` not checked on learn route access
4. **Instructor auto-enrol** — Not triggered on course creation

---

## 5. Quiz Engine

### PRD Requirements (Section 5.4)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Question Types (MCQ/TF/Short) | P0 | ✅ Complete | Enum and model fields defined |
| Quiz Settings | P0 | ✅ Complete | Time limit, attempts, passing score in model |
| Auto-Grading | P0 | ❌ Not Started | No grading logic |
| Attempt History | P0 | ❌ Not Started | No UI to view attempts |
| Retakes | P0 | ⚠️ Partial | `attempts_allowed` field exists; not enforced |
| Question Bank | P1 | ❌ Not Started | Questions tied to single quiz |
| Feedback | P1 | ⚠️ Partial | `explanation` field exists; not displayed |

### Implemented Files

```
app/Models/
├── Quiz.php                  # Quiz configuration
├── QuizQuestion.php          # Questions with options, ordered scope
└── QuizAttempt.php           # Student attempts with answers/score/passed

app/Enums/QuizQuestionType.php  # MCQ, TrueFalse, ShortAnswer
app/Livewire/QuizBuilder.php    # Stub — hardcoded single question, no real CRUD
app/Policies/QuizAttemptPolicy.php
```

### What's Missing

1. **Quiz CRUD** — `QuizBuilder` is a stub; no create/edit/delete for quizzes or questions
2. **Quiz taking UI** — No interface for students to take and submit quizzes
3. **Auto-grading** — No score calculation logic
4. **Attempt history** — No UI to list or review past attempts

---

## 6. Payments & Monetisation

### PRD Requirements (Section 5.5)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| One-Time Purchase | P0 | ✅ Complete | Stripe Checkout + webhook fulfilment + order history + invoice PDF |
| Subscription Plans | P1 | ⚠️ Partial | Cashier installed; no plans or UI |
| Coupon Codes | P0 | ⚠️ Partial | Coupon model complete; coupon validation + discount applied in checkout via `CouponService`; no admin UI to manage coupons |
| Free Courses | P0 | ✅ Complete | Free enrolment flow fully wired |
| Instructor Payouts | P1 | ❌ Not Started | No revenue split or Stripe Connect |
| Invoice / Receipt | P0 | ✅ Complete | Receipt/failure emails queued; invoice PDF generated via DomPDF and downloadable from /my-orders |
| Refunds | P1 | ❌ Not Started | No refund handling |

### Implemented Files

```
app/Models/
├── Order.php                 # Payment records with Stripe fields, paid scope
├── Coupon.php                # Discount codes with isValidFor() validation
└── CouponUsage.php           # Coupon-order linking

app/Enums/
├── OrderStatus.php           # Pending, Paid, Failed, Refunded
└── DiscountType.php          # Fixed, Percentage

resources/views/checkout/course.blade.php  # Checkout UI with "Payment Coming Soon" placeholder
resources/views/payment/success.blade.php  # Post-checkout success page (session_id based)
resources/views/student/orders.blade.php   # Order history + invoice download links
resources/views/invoices/course.blade.php  # Invoice PDF template (DomPDF)
resources/views/emails/payment-receipt.blade.php
resources/views/emails/payment-failed.blade.php

config/cashier.php            # Stripe configuration
```

### Stripe One‑Time Purchase Flow (Current)

**User-visible flow**

1. **Course page CTA → checkout**
   - Paid courses post to `POST /checkout/{course}` (`PaymentController@checkout`).
2. **Stripe-hosted Checkout**
   - A Stripe Checkout Session is created with a single line item (course title, amount).
3. **Return**
   - On success, Stripe redirects to `GET /payment/success?session_id={CHECKOUT_SESSION_ID}`.
   - On cancel, Stripe redirects back to `GET /courses/{slug}`.
4. **Order history**
   - Student can view purchases at `GET /my-orders`.
   - If invoice is generated, they can download it from `GET /my-orders/{order}/invoice`.

**Backend fulfilment**

- **Pending Order creation**:
  - `PaymentController@checkout` creates an `orders` row with `status=pending` and `stripe_session_id`.
  - Coupon discounts are applied if a `coupon_code` exists in session (via `CouponService`); metadata stores coupon/discount info.
- **Webhook fulfilment**:
  - Stripe calls `POST /webhooks/stripe` (route registered outside the `web` middleware group / CSRF).
  - `StripeWebhookController` verifies the signature using `STRIPE_WEBHOOK_SECRET` and processes events idempotently.
  - On `checkout.session.completed`:
    - Order is marked `paid` and payment intent stored.
    - `EnrolmentService::enrol()` is called to create the `Enrollment`.
    - Jobs are queued:
      - `SendPaymentReceiptEmail`
      - `GenerateInvoicePdf` (DomPDF; stores generated path in `Order.metadata.invoice_path`)
  - On `payment_intent.payment_failed`:
    - Order is marked `failed`.
    - `SendPaymentFailedEmail` is queued.

### What's Missing

1. **Cashier Billable trait usage** — Required for subscriptions; currently only one-time payments are implemented
2. **Coupon admin UI** — No CRUD for coupons
3. **Refunds** — No refund handling or admin UI
4. **Subscriptions** — No plans, pricing page, or subscription UI (P1)

---

## 7. Certificates

### PRD Requirements (Section 5.6)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Auto-Issue on Completion | P0 | ❌ Not Started | No completion detection or certificate creation |
| PDF Generation | P0 | ❌ Not Started | DomPDF installed but not used |
| Unique UUID Verification | P0 | ⚠️ Partial | Model has UUID; verification page uses hardcoded data |
| Custom Templates | P1 | ❌ Not Started | No template upload |

### Implemented Files

```
app/Models/Certificate.php          # Certificate model with UUID, verify_url accessor
app/Policies/CertificatePolicy.php  # view: owner or admin

resources/views/certificates/verify.blade.php   # Static/hardcoded — not dynamic
resources/views/student/certificates.blade.php  # Static/hardcoded — not dynamic
```

### What's Missing

1. **Certificate creation** — No logic to create certificate on course completion
2. **PDF generation** — No PDF rendering with DomPDF
3. **Dynamic verification** — Page doesn't load certificate by UUID from DB
4. **Student certificates list** — Uses hardcoded data
5. **Download functionality** — No PDF download

---

## 8. Admin Dashboard

### PRD Requirements (Section 5.7)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Platform KPIs | P0 | ⚠️ Partial | User/course/enrollment counts real; revenue is mocked (`count * 49`) |
| User Management | P0 | ✅ Complete | Full CRUD, search, filter by role, suspend/reactivate |
| Course Moderation | P0 | ✅ Complete | Approve/reject review queue with search |
| Coupon Management | P0 | ❌ Not Started | No admin UI |
| Revenue Reports | P0 | ⚠️ Partial | Basic count shown; no real order-based revenue |
| System Settings | P0 | ✅ Complete | Mailer, Stripe keys, test email |

### Implemented Files

```
app/Livewire/
├── AdminDashboard.php        # KPI stats (revenue mocked)
├── AdminUserTable.php        # Full CRUD with create/edit modals, suspend, delete
└── AdminCourseReview.php     # Approve/reject with search

app/Http/Controllers/Admin/SettingsController.php

resources/views/admin/
├── dashboard.blade.php
├── users/index.blade.php
├── courses/index.blade.php
├── courses/review.blade.php
└── settings.blade.php
```

### What's Missing

1. **Accurate revenue** — Dashboard multiplies enrollment count by a hardcoded $49; needs real `Order` data
2. **Coupon management** — No CRUD for coupons
3. **Order management** — No order list or refund handling
4. **Impersonation** — Button exists but not functional

---

## 9. Notifications

### PRD Requirements (Section 5.8)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| Email: Welcome | P0 | ❌ Not Started | No welcome email on registration |
| Email: Enrolment Confirmation | P0 | ✅ Complete | Queued job + Mailable + Markdown template |
| Email: Quiz Result | P0 | ❌ Not Started | No quiz notification |
| Email: Certificate Issued | P0 | ❌ Not Started | No certificate notification |
| Email: Payment Receipt | P0 | ✅ Complete | Queued receipt email on webhook completion |
| In-app Notifications | P0 | ⚠️ Partial | UI exists; `unreadCount` hardcoded to 3; no DB backend |
| Queued Jobs | P0 | ⚠️ Partial | Enrolment + payment/invoice jobs exist; quiz/certificate jobs still missing |

### Implemented Files

```
app/Notifications/QueuedVerifyEmail.php         # Email verification (queued)
app/Jobs/SendEnrolmentConfirmationEmail.php     # Queued enrolment confirmation
app/Mail/EnrolmentConfirmation.php              # Mailable with Markdown template
app/Events/UserEnrolled.php                     # Event fired on enrolment
resources/views/emails/enrolment-confirmation.blade.php

app/Jobs/SendPaymentReceiptEmail.php            # Queued payment receipt
app/Jobs/SendPaymentFailedEmail.php             # Queued payment failed
app/Jobs/GenerateInvoicePdf.php                 # Queued invoice generation
app/Mail/PaymentReceipt.php
app/Mail/PaymentFailed.php
resources/views/emails/payment-receipt.blade.php
resources/views/emails/payment-failed.blade.php

app/Livewire/NotificationBell.php               # UI only — hardcoded unreadCount = 3
```

### What's Missing

1. **Welcome email** — No notification on registration
2. **Database notifications** — No `notifications` table migration; `NotificationBell` uses static data
3. **Event listeners** — `UserEnrolled` event is fired but no listener is registered
4. **In-app backend** — `NotificationBell` needs to read from DB notifications
5. **Quiz/certificate notifications** — Not implemented

---

## 10. REST API

### PRD Requirements (Section 9)

| Feature | PRD Priority | Status | Notes |
|---------|--------------|--------|-------|
| API Routes | P0 | ❌ Not Started | No `routes/api.php` |
| API Controllers | P0 | ❌ Not Started | No API controllers |
| Sanctum Auth | P0 | ⚠️ Partial | Sanctum installed; not configured for API |
| API Versioning | P0 | ❌ Not Started | No `/api/v1/` prefix |
| API Documentation | P1 | ❌ Not Started | No documentation |

### What's Missing

Everything — REST API is not implemented.

---

## 11. Non-Functional Requirements

### Performance (Section 6.1)

| Requirement | Status | Notes |
|-------------|--------|-------|
| Sub-300ms API responses | ⚠️ Partial | No API; web responses not benchmarked |
| Redis caching | ⚠️ Partial | Redis configured; limited cache usage |
| CDN for videos | ⚠️ Partial | S3 upload wired; no CloudFront/CDN configuration |
| Queue offloading | ⚠️ Partial | Enrolment confirmation queued; most jobs still missing |

### Security (Section 6.2)

| Requirement | Status | Notes |
|-------------|--------|-------|
| Auth on all routes | ✅ Complete | Middleware applied |
| Role-based middleware | ✅ Complete | `IsAdmin`, `IsInstructor`, `IsStudent` middleware |
| Signed URLs for content | ❌ Not Started | No presigned S3 URLs for lesson content |
| CSRF protection | ✅ Complete | Laravel default |
| Stripe webhook verification | ✅ Complete | Signature verified in `StripeWebhookController` using `STRIPE_WEBHOOK_SECRET` |
| Role-based policies | ✅ Complete | Spatie Permission + policies for Course, Enrollment, Certificate, QuizAttempt |
| Rate limiting | ⚠️ Partial | On auth routes; not on enrollment/quiz |
| XSS prevention | ✅ Complete | Blade escaping |
| Suspended user blocking | ❌ Not Started | `suspended_at` set but no middleware enforces it |

---

## 12. Testing

### Current Test Coverage

| Suite | Tests | Status |
|-------|-------|--------|
| Auth (login, register, password reset, verification) | 18 | ✅ All passing |
| Profile (update, delete account) | 5 | ✅ All passing |
| Course Management (Livewire) | 11 | ✅ All passing |
| Admin Course Review (Livewire) | 6 | ✅ All passing |
| Admin User Table (Livewire) | 12 | ✅ All passing |
| Course Model (unit) | 7 | ✅ All passing |
| User Model (unit) | 8 | ✅ All passing |
| Course Policy (unit) | 10 | ✅ All passing |
| Enrollment Progress (unit) | 7 | ✅ All passing |
| Coupon Model (unit) | 9 | ✅ All passing |
| Stripe Checkout / Webhook (feature) | 2 | ⚠️ Partial | Basic coverage; fixture/signature mocking needs strengthening |
| **Total** | **99** | **✅ 99/99 passing** |

### Factories Added

```
database/factories/
├── UserFactory.php       # Existing
├── CourseFactory.php     # Added — published(), inReview(), free() states
├── SectionFactory.php    # Added
├── LessonFactory.php     # Added — video(), preview() states
├── EnrollmentFactory.php # Added — completed(), expired() states
└── CouponFactory.php     # Added — fixed(), inactive(), expired() states
```

### Bug Fixed During Testing

`Enrollment::getProgressPercentageAttribute()` used `pluck('id')` on a `hasManyThrough` query joining `lessons` and `sections`, causing an ambiguous column error in SQLite. Fixed to `pluck('lessons.id')`.

### What's Missing

- Enrolment flow tests (free enrol, duplicate guard, paid redirect)
- Stripe webhook signature verification test using Stripe fixture
- `EnrolmentService` unit tests
- `CourseCurriculum` Livewire tests (section/lesson CRUD, reorder)
- `LessonEditor` tests
- `StudentDashboard` tests
- E2E tests (Dusk not configured)
- Target: 80% coverage per PRD

---

## 13. Summary Matrix

### By PRD Phase

| Phase | Deliverables | Status | Completion |
|-------|--------------|--------|------------|
| **Phase 1** | Auth, roles, course CRUD, file upload, basic enrollment | ✅ Mostly Complete | 80% |
| **Phase 2** | Lesson player, progress tracking, quiz engine, drip, resume | ⚠️ Partial | 45% |
| **Phase 3** | Stripe payments, subscriptions, coupons, webhooks | ⚠️ Partial | 20% |
| **Phase 4** | PDF certificates, notifications, email sequences, reviews | ⚠️ Partial | 20% |
| **Phase 5** | Admin analytics, Horizon, Scout search | ⚠️ Partial | 40% |
| **Phase 6** | REST API, test coverage, Dusk E2E, staging QA | ❌ Not Started | 5% |

### Priority Items to Complete

#### P0 (Critical for MVP)

1. **Lesson completion** — Wire "Mark complete" button in `LessonPlayer` to create `LessonProgress` records
2. **Learn page access control** — Gate the learn route behind enrollment check
3. **Lesson player UX** — Prev/next navigation, drip lock UI, sidebar completion state from `LessonProgress`
5. **Certificate generation** — Auto-issue PDF on course completion using DomPDF
6. **Quiz taking UI** — Build student quiz interface with auto-grading
7. **Suspended user middleware** — Block suspended users from accessing the app

#### P1 (Important for Launch)

1. **In-app notifications** — Wire `NotificationBell` to database notifications
2. **Welcome email** — Send on registration
3. **Thumbnail upload** — Wire file input in `InstructorCourseForm`
4. **Revenue reports** — Replace mocked revenue with real `Order` data
5. **REST API** — Mobile-ready API endpoints with Sanctum

#### P2 (Nice to Have)

1. **GitHub OAuth** — Add alongside Google
2. **Question bank** — Reusable quiz questions
3. **Custom certificate templates** — Admin-uploadable templates
4. **Prerequisites** — Course prerequisite validation on enrollment

---

## File Structure Reference

```
learnflow/
├── app/
│   ├── Enums/                    # ✅ Complete
│   ├── Events/                   # ⚠️ Partial (UserEnrolled only)
│   ├── Http/Controllers/
│   │   ├── Admin/               # ⚠️ Partial (Settings only)
│   │   ├── Auth/                # ✅ Complete
│   │   ├── EnrolmentController  # ✅ Complete (free enrol + paid redirect)
│   │   ├── PaymentController    # ✅ Complete (Stripe Checkout)
│   │   └── StripeWebhookController # ✅ Complete (webhook fulfilment)
│   │   ├── Instructor/          # ❌ Missing (routes use closures)
│   │   └── Student/             # ❌ Missing (routes use closures)
│   ├── Http/Requests/           # ✅ StoreCourseRequest, UpdateCourseRequest
│   ├── Jobs/                    # ⚠️ Partial (enrolment + payment/invoice jobs)
│   ├── Livewire/                # ⚠️ Partial (curriculum/editor complete; quiz stub)
│   ├── Mail/                    # ⚠️ Partial (EnrolmentConfirmation + payment mailables)
│   ├── Models/                  # ✅ Complete
│   ├── Notifications/           # ⚠️ Partial (QueuedVerifyEmail only)
│   ├── Policies/                # ✅ Complete
│   └── Services/                # ⚠️ Partial (EnrolmentService + CouponService)
├── database/
│   ├── migrations/              # ✅ Complete
│   ├── seeders/                 # ⚠️ Partial (RolesAndPermissions only)
│   └── factories/               # ⚠️ Partial (User, Course, Section, Lesson, Enrollment, Coupon)
├── resources/views/
│   ├── admin/                   # ✅ Mostly Complete
│   ├── emails/                  # ⚠️ Partial (enrolment-confirmation + payment emails)
│   ├── invoices/                # ✅ Added (DomPDF templates)
│   ├── instructor/              # ✅ Mostly Complete
│   ├── student/                 # ⚠️ Partial (certificates/courses use hardcoded data)
│   └── livewire/                # ⚠️ Partial
├── routes/
│   ├── web.php                  # ✅ Complete
│   ├── auth.php                 # ✅ Complete
│   └── api.php                  # ❌ Missing
└── tests/
    ├── Feature/Auth/            # ✅ Complete
    ├── Feature/                 # ⚠️ Partial (course mgmt, admin — missing enrolment, quiz)
    └── Unit/                    # ⚠️ Partial (models, policy, coupon — missing service tests)
```

---

## Next Steps

1. **Immediate** — Wire lesson completion in `LessonPlayer`; add enrollment access gate to learn route
2. **Short-term** — Quiz taking UI; certificate creation on completion; strengthen Stripe webhook tests
3. **Medium-term** — Certificate PDF generation; in-app notifications backend
4. **Long-term** — REST API; comprehensive test coverage to 80%

---

*This document should be updated as features are completed.*
