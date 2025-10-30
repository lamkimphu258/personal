## ADDED Requirements

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

