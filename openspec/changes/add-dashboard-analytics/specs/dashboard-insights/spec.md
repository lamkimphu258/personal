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
The dashboard SHALL provide shared time-range controls that update every chart in sync.

#### Scenario: Quick range presets
- **WHEN** I click the Week, Month, or Year preset
- **THEN** all dashboard charts refresh to cover the last 7, 30, or 365 days respectively
- **AND** the selected preset is visually highlighted.

#### Scenario: Custom date span
- **WHEN** I choose a custom start and end date
- **THEN** each chart rerenders using only data within that inclusive range
- **AND** the preset highlight clears while the custom range summary appears beside the filters.

### Requirement: Weight Trend Visualisation
The dashboard SHALL visualise weight changes over time using the global chart component.

#### Scenario: Plot daily weights
- **GIVEN** weight entries exist within the selected range
- **THEN** the chart renders a line graph with date labels on the X-axis and weight (kg) on the Y-axis using the shared chart component
- **AND** missing days are omitted rather than zero-filled, preserving accurate trends.

#### Scenario: Empty state messaging
- **WHEN** no weight entries fall inside the selected window
- **THEN** the chart area shows an empty-state message telling me to record weights on the Daily Tracking page.

### Requirement: Calorie Goal Adherence Summary
The dashboard SHALL surface how consistently calorie goals are met.

#### Scenario: Show goal hit counts
- **WHEN** I view the dashboard for a given range
- **THEN** the chart displays two values using the shared component: number of days where total calories were at or below target, and days exceeding target
- **AND** totals come from food entries grouped by day and compared to the active calorie target for that date.

#### Scenario: Handle missing calorie target
- **WHEN** no calorie target is available for the period (e.g., no nutrition profile saved)
- **THEN** the section explains that calorie goals require completing the profile and does not render misleading data.

### Requirement: Top Foods Frequency
The dashboard SHALL highlight the most frequently consumed foods in the selected period.

#### Scenario: Display top 10 foods
- **WHEN** there are food entries in the range
- **THEN** the chart lists the ten foods with the highest entry counts sorted descending, rendered via the shared component as a horizontal bar chart showing servings per food
- **AND** entries linked to templates use the stored entry name so manual edits still reflect accurately.

#### Scenario: Empty state without food entries
- **WHEN** no food entries exist within the range
- **THEN** the top foods section shows an empty-state message suggesting creating meals on the Daily Tracking page.
