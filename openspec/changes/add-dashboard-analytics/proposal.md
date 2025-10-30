## Why
- Give the dashboard a meaningful overview of weight progress and nutrition adherence instead of a placeholder page.
- Surface historical tracking data in visual form so trends and problem areas are obvious at a glance.
- Provide reusable chart primitives so future pages can share filtering and rendering behaviours without duplication.

## What Changes
- Add dashboard requirements for weight/goal widgets, time-range filters, and three stacked chart sections (weight trend, calorie-goal adherence, top foods).
- Introduce a reusable chart layout component with built-in controls for predefined ranges (week, month, year) and custom date spans.
- Specify data aggregation rules for calorie-goal hit counts and food frequency to drive the new visualisations.

## Impact
- Frontend implementation will require Blade + Alpine.js updates as well as Tailwind styling and Vite asset changes.
- Backend queries must aggregate weight, calorie totals, and food frequencies across arbitrary ranges; expect additional endpoints or controller logic.
- No new third-party dependencies anticipated; ensure existing seed data is sufficient for manual verification.
