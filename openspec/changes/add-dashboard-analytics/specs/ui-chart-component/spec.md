## ADDED Requirements
### Requirement: Reusable Chart Panel Component
The system SHALL provide a reusable Blade component wrapping chart content with consistent headers, controls, and empty states.

#### Scenario: Render chart with title and actions
- **WHEN** a view includes the chart panel component with a title, optional description, and action slot
- **THEN** the component renders a bordered, rounded section with the title/description, an actions area on the right for filters, and a content slot for the chart canvas.

#### Scenario: Support loading and empty states
- **WHEN** the parent view passes a loading flag or empty message
- **THEN** the component shows a subdued loading shimmer or empty message instead of the chart slot so consuming pages keep behaviour consistent.

### Requirement: Filter Event API
The component SHALL expose Alpine.js hooks so shared date filters can drive multiple chart instances.

#### Scenario: React to global date range updates
- **WHEN** the page dispatches a `chart-range-changed` event with a payload containing `start` and `end` ISO dates plus `preset`
- **THEN** each chart panel initialises Alpine state with that payload and re-emits a `chart-refresh` event to its consumer so datasets can reload.

#### Scenario: Allow per-chart configuration
- **WHEN** a chart panel is initialised with a config object (e.g., metric name or endpoint)
- **THEN** the component keeps the config stable across refreshes and passes it back with `chart-refresh` so individual charts know how to fetch their data.
