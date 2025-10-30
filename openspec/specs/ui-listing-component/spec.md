# ui-listing-component Specification

## Purpose
TBD - created by archiving change add-global-listing-component. Update Purpose after archive.
## Requirements
### Requirement: Global Listing Component
The system SHALL provide a reusable Listing Component for rendering listing pages. This component encapsulates name search, item actions, and inline popups for viewing and editing entries.

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
- THEN Edit and Delete actions are available when the page supplies the corresponding configuration
- AND Delete requires confirmation prior to removal
- AND clicking Edit opens the inline popup instead of navigating away

#### Scenario: Inline detail popup contract
- WHEN the user clicks an item’s view action (or name, if configured)
- THEN the component opens an inline popup showing key details for that item
- AND the popup can be closed via close control, Escape key, or clicking the backdrop
- AND the detail popup remains available even when inline editing is enabled

#### Scenario: Inline edit popup configuration
- GIVEN the page provides edit configuration with a form action, HTTP method, and field definitions
- WHEN the user opens the Edit action for an item
- THEN the inline popup switches to edit mode and renders a form inside the modal
- AND each configured field is prefilled from the item using the provided keys
- AND the form includes the item identifier so the backend can persist changes

#### Scenario: Inline edit submission
- WHEN the inline edit form is submitted
- THEN it submits to the configured action using the configured HTTP method with CSRF protection
- AND the popup closes after submission or when cancelled via the close controls
- AND closing the popup resets the form state so another item starts with fresh values

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

