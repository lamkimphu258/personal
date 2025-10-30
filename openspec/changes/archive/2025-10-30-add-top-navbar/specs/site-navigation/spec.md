## ADDED Requirements
### Requirement: Present Site Navigation
The system MUST display a consistent top navbar at the top of each page so I can move between sections without typing URLs.

#### Scenario: Navbar appears on core pages
- **GIVEN** I open the dashboard or profile page
- **THEN** a top navbar is visible before the page-specific content
- **AND** the navbar shows the app name that links back to the home page
- **AND** the navbar lists links labeled Home and Profile that route to `/` and the profile editor respectively
- **AND** the link for the current page is visually highlighted so I know where I am.

#### Scenario: Navigate using the navbar
- **GIVEN** I am on any page with the navbar
- **WHEN** I click the Home or Profile link
- **THEN** I am taken to the matching page without reloading a blank screen or losing the navbar.
