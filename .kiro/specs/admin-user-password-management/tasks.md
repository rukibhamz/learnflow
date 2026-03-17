# Implementation Plan: Admin User Password Management

## Overview

Extend `AdminUserTable` with password management: new Livewire properties and methods in the PHP component, a lock_reset icon button per row in the blade view, and a new password modal. All logic stays inside the existing component — no new routes, controllers, or classes.

## Tasks

- [x] 1. Add new Livewire properties to AdminUserTable
  - Add `$showPasswordModal`, `$passwordUserId`, `$passwordUserName`, `$passwordUserEmail`, `$newPasswordValue` as public properties with their default values
  - _Requirements: 1.1, 4.1, 4.4_

- [x] 2. Implement `openPasswordModal()` and `closePasswordModal()`
  - [x] 2.1 Implement `openPasswordModal(int $userId)`
    - Load user via `User::findOrFail($userId)`, populate read-only fields, reset `$newPasswordValue`, set `$showPasswordModal = true`
    - _Requirements: 1.1, 4.4_
  - [ ]* 2.2 Write property test for Property 1: Modal opens with correct target user data
    - **Property 1: Modal opens with correct target user data**
    - Loop 100 iterations: create a random user, call `openPasswordModal`, assert `showPasswordModal` is `true` and name/email match
    - **Validates: Requirements 1.1**
  - [x] 2.3 Implement `closePasswordModal()`
    - Reset all five modal properties to their defaults
    - _Requirements: 4.3_
  - [ ]* 2.4 Write unit test for `closePasswordModal()`
    - Open modal for a user, call `closePasswordModal()`, assert all fields are reset to defaults
    - _Requirements: 4.3_

- [x] 3. Implement `setPassword()`
  - [x] 3.1 Implement `setPassword()` method
    - Call `abort_unless(auth()->user()->hasRole('admin'), 403)`, validate `newPasswordValue` with `required|string|min:8`, on pass load user and call `$user->update(['password' => Hash::make($this->newPasswordValue)])`, then `closePasswordModal()` and flash success
    - _Requirements: 1.2, 1.3, 1.4, 1.5, 3.2, 3.3_
  - [ ]* 3.2 Write property test for Property 2: Short passwords are rejected and modal stays open
    - **Property 2: Short passwords are rejected and modal stays open**
    - Loop 100 iterations: generate random strings of length 0–7, call `setPassword()`, assert validation error on `newPasswordValue` and `showPasswordModal` is still `true`
    - **Validates: Requirements 1.2, 1.5**
  - [ ]* 3.3 Write property test for Property 3: Password set is a hash round-trip
    - **Property 3: Password set is a hash round-trip**
    - Loop 100 iterations: generate random strings of length 8–64, call `setPassword()`, assert `Hash::check($plain, $user->fresh()->password)` is `true`
    - **Validates: Requirements 1.3**
  - [ ]* 3.4 Write unit tests for `setPassword()`
    - Admin sets valid password → DB hash verifies, modal closes, success flash
    - Admin sets password for own account → succeeds (Requirement 1.6)
    - _Requirements: 1.3, 1.4, 1.6_

- [x] 4. Implement `sendResetEmail()`
  - [x] 4.1 Implement `sendResetEmail()` method
    - Call `abort_unless(auth()->user()->hasRole('admin'), 403)`, call `Password::sendResetLink(['email' => $this->passwordUserEmail])`, on `Password::RESET_LINK_SENT` call `closePasswordModal()` and flash success, otherwise flash error and keep modal open
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.2, 3.3_
  - [ ]* 4.2 Write unit tests for `sendResetEmail()`
    - Mock broker success → modal closes, success flash (Requirement 2.2)
    - Mock broker failure → error flash, modal stays open (Requirement 2.3)
    - Unverified user email → broker still called (Requirement 2.4)
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x] 5. Checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Add access control tests
  - [x] 6.1 Write unit test for non-admin UI rendering
    - Assert non-admin (student/instructor) rendered output does not contain the password management button
    - _Requirements: 3.1_
  - [ ]* 6.2 Write property test for Property 4: Non-admin users see no password management controls
    - **Property 4: Non-admin users see no password management controls**
    - Loop 100 iterations: create random student/instructor users, render component, assert password button absent
    - **Validates: Requirements 3.1**
  - [ ]* 6.3 Write property test for Property 5: Non-admin direct invocation returns 403
    - **Property 5: Non-admin direct invocation returns 403**
    - Loop 100 iterations: create random non-admin users, call `setPassword()` and `sendResetEmail()`, assert both abort with 403
    - **Validates: Requirements 3.2, 3.3**

- [x] 7. Add password icon button to blade row actions
  - In `admin-user-table.blade.php`, add a `lock_reset` icon button with `wire:click="openPasswordModal({{ $user->id }})"` inside each row's actions div, visible only to admins via `@if(auth()->user()->hasRole('admin'))`
  - _Requirements: 3.1, 4.1_

- [x] 8. Add password modal to blade view
  - [x] 8.1 Add the Password_Reset_Modal block after the existing Edit modal
    - Follow the same backdrop + card + form structure as the edit modal
    - Show Target_User name and email as read-only fields
    - Include a password input bound to `wire:model="newPasswordValue"` with `@error('newPasswordValue')` display
    - Include a "Set Password" submit button (`wire:submit="setPassword()"`) and a "Send Reset Email" button (`wire:click="sendResetEmail()"`)
    - Include a Cancel button that calls `wire:click="closePasswordModal()"`
    - _Requirements: 4.2, 4.3, 4.4_
  - [ ]* 8.2 Write unit test for modal UI integration
    - Admin opens modal → name and email visible in rendered output
    - Cancel closes modal and resets fields
    - _Requirements: 4.2, 4.3, 4.4_

- [x] 9. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Property tests use plain PHP loops of 100 iterations — no eris dependency needed
- Each property test must include a comment: `// Feature: admin-user-password-management, Property {N}: {property_text}`
- All new tests go in a new `AdminUserPasswordManagementTest` class extending the same `AdminUserTableTest` pattern (`RefreshDatabase`, `RolesAndPermissionsSeeder`, `Livewire::actingAs()`)
