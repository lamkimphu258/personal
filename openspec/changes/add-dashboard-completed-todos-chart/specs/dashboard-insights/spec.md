## MODIFIED Requirements
### Requirement: Completed Todo Trend Chart
The dashboard SHALL include a line chart that tracks the number of completed todo occurrences per day across the active range.

#### Scenario: Plot daily completed counts
- **WHEN** the dashboard loads with todo data inside the selected date range
- **THEN** the chart renders a line graph using the reusable line chart component with each point labelled by date
- **AND** the Y-axis reflects the count of completed occurrences for that day (0+)
- **AND** range presets or custom start/end dates update the dataset in sync with other charts.

#### Scenario: Empty state without completed todos
- **WHEN** no completed todos exist for the selected range
- **THEN** the chart area displays an explanatory empty-state message
- **AND** the message links or directs me to the Todo page so I can begin completing tasks.
