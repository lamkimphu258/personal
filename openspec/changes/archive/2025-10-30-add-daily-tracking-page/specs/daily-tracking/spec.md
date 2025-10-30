## ADDED Requirements

### Requirement: Daily Tracking Page
The system SHALL provide a Daily Tracking page scoped to a selected date with simple navigation.

#### Scenario: Default to today
- WHEN the user visits the Daily Tracking page
- THEN the selected date defaults to today
- AND the page displays widgets, forms, and entries for that date

#### Scenario: Navigate to previous/next day
- WHEN the user clicks Previous or Next
- THEN the selected date changes accordingly
- AND the page refreshes to show data for the new date

#### Scenario: Jump via date picker
- WHEN the user changes the date using a date input
- THEN the page updates to show data for the selected date

### Requirement: Progress Widget (Calories and Macros)
The system SHALL show a progress widget for the selected date summarizing daily calories and macronutrients versus targets.

#### Scenario: With profile targets available
- GIVEN a Nutrition Profile with daily targets exists
- WHEN the user views the selected date
- THEN the widget shows totals for calories, protein, fat, and carbohydrates consumed
- AND shows the corresponding daily targets
- AND shows remaining values (target minus consumed, minimum 0)

#### Scenario: Without profile targets
- GIVEN no Nutrition Profile exists
- WHEN the user views the selected date
- THEN the widget shows consumed totals only
- AND shows guidance to set up a profile to enable targets

## ADDED Requirements

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
