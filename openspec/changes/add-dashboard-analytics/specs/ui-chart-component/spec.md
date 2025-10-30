## ADDED Requirements
### Requirement: Reusable Line Chart Component
The system SHALL provide a reusable Blade component that renders consistent line chart layouts with shared headers, controls, and empty states.

#### Scenario: Render line chart with title and actions
- **WHEN** a view includes the line chart component with a title, optional description, and action slot
- **THEN** the component renders a bordered, rounded section with the title/description, an actions area on the right for filters, and a `<canvas>` element configured for a line chart.

#### Scenario: Support loading and empty states
- **WHEN** the parent view passes a loading flag or empty message
- **THEN** the component shows a subdued loading shimmer or empty message instead of the chart canvas so consuming pages keep behaviour consistent.

### Requirement: Reusable Bar Chart Component
The system SHALL provide a reusable Blade component purpose-built for horizontal bar charts to visualise categorical datasets.

#### Scenario: Render bar chart with title and actions
- **WHEN** a view includes the bar chart component with a title, optional description, and action slot
- **THEN** the component renders a bordered, rounded section with the title/description, an actions area on the right for filters, and a `<canvas>` element configured for a horizontal bar chart.

#### Scenario: Support loading and empty states
- **WHEN** the parent view passes a loading flag or empty message
- **THEN** the component shows a subdued loading shimmer or empty message instead of the bar chart canvas so consuming pages keep behaviour consistent.

### Requirement: Line Chart Data API
The component SHALL expose stable data attributes so shared JavaScript can refresh chart datasets in sync.

#### Scenario: Identify chart instances
- **WHEN** a line chart component is rendered
- **THEN** it includes `data-line-chart`, `data-chart-id`, and `data-chart-key` attributes so the dashboard manager can target the canvas for updates.

#### Scenario: Toggle loading and messages programmatically
- **WHEN** the dashboard manager toggles the `data-line-chart-loading` or `data-line-chart-message` overlays
- **THEN** the component displays loading or informational states without requiring Blade markup changes.

### Requirement: Bar Chart Data API
The bar chart component SHALL expose stable data attributes so shared JavaScript can refresh bar datasets in sync.

#### Scenario: Identify bar chart instances
- **WHEN** a bar chart component is rendered
- **THEN** it includes `data-bar-chart`, `data-chart-id`, and `data-chart-key` attributes so the dashboard manager can target the canvas for updates.

#### Scenario: Toggle bar loading and messages programmatically
- **WHEN** the dashboard manager toggles the `data-bar-chart-loading` or `data-bar-chart-message` overlays
- **THEN** the component displays loading or informational states without requiring Blade markup changes.
