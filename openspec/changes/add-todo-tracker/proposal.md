## Why
- Introduce a central place to manage daily chores and routines alongside the existing health tracking flows.
- Let recurring chores surface automatically so nothing is forgotten on its scheduled day.
- Provide clear progress feedback per day so I can see how many tasks remain at a glance.

## What Changes
- Define a Todo page with a daily-focused layout, summary widget row, task creation form, and listing using the global listing component.
- Specify task records with title, priority, due date, completion state, and optional recurrence rules (daily or selected weekdays).
- Describe behaviours for generating daily task occurrences from recurring definitions and updating the top-level metrics widget.

## Impact
- Requires new database tables/models for tasks plus recurrence support and completion tracking per day.
- Adds new Blade views, routes, and controller logic to power the Todo page and CRUD interactions.
- Introduces UI work (forms, inline editing, summary widgets) and manual verification across different recurrence patterns; no third-party dependencies expected.
