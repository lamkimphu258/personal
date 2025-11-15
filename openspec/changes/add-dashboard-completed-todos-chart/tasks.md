## Implementation
- [x] Extend dashboard data query/service to aggregate completed todo occurrences per day for the active range.
- [x] Update dashboard controller/API response to expose the todo completion trend dataset with labels usable by the chart component.
- [x] Render a new line chart section on the dashboard for "Completed Tasks" using the shared chart layout and handle empty-state messaging.
- [x] Manually verify presets (week/month/year) and custom ranges with/without todo data to ensure counts, tooltips, and summaries behave as expected.
- [x] Run `vendor/bin/pint --dirty` on touched PHP files before finishing.
