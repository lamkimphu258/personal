## 1. Implementation
- [x] 1.1 Create migrations and models
  - [x] 1.1.1 `daily_weights` (date, weight_kg)
  - [x] 1.1.2 `food_entries` (date, name, protein_g, carbs_g, fat_g, calories)
- [x] 1.2 Add Eloquent relationships/scopes for date filtering and totals
- [x] 1.3 Add controller and routes for Daily Tracking page
- [x] 1.4 Build Blade view
  - [x] 1.4.1 Date selector (prev/next, date input)
  - [x] 1.4.2 Progress widget (uses NutritionProfile targets when present)
  - [x] 1.4.3 Weight form (create/update for selected date)
  - [x] 1.4.4 Food entry form (create for selected date)
  - [x] 1.4.5 Food list for the selected date with per-day totals
- [x] 1.5 Lightweight date navigation UX (server-driven prev/next; date input submit)
- [x] 1.6 Validation via Form Request classes (weight, food entry)
- [x] 1.8 Add navbar link to Tracking page
- [x] 1.9 Add edit/delete actions to food entries listing and hook into controller

## 2. Notes
- No browser or backend tests per project conventions; verify via browser.
- Ensure formatting: `vendor/bin/pint --dirty` after code changes.
