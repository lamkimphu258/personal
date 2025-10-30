## Why
Tracking daily intake and weight with a simple flow enables consistent progress monitoring and future charting. The app already computes daily calorie and macro targets from the Nutrition Profile; a daily page should let the user log weight and foods per day, visualize progress against those targets, and persist history for later analysis.

## What Changes
- Add a Daily Tracking page scoped to a selected date (defaults to today) with simple date navigation.
- Show a progress widget for the selected date: calories and macronutrients vs daily targets (when targets exist), with totals and remaining values.
- Add a form to record a single weight entry for the selected date (create/update behavior).
- Add a form to record food entries (name, protein g, carbs g, fat g, calories) for the selected date.
- List all food entries for the selected date with per-day totals.
- Persist all entries historically to support charts/graphs on the dashboard.

## Impact
- Affected specs: daily-tracking (new capability)
- Affected code: routes/web.php, controllers for tracking, Eloquent models and migrations for daily weight and food entries, Blade views for the daily page, small Alpine.js for date switching.
