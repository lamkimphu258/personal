## Why
- Surface adherence to daily todos alongside existing health metrics so productivity trends are visible from the dashboard.
- Highlight streaks or gaps in completing tasks, encouraging consistency with daily routines.
- Reuse the existing dashboard chart patterns to keep the experience cohesive while extending data coverage to the new Todo system.

## What Changes
- Add a dashboard requirement for a line chart that shows daily completed task counts over the selected date range.
- Specify data sourcing rules that aggregate todo occurrences per day and align with existing range filters.
- Cover empty states when no todo data exists yet, guiding the user toward the Todo page for setup.

## Impact
- Requires querying task occurrences joined with completion status and integrating them into the dashboard data endpoint.
- Updates dashboard Blade/JS to render an additional line chart alongside existing widgets.
- Manual verification needed for various ranges (week/month/custom) and dates with/without todo data; no new dependencies expected.
