# Requirements Document

## Introduction

This feature adds password management capabilities to the admin user management dashboard. Admins can either set a new password directly for any user, or trigger a standard Laravel password reset email to be sent to the user. Both actions are accessible from the existing `AdminUserTable` Livewire component.

## Glossary

- **Admin**: An authenticated user with the `admin` role
- **AdminUserTable**: The Livewire component at `app/Livewire/AdminUserTable.php` that manages the user list on the admin dashboard
- **Target_User**: The user whose password is being managed by the admin
- **Password_Reset_Email**: The standard Laravel password reset notification sent via `Password::sendResetLink()`
- **Direct_Password_Set**: The action of an admin manually assigning a new plaintext password to a Target_User
- **Password_Reset_Modal**: The UI modal within AdminUserTable used to perform password management actions

## Requirements

### Requirement 1: Direct Password Set

**User Story:** As an admin, I want to set a new password directly for any user, so that I can help users who are locked out without requiring them to go through email.

#### Acceptance Criteria

1. WHEN an admin clicks the password management action for a Target_User, THE AdminUserTable SHALL open the Password_Reset_Modal pre-populated with the Target_User's name and email (read-only).
2. WHEN an admin submits a new password in the Password_Reset_Modal, THE AdminUserTable SHALL validate that the password is at least 8 characters long.
3. WHEN the new password passes validation, THE AdminUserTable SHALL hash and persist the new password to the Target_User's record.
4. WHEN the Direct_Password_Set succeeds, THE AdminUserTable SHALL close the Password_Reset_Modal and display a success flash message.
5. IF the new password fails validation, THEN THE AdminUserTable SHALL display inline validation errors without closing the Password_Reset_Modal.
6. IF an admin attempts to set a password for their own account via this interface, THEN THE AdminUserTable SHALL permit the action (self-service is allowed for password updates).

### Requirement 2: Send Password Reset Email

**User Story:** As an admin, I want to send a password reset email to a user, so that the user can securely set their own new password through the standard flow.

#### Acceptance Criteria

1. WHEN an admin clicks "Send Reset Email" in the Password_Reset_Modal, THE AdminUserTable SHALL invoke the Laravel password broker to send a Password_Reset_Email to the Target_User's registered email address.
2. WHEN the Password_Reset_Email is dispatched successfully, THE AdminUserTable SHALL close the Password_Reset_Modal and display a success flash message confirming the email was sent.
3. IF the password broker fails to send the Password_Reset_Email (e.g. invalid email, mail driver error), THEN THE AdminUserTable SHALL display an error flash message and keep the Password_Reset_Modal open.
4. THE AdminUserTable SHALL send the Password_Reset_Email regardless of whether the Target_User's email address has been verified.

### Requirement 3: Access Control

**User Story:** As a system owner, I want password management actions to be restricted to admins only, so that regular users and instructors cannot change other users' passwords.

#### Acceptance Criteria

1. WHILE a user does not have the `admin` role, THE AdminUserTable SHALL not render password management controls for any user record.
2. IF a non-admin user invokes a password management action directly (e.g. via crafted Livewire call), THEN THE AdminUserTable SHALL abort the request with a 403 response.
3. THE AdminUserTable SHALL enforce authorization checks server-side on both the Direct_Password_Set and Send_Password_Reset_Email actions.

### Requirement 4: UI Integration

**User Story:** As an admin, I want password management to be accessible from the existing user table, so that I don't have to navigate away to manage passwords.

#### Acceptance Criteria

1. THE AdminUserTable SHALL display a password management action button or icon for each user row in the admin user table.
2. WHEN the Password_Reset_Modal is open, THE AdminUserTable SHALL present two distinct actions: Direct_Password_Set (form input) and a "Send Reset Email" button.
3. WHEN an admin closes the Password_Reset_Modal without submitting, THE AdminUserTable SHALL reset all modal form fields to their default empty state.
4. THE AdminUserTable SHALL display the Target_User's name and email as read-only context within the Password_Reset_Modal so the admin can confirm they are acting on the correct user.
