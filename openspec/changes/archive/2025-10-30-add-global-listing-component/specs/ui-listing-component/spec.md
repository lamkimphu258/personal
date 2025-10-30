## ADDED Requirements

### Requirement: Global Listing Component
The system SHALL provide a reusable Listing Component for rendering listing pages. This component encapsulates name search, item actions, and an inline detail popup.

#### Scenario: Inputs and rendering
- GIVEN a collection of items with at least `id` and `name`
- WHEN the Listing Component is rendered with items and optional configuration
- THEN it displays items in a list/table form with each item’s name

#### Scenario: Name search integration
- WHEN a search term is entered into the component’s search input
- THEN the displayed items filter to those whose names contain the term, case-insensitive
- AND clearing the search restores the full set

#### Scenario: Item actions
- WHEN the component renders an item
- THEN Edit and Delete actions are available
- AND Delete requires confirmation prior to removal

#### Scenario: Inline detail popup contract
- WHEN the user clicks an item’s view action (or name, if configured)
- THEN the component opens an inline popup showing key details for that item
- AND the popup can be closed via close control, Escape key, or clicking the backdrop

#### Scenario: Extensibility
- WHEN a page needs additional filters or columns
- THEN the component supports additional slots/props to extend displayed columns and filters without duplicating core behaviors

#### Scenario: Default latest-first sorting
- WHEN the component renders items without an explicit sort provided by the page
- THEN items are ordered by most recent first by default
- AND "most recent" uses a date-like field (e.g., `created_at` or domain `date`) in descending order

### Requirement: Default Usage
All listing pages SHALL use the Global Listing Component by default unless the page’s spec explicitly documents an exception with rationale.

#### Scenario: Mandated usage
- GIVEN any listing page in the system
- WHEN it is implemented or updated
- THEN it MUST be rendered using the Global Listing Component by default
- UNLESS an approved exception is recorded in the page’s spec
