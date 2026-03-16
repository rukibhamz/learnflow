# Requirements Document

## Introduction

The Instructor Course Management module enables instructors to create, edit, organize, and manage their courses within the LearnFlow platform. This module provides a comprehensive interface for course lifecycle management, from initial creation through content organization and publication, using Livewire components for reactive user interactions.

## Glossary

- **Instructor**: A user with permission to create and manage courses
- **Course**: A learning program containing sections, lessons, and metadata
- **Section**: A logical grouping of lessons within a course
- **Lesson**: An individual learning unit (video, text, or PDF)
- **Enrollment**: A student's registration in a course
- **Course_Index**: The Livewire component displaying the instructor's course list
- **Course_Form**: The Livewire component for creating and editing course metadata
- **Course_Curriculum**: The Livewire component for managing sections and lessons
- **Lesson_Editor**: The Livewire component for editing individual lesson content
- **Draft**: A course status indicating unpublished work in progress
- **Published**: A course status indicating availability to students
- **Under_Review**: A course status indicating submission for approval
- **Soft_Delete**: Marking a record as deleted without removing from database
- **Autosave**: Automatic periodic saving of form data
- **Drip_Content**: Lessons that unlock after a specified number of days
- **Preview_Lesson**: A lesson accessible without enrollment
- **Media_Library**: The spatie/laravel-medialibrary package for file management
- **Form_Request**: Laravel validation class for incoming HTTP requests
- **Flash_Message**: Temporary user feedback message displayed after actions
- **Presigned_URL**: A temporary authenticated URL for direct S3 uploads

## Requirements

### Requirement 1: Display Instructor Course List

**User Story:** As an instructor, I want to view all my courses in a table, so that I can quickly access and manage them.

#### Acceptance Criteria

1. THE Course_Index SHALL display a table containing all courses belonging to the authenticated instructor
2. FOR EACH course, THE Course_Index SHALL display the title, status badge, enrolled student count, price, and creation date
3. WHEN an instructor views the course list, THE Course_Index SHALL sort courses by creation date in descending order
4. THE Course_Index SHALL display a status badge with distinct visual styling for Draft, Published, and Under_Review statuses
5. WHEN a course has zero price, THE Course_Index SHALL display "Free" instead of the numeric value

### Requirement 2: Search and Filter Courses

**User Story:** As an instructor, I want to search and filter my courses, so that I can quickly find specific courses.

#### Acceptance Criteria

1. THE Course_Index SHALL provide a search input field for filtering courses by title
2. WHEN an instructor types in the search field, THE Course_Index SHALL filter the course list to show only courses with titles containing the search term
3. THE Course_Index SHALL provide a status filter dropdown with options for all statuses plus "All"
4. WHEN an instructor selects a status filter, THE Course_Index SHALL display only courses matching that status
5. WHEN both search and filter are applied, THE Course_Index SHALL display courses matching both criteria

### Requirement 3: Perform Course Actions

**User Story:** As an instructor, I want to edit, preview, delete, and duplicate courses, so that I can manage my course catalog.

#### Acceptance Criteria

1. FOR EACH course in the list, THE Course_Index SHALL display action buttons for Edit, Preview, Delete, and Duplicate
2. WHEN an instructor clicks Edit, THE Course_Index SHALL navigate to the Course_Form for that course
3. WHEN an instructor clicks Preview, THE Course_Index SHALL open the student-facing course view in a new tab
4. WHEN an instructor clicks Delete, THE Course_Index SHALL soft delete the course and display a flash message
5. WHEN an instructor clicks Duplicate, THE Course_Index SHALL create a copy of the course with "(Copy)" appended to the title and display a flash message

### Requirement 4: Create and Edit Course Metadata

**User Story:** As an instructor, I want to create and edit course information, so that I can provide complete course details to students.

#### Acceptance Criteria

1. THE Course_Form SHALL provide input fields for title, short_description, description, thumbnail, price, level, language, requirements, and outcomes
2. WHEN an instructor enters a course title, THE Course_Form SHALL automatically generate a URL-friendly slug
3. THE Course_Form SHALL provide a rich text editor for the description field
4. THE Course_Form SHALL allow thumbnail upload and display a preview of the uploaded image
5. WHEN a price of zero is entered, THE Course_Form SHALL treat the course as free
6. THE Course_Form SHALL provide select dropdowns for level and language with predefined options
7. THE Course_Form SHALL allow adding and removing multiple requirement items dynamically
8. THE Course_Form SHALL allow adding and removing multiple outcome items dynamically

### Requirement 5: Validate Course Data

**User Story:** As an instructor, I want my course data validated, so that I submit complete and correct information.

#### Acceptance Criteria

1. WHEN an instructor submits the Course_Form, THE System SHALL validate the data using StoreCourseRequest or UpdateCourseRequest
2. IF validation fails, THEN THE Course_Form SHALL display error messages next to the relevant fields
3. THE System SHALL require title, short_description, and description fields
4. THE System SHALL validate that price is a non-negative numeric value
5. THE System SHALL validate that thumbnail uploads are image files with maximum size of 2MB

### Requirement 6: Autosave Course Drafts

**User Story:** As an instructor, I want my course changes automatically saved, so that I don't lose work if I navigate away.

#### Acceptance Criteria

1. WHILE an instructor is editing a course, THE Course_Form SHALL automatically save the draft every 60 seconds
2. WHEN an autosave completes successfully, THE Course_Form SHALL display a timestamp indicating the last save time
3. IF an autosave fails, THEN THE Course_Form SHALL display a warning message to the instructor
4. THE Course_Form SHALL only autosave if at least one field has been modified since the last save

### Requirement 7: Manage Course Publication Status

**User Story:** As an instructor, I want to control my course publication status, so that I can manage when students can access it.

#### Acceptance Criteria

1. THE Course_Form SHALL provide status action buttons: Save Draft, Submit for Review, Publish, and Unpublish
2. WHEN a course is in Draft status, THE Course_Form SHALL display "Save Draft" and "Submit for Review" buttons
3. WHEN a course is Under_Review, THE Course_Form SHALL display only "Save Draft" button
4. WHEN a course is Published, THE Course_Form SHALL display "Save Draft" and "Unpublish" buttons
5. WHEN an instructor clicks a status button, THE Course_Form SHALL update the course status and display a flash message

### Requirement 8: Display Course Curriculum Structure

**User Story:** As an instructor, I want to view my course curriculum, so that I can see the organization of sections and lessons.

#### Acceptance Criteria

1. THE Course_Curriculum SHALL display all sections for the current course in order
2. FOR EACH section, THE Course_Curriculum SHALL display the section title and all lessons within that section
3. FOR EACH lesson, THE Course_Curriculum SHALL display the lesson title, type icon, and duration if applicable
4. THE Course_Curriculum SHALL visually nest lessons under their parent sections
5. THE Course_Curriculum SHALL display an empty state message when no sections exist

### Requirement 9: Manage Sections

**User Story:** As an instructor, I want to add, rename, and delete sections, so that I can organize my course content logically.

#### Acceptance Criteria

1. THE Course_Curriculum SHALL provide an "Add Section" button
2. WHEN an instructor clicks "Add Section", THE Course_Curriculum SHALL create a new section with a default title and focus the title input
3. THE Course_Curriculum SHALL allow inline editing of section titles
4. WHEN an instructor edits a section title, THE Course_Curriculum SHALL save the change on blur or Enter key
5. THE Course_Curriculum SHALL provide a delete button for each section
6. WHEN an instructor deletes a section, THE Course_Curriculum SHALL remove the section and all its lessons and display a flash message

### Requirement 10: Manage Lessons

**User Story:** As an instructor, I want to add, edit, and delete lessons within sections, so that I can build my course content.

#### Acceptance Criteria

1. FOR EACH section, THE Course_Curriculum SHALL provide an "Add Lesson" button
2. WHEN an instructor clicks "Add Lesson", THE Course_Curriculum SHALL display a modal with fields for title and type selection
3. THE Course_Curriculum SHALL support lesson types: video, text, and PDF
4. WHEN an instructor submits the add lesson modal, THE Course_Curriculum SHALL create the lesson and close the modal
5. THE Course_Curriculum SHALL allow inline editing of lesson titles
6. THE Course_Curriculum SHALL provide edit and delete buttons for each lesson
7. WHEN an instructor clicks edit on a lesson, THE Course_Curriculum SHALL navigate to the Lesson_Editor
8. WHEN an instructor deletes a lesson, THE Course_Curriculum SHALL remove the lesson and display a flash message

### Requirement 11: Reorder Sections and Lessons

**User Story:** As an instructor, I want to drag and drop sections and lessons, so that I can easily reorganize my course structure.

#### Acceptance Criteria

1. THE Course_Curriculum SHALL enable drag-and-drop reordering of sections
2. THE Course_Curriculum SHALL enable drag-and-drop reordering of lessons within a section
3. WHEN an instructor drags a section to a new position, THE Course_Curriculum SHALL update the visual order immediately
4. WHEN an instructor drags a lesson to a new position within its section, THE Course_Curriculum SHALL update the visual order immediately
5. WHEN reordering completes, THE Course_Curriculum SHALL persist the new order by sending a PATCH request to the reorder endpoint
6. IF the reorder request fails, THEN THE Course_Curriculum SHALL revert to the previous order and display an error message

### Requirement 12: Edit Video Lessons

**User Story:** As an instructor, I want to add video content to lessons, so that I can provide video-based instruction.

#### Acceptance Criteria

1. WHEN editing a video lesson, THE Lesson_Editor SHALL provide an embed URL input field
2. THE Lesson_Editor SHALL validate that embed URLs are from supported video platforms
3. THE Lesson_Editor SHALL provide an upload option for direct video file uploads
4. WHEN an instructor initiates a video upload, THE Lesson_Editor SHALL request a presigned URL from POST /instructor/lessons/{id}/upload-url
5. WHEN a presigned URL is received, THE Lesson_Editor SHALL upload the video file directly to S3 using the presigned URL
6. WHILE a video upload is in progress, THE Lesson_Editor SHALL display a progress bar
7. WHEN a video upload completes, THE Lesson_Editor SHALL display a success message and update the lesson record

### Requirement 13: Edit Text Lessons

**User Story:** As an instructor, I want to create text-based lessons, so that I can provide written instruction and explanations.

#### Acceptance Criteria

1. WHEN editing a text lesson, THE Lesson_Editor SHALL provide a rich text editor
2. THE Lesson_Editor SHALL support text formatting including bold, italic, lists, headings, and links
3. WHEN an instructor saves a text lesson, THE Lesson_Editor SHALL store the formatted HTML content
4. THE Lesson_Editor SHALL sanitize HTML content to prevent XSS attacks

### Requirement 14: Edit PDF Lessons

**User Story:** As an instructor, I want to upload PDF documents as lessons, so that I can provide downloadable resources.

#### Acceptance Criteria

1. WHEN editing a PDF lesson, THE Lesson_Editor SHALL provide a file upload input
2. THE Lesson_Editor SHALL validate that uploaded files are PDF format with maximum size of 10MB
3. WHEN an instructor uploads a PDF, THE Lesson_Editor SHALL process the upload using Media_Library
4. THE Lesson_Editor SHALL queue the file processing job
5. WHEN a PDF upload completes, THE Lesson_Editor SHALL display the filename and a preview link

### Requirement 15: Configure Lesson Access Settings

**User Story:** As an instructor, I want to control lesson access settings, so that I can offer previews and implement drip content.

#### Acceptance Criteria

1. THE Lesson_Editor SHALL provide a toggle for marking a lesson as a preview lesson
2. WHEN a lesson is marked as preview, THE System SHALL allow access without enrollment
3. THE Lesson_Editor SHALL provide an input field for unlock_after_days
4. WHEN unlock_after_days is set, THE System SHALL restrict lesson access until that many days after enrollment
5. WHEN unlock_after_days is null or zero, THE System SHALL make the lesson immediately available upon enrollment

### Requirement 16: Save Lesson Changes

**User Story:** As an instructor, I want to save my lesson edits, so that my changes are persisted.

#### Acceptance Criteria

1. THE Lesson_Editor SHALL provide a "Save" button
2. WHEN an instructor clicks Save, THE Lesson_Editor SHALL validate and submit the lesson data
3. WHEN a save succeeds, THE Lesson_Editor SHALL display a flash message and return to the Course_Curriculum
4. IF a save fails, THEN THE Lesson_Editor SHALL display validation errors without navigating away
5. THE Lesson_Editor SHALL provide a "Cancel" button that returns to Course_Curriculum without saving

### Requirement 17: Process File Uploads Asynchronously

**User Story:** As an instructor, I want file uploads processed in the background, so that I can continue working without waiting.

#### Acceptance Criteria

1. WHEN a file upload is initiated, THE System SHALL queue the processing job
2. THE System SHALL process thumbnail uploads to generate multiple sizes
3. THE System SHALL process PDF uploads to extract metadata
4. WHEN upload processing completes, THE System SHALL update the associated record with file information
5. IF upload processing fails, THEN THE System SHALL log the error and notify the instructor

### Requirement 18: Display User Feedback

**User Story:** As an instructor, I want to see confirmation messages after actions, so that I know my actions succeeded.

#### Acceptance Criteria

1. WHEN an instructor completes a create, update, or delete action, THE System SHALL display a flash message
2. THE System SHALL display success messages with green styling
3. THE System SHALL display error messages with red styling
4. THE System SHALL automatically dismiss flash messages after 5 seconds
5. THE System SHALL allow manual dismissal of flash messages by clicking a close button

### Requirement 19: Parse and Print Course Data

**User Story:** As a developer, I want to serialize and deserialize course data, so that I can export and import courses.

#### Acceptance Criteria

1. THE System SHALL provide a parser that converts JSON course data into Course objects
2. WHEN valid JSON course data is provided, THE Parser SHALL create a complete Course object with all sections and lessons
3. WHEN invalid JSON course data is provided, THE Parser SHALL return a descriptive error message
4. THE System SHALL provide a pretty printer that formats Course objects into valid JSON
5. FOR ALL valid Course objects, parsing then printing then parsing SHALL produce an equivalent object

