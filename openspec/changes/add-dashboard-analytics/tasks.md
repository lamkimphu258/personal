## Implementation
- [x] Define Blade/Alpine chart layout component with filter controls (week/month/year/custom) and slot for datasets.
- [x] Create dedicated bar chart component for categorical datasets such as Top Foods.
- [x] Extend dashboard controller/view to source current/target weight widgets and arrow status.
- [x] Implement weight trend, calorie-goal adherence, and top-food charts using the shared component.
- [x] Add backend aggregation queries for calorie-goal counts and top 10 foods within selected ranges.
- [x] Seed representative dashboard data (weights, nutrition profile, varied food entries) so each chart renders during manual QA.
- [x] Ensure Tailwind/Vite assets updated and run `npm run build` if needed for manual verification. _(Manual verification performed with Vite dev server; full build not required for local check.)_
- [x] Format touched PHP files with `vendor/bin/pint --dirty`.
