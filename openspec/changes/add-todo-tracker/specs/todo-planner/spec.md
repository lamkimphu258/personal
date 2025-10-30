## ADDED Requirements
### Requirement: Todo Page Daily Overview
The Todo page SHALL focus on a single selected date, showing summary metrics before the task list.

#### Scenario: View today by default
- **WHEN** I open the Todo page
- **THEN** the page defaults to the current date
- **AND** a summary widget row displays three cards for Total Tasks, Completed Tasks, and Incomplete Tasks for that date.

#### Scenario: Switch the active date
- **WHEN** I choose a different date
- **THEN** the summary widget counts and the task listing refresh to reflect tasks scheduled for the newly selected date
- **AND** the selected date remains sticky when I create or edit tasks until I change it again.

### Requirement: Task Creation Form
The system SHALL provide a create form that captures task details and recurrence preferences for the active date.

#### Scenario: Create a one-time task
- **WHEN** I submit the form with a title, priority, due date, and no recurrence
- **THEN** the task saves with completion state set to incomplete for the due date
- **AND** it appears immediately in the listing for that date.

#### Scenario: Priority and validation rules
- **WHEN** I submit the form
- **THEN** the title is required (max 120 characters), due date must be on or after the selected date, and priority MUST be one of `low`, `medium`, or `high`
- **AND** invalid input redisplays the form with inline validation errors without losing entered values.

### Requirement: Task Listing and Inline Management
The Todo page SHALL reuse the Global Listing Component to manage tasks scheduled for the selected date.

#### Scenario: Render tasks for a date
- **WHEN** the page loads tasks for the active date
- **THEN** each task row shows title, priority badge, due date, and completion status, leveraging the default columns plus any necessary component slots.

#### Scenario: Toggle completion inline
- **WHEN** I mark a task as complete or incomplete from the listing
- **THEN** the task’s completion state updates without a full page refresh
- **AND** the summary widget counts update to reflect the new totals.

#### Scenario: Edit or delete a task
- **WHEN** I choose Edit or Delete from the listing actions
- **THEN** Edit opens the inline form populated with the task data (including recurrence settings)
- **AND** Delete requires confirmation before removing the task and any future occurrences tied to it.

### Requirement: Recurring Task Scheduling
The system SHALL support recurring tasks that reappear automatically on applicable dates with independent completion tracking per occurrence.

#### Scenario: Repeat daily
- **WHEN** I create a task and select the `Repeat daily` option
- **THEN** the system generates occurrences for every subsequent date after the initial due date
- **AND** marking one day’s occurrence complete does not affect future occurrences.

#### Scenario: Repeat on selected days
- **WHEN** I choose specific days of the week (e.g., Monday and Thursday)
- **THEN** the task appears on each chosen day starting from the next matching date on or after the due date
- **AND** each occurrence defaults to incomplete until I mark it complete for that day.

#### Scenario: Combine weekday and weekend repeats
- **WHEN** I select a mix of weekday and weekend options (e.g., Monday, Wednesday, and Saturday)
- **THEN** the task schedules occurrences on every chosen day without restriction
- **AND** each generated occurrence tracks completion independently just like other recurrence patterns.

#### Scenario: Stop or change recurrence
- **WHEN** I edit a recurring task to remove recurrence or change the weekday selection
- **THEN** existing occurrences in the past remain untouched for historical accuracy
- **AND** upcoming occurrences regenerate according to the new settings.

### Requirement: Task Detail Updates
The system SHALL allow editing core fields while preserving past completion history.

#### Scenario: Update title, priority, or due date
- **WHEN** I edit a task’s title, priority, or due date
- **THEN** occurrences for dates on or after the edited due date update with the new details
- **AND** completion history before the update is preserved.

#### Scenario: Deleting completed tasks
- **WHEN** I delete a task that has completed occurrences
- **THEN** the system removes future occurrences but keeps historical records needed for the daily summary counts on past dates
- **AND** the counts for those past dates remain accurate when revisited.
