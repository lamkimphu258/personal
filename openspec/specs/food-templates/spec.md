# food-templates Specification

## Purpose
TBD - created by archiving change add-food-templates. Update Purpose after archive.
## Requirements
### Requirement: Food Templates Management
The system SHALL provide a Food Templates page to manage reusable food definitions.

#### Scenario: Create template
- WHEN the user submits a template with name, description (optional), calories, protein (g), carbs (g), fat (g)
- THEN the system saves the template for later reuse

#### Scenario: List templates
- WHEN the user visits the Food Templates page
- THEN the page lists existing templates with their name and key nutrient info

#### Scenario: View template detail
- WHEN the user clicks a template in the list
- THEN the system navigates to a template detail view
- AND the page shows the template’s name, description, calories, protein (g), carbs (g), and fat (g)

#### Scenario: Search by name
- GIVEN templates exist with various names
- WHEN the user searches by a name or partial name
- THEN the list filters to templates whose names contain the search term (case-insensitive)

#### Scenario: Validation
- WHEN required fields are missing or have invalid values (negative or non-numeric)
- THEN the system rejects the submission with validation messages

### Requirement: Inline Detail Popup From List
The system SHALL show an inline popup on the Food Templates list when clicking a template entry.

#### Scenario: Open popup from list
- WHEN the user clicks a template name in the list
- THEN a popup appears showing the template’s name, description (if any), calories, protein (g), carbs (g), and fat (g)

#### Scenario: Close popup
- WHEN the popup is open
- THEN the user can close it via a close control, pressing Escape, or clicking the backdrop

### Requirement: Template Integration with Daily Page
The system SHALL allow selecting a template on the Daily Tracking page to prefill a food entry and link the created entry to its template.

#### Scenario: Prefill from template
- GIVEN at least one Food Template exists
- WHEN the user selects a template on the Daily page
- THEN the food entry form pre-fills calories, protein (g), carbs (g), and fat (g)

#### Scenario: Save with template link
- WHEN the user submits the pre-filled food entry
- THEN the system saves the food entry for the selected date
- AND records a reference to the Food Template (template link)

#### Scenario: Distinguish custom vs template entries
- WHEN displaying the day’s food entries
- THEN entries created from a template indicate the template name as a clickable link
- AND custom entries (no template) do not display a template link

### Requirement: Template Detail Popup
The system SHALL display a popup with details when clicking the template link from a day’s food entry.

#### Scenario: Show template details
- WHEN the user clicks a template link in the food list
- THEN a popup appears showing the template’s name, description (if any), and calories/macros
- AND closing the popup returns to the list view state

