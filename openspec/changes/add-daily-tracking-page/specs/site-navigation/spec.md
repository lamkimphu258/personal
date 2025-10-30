## MODIFIED Requirements

### Requirement: Present Site Navigation
The navbar MUST include a link labeled "Tracking" that routes to the Daily Tracking page.

#### Scenario: Navbar shows Tracking link
- GIVEN I open any page with the navbar
- THEN the navbar lists Home, Profile, and Tracking
- AND the Tracking link routes to `/tracking`
- AND the active page link is visually highlighted.

#### Scenario: Navigate to Daily Tracking via navbar
- GIVEN I am on any page with the navbar
- WHEN I click the Tracking link
- THEN I am taken to the Daily Tracking page without losing the navbar.

