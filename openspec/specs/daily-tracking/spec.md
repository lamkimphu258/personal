# daily-tracking Specification

## Purpose
TBD - created by archiving change add-daily-tracking-page. Update Purpose after archive.
## Requirements
### Requirement: Progress Section Layout (Wider)
The progress section SHALL span the full content width above the forms and listings to maximise readability.

#### Scenario: Full-width progress section on large screens
- GIVEN I view the Daily Tracking page on a desktop or large screen
- THEN the progress section stretches across the full main content width as a single wide block
- AND the individual metric cards (calories, protein, carbs, fat) arrange in responsive columns within that block.

#### Scenario: Mobile-friendly stacking
- GIVEN I view the Daily Tracking page on a small screen
- THEN the progress section remains at the top and its metric cards stack or wrap responsively without horizontal scrolling.

### Requirement: Daily Weight Entry
The system SHALL allow recording a single weight entry for the selected date.

#### Scenario: Create or update weight for the day
- WHEN the user submits the weight form for the selected date
- THEN the system saves the weight in kilograms for that date
- AND subsequent submissions for the same date update the existing entry

### Requirement: Food Entry Form
The system SHALL allow creating food entries for the selected date with name and nutrition values.

#### Scenario: Add food entry
- WHEN the user submits the food form with name, protein (g), carbs (g), fat (g), and calories
- THEN the system saves the entry for the selected date
- AND the entry appears in the day’s food list

#### Scenario: Validation
- WHEN required fields are missing or invalid (negative values, non-numeric macros/calories)
- THEN the system rejects the submission with validation messages

### Requirement: Food Entries Listing
The system SHALL list all food entries for the selected date with a totals summary.

#### Scenario: List entries and totals
- WHEN the selected date has food entries
- THEN the page shows each entry with name, protein (g), carbs (g), fat (g), and calories
- AND shows a totals row for protein, carbs, fat, and calories

#### Scenario: Edit or delete an entry
- WHEN the user views a food entry in the list
- THEN edit and delete actions are available for that entry
- AND deleting prompts for confirmation before removal
- AND editing loads the entry so it can be updated without creating a duplicate

#### Scenario: Empty state
- WHEN the selected date has no food entries
- THEN the page shows an appropriate empty-state message

### Requirement: Historical Persistence
The system SHALL persist daily weight and food entries historically for later analytics.

#### Scenario: Navigate across days without losing data
- WHEN the user records data on a date and later navigates to another date
- THEN data for previous dates remains intact and is retrievable by selecting that date again

### Requirement: Template Selection and Linking (Daily Integration)
The system SHALL support selecting a Food Template to prefill the food entry form and persist a link to the template for that day’s entry.

#### Scenario: Select and prefill
- GIVEN Food Templates exist
- WHEN the user selects a template on the Daily page
- THEN the form fields for calories, protein (g), carbs (g), and fat (g) auto-fill from the template

#### Scenario: Persist template link
- WHEN the user saves the pre-filled entry
- THEN the food entry stores a template reference (template id)
- AND the entry is marked as template-based for that day

#### Scenario: Template link in listing
- WHEN listing food entries for the day
- THEN template-based entries display the template name as a clickable link
- AND clicking shows a popup with the template’s calories and macros

