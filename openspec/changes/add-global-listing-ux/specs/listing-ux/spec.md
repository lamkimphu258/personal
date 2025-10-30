## ADDED Requirements

### Requirement: Global Listing UX Standard
All listing pages in the system MUST follow a standard pattern for search, actions, and detail viewing, unless a page’s spec explicitly documents an exception.

#### Scenario: Name search behavior
- WHEN the user enters a search term in the listing search input
- THEN the list filters to items whose names contain the term, case-insensitive
- AND clearing the search restores the full list

#### Scenario: Edit and delete actions
- WHEN viewing the listing
- THEN each item provides Edit and Delete actions accessible directly from the list
- AND Delete prompts for confirmation prior to removal

#### Scenario: Inline detail popup
- WHEN the user clicks an item’s view action (or name, per page design)
- THEN a popup appears with the item’s key details
- AND the popup can be closed via a close control, pressing Escape, or clicking the backdrop

#### Scenario: Applicability
- GIVEN any new or existing listing page (e.g., Food Templates)
- WHEN the page is specified or modified
- THEN the page SHALL conform to the Global Listing UX Standard unless its spec records an explicit exception with rationale

