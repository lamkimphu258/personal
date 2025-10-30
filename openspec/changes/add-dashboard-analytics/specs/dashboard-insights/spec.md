## ADDED Requirements
### Requirement: Dashboard Summary Widgets
The dashboard SHALL display weight summary widgets above the charts so current status is immediately visible.

#### Scenario: Show current weight with trend icon
- **GIVEN** at least one daily weight entry exists
- **WHEN** I load the dashboard
- **THEN** the first widget shows the most recent weight in kilograms
- **AND** an arrow indicator reflects the delta versus the previous recorded weight (up for gain, down for loss, flat when unchanged)
- **AND** when no prior entry exists the arrow is neutral with "no previous data" copy.

#### Scenario: Show target weight alongside
- **GIVEN** a nutrition profile exists
- **WHEN** I load the dashboard
- **THEN** a second widget shows the goal weight in kilograms pulled from the profile
- **AND** if no profile is saved the widget prompts me to complete the profile page instead of showing stale data.

### Requirement: Unified Date Filters
The dashboard SHALL provide shared time-range controls that update every line chart in sync.

#### Scenario: Quick range presets
- **WHEN** I click the Week, Month, or Year preset
- **THEN** all dashboard charts refresh to cover the last 7, 30, or 365 days respectively
- **AND** the selected preset is visually highlighted.

#### Scenario: Custom date span
- **WHEN** I choose a custom start and end date
- **THEN** each chart rerenders using only data within that inclusive range
- **AND** the preset highlight clears while the custom range summary appears beside the filters.

### Requirement: Weight Trend Line Chart
The dashboard SHALL visualise weight changes over time using the reusable line chart component.

#### Scenario: Plot daily weights
- **GIVEN** weight entries exist within the selected range
- **THEN** the chart renders a line graph with date labels on the X-axis and weight (kg) on the Y-axis using the reusable line chart component
- **AND** missing days are omitted rather than zero-filled, preserving accurate trends.
 - **AND** seed data provides at least three sample weight points across different dates so the chart can be validated visually during development.

#### Scenario: Empty state messaging
- **WHEN** no weight entries fall inside the selected window
- **THEN** the chart area shows an empty-state message telling me to record weights on the Daily Tracking page.

### Requirement: Calorie Goal Adherence Line Chart
The dashboard SHALL surface how consistently calorie goals are met using a line chart.

#### Scenario: Plot daily goal adherence
- **WHEN** I view the dashboard for a given range
- **THEN** the line chart plots each day with a value of `1` when total calories were at or below target and `0` when the target was exceeded, using the reusable line chart component
- **AND** the dataset comes from food entries grouped by day and compared to the active calorie target for that date.
 - **AND** seed data includes at least five days of food entries with varying totals to demonstrate both success (`1`) and failure (`0`) values on the chart.

#### Scenario: Handle missing calorie target
- **WHEN** no calorie target is available for the period (e.g., no nutrition profile saved)
- **THEN** the section explains that calorie goals require completing the profile and does not render misleading data.

### Requirement: Top Foods Bar Chart
The dashboard SHALL highlight the most frequently consumed foods in the selected period using a bar chart variant of the shared component.

#### Scenario: Display top 10 foods as bars
- **WHEN** there are food entries in the range
- **THEN** the chart renders the ten foods with the highest entry counts sorted descending, using a horizontal bar chart with food names as labels and servings per food on the axis
- **AND** entries linked to templates use the stored entry name so manual edits still reflect accurately.
 - **AND** seed data supplies at least ten distinct food names with varying frequencies so the chart ordering can be inspected visually.

#### Scenario: Empty state without food entries
- **WHEN** no food entries exist within the range
- **THEN** the top foods section shows an empty-state message suggesting creating meals on the Daily Tracking page.
