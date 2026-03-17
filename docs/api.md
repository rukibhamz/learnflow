# LearnFlow API Reference

**Base URL:** `/api`
**Authentication:** Bearer token via Laravel Sanctum
**Rate Limits:** 60 req/min (public), 120 req/min (authenticated)

---

## Authentication

### POST /api/auth/login
Login and receive an API token.

**Body:**
| Field | Type | Required |
|-------|------|----------|
| email | string | Yes |
| password | string | Yes |
| device_name | string | Yes |

**Response:** `200 OK`
```json
{ "token": "1|abc...", "user": { "id": 1, "name": "...", "email": "...", "role": "student" } }
```

### POST /api/auth/register
Create a new student account.

**Body:**
| Field | Type | Required |
|-------|------|----------|
| name | string | Yes |
| email | string | Yes |
| username | string | Yes |
| password | string | Yes |
| password_confirmation | string | Yes |
| device_name | string | Yes |

**Response:** `201 Created`

### GET /api/user
Get authenticated user profile. **Requires auth.**

**Response:** `200 OK`

### POST /api/auth/logout
Revoke current token. **Requires auth.**

**Response:** `200 OK`

---

## Courses

### GET /api/courses
List published courses with pagination.

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| search | string | Full-text search |
| level | string | Filter by level (beginner, intermediate, advanced) |
| free | string | "true" to show only free courses |
| sort | string | newest, popular, rated, price_low, price_high |
| per_page | int | Results per page (default: 15) |
| page | int | Page number |

**Response:** `200 OK`
```json
{
  "data": [{ "id": 1, "title": "...", "slug": "...", "price": 49.99, "average_rating": 4.5, ... }],
  "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 42 }
}
```

### GET /api/courses/{slug}
Get course detail with full curriculum.

**Response:** `200 OK` — includes `curriculum` array of sections with nested lessons.

---

## Enrollments

All enrollment endpoints **require auth.**

### GET /api/enrollments
List authenticated user's enrollments.

**Response:** `200 OK`
```json
{
  "data": [{ "id": 1, "course": { "id": 1, "title": "..." }, "progress_percentage": 65, "enrolled_at": "..." }]
}
```

### POST /api/enrollments
Enroll in a free course.

**Body:**
| Field | Type | Required |
|-------|------|----------|
| course_id | int | Yes |

**Response:** `201 Created` (free courses only; paid courses return `422`)

### GET /api/enrollments/{courseSlug}/progress
Get detailed progress for a specific enrollment.

**Response:** `200 OK` — includes `completed_lesson_ids` array.

---

## Certificates

### GET /api/certificates
List authenticated user's certificates. **Requires auth.**

**Response:** `200 OK`
```json
{
  "data": [{ "id": 1, "uuid": "...", "course": { "title": "..." }, "issued_at": "...", "verify_url": "...", "download_url": "..." }]
}
```

### GET /api/certificates/{uuid}/verify
Verify a certificate by UUID. **Public.**

**Response:** `200 OK`
```json
{ "data": { "uuid": "...", "student_name": "...", "course_title": "...", "issued_at": "...", "verified": true } }
```

---

## Notifications

All notification endpoints **require auth.**

### GET /api/notifications
List user notifications (latest 20).

**Query:** `?limit=20`

**Response:** `200 OK`
```json
{
  "data": [{ "id": "uuid", "type": "CourseCompletedNotification", "message": "...", "read_at": null, "created_at": "..." }],
  "unread_count": 3
}
```

### POST /api/notifications/{id}/read
Mark a single notification as read.

### POST /api/notifications/read-all
Mark all notifications as read.

---

## Error Responses

| Code | Meaning |
|------|---------|
| 401 | Unauthenticated — missing or invalid token |
| 403 | Forbidden — insufficient permissions |
| 404 | Resource not found |
| 422 | Validation error — check `errors` object |
| 429 | Rate limit exceeded |
