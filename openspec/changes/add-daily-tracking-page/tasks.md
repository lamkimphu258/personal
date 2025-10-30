## 1. Implementation
- [ ] 1.1 Create migrations and models
  - [ ] 1.1.1 `daily_weights` (date, weight_kg)
  - [ ] 1.1.2 `food_entries` (date, name, protein_g, carbs_g, fat_g, calories)
- [ ] 1.2 Add Eloquent relationships/scopes for date filtering and totals
- [ ] 1.3 Add controller and routes for Daily Tracking page
- [ ] 1.4 Build Blade view
  - [ ] 1.4.1 Date selector (prev/next, date input)
  - [ ] 1.4.2 Progress widget (uses NutritionProfile targets when present)
  - [ ] 1.4.3 Weight form (create/update for selected date)
  - [ ] 1.4.4 Food entry form (create for selected date)
  - [ ] 1.4.5 Food list for the selected date with per-day totals
- [ ] 1.5 Lightweight Alpine.js for date navigation and form UX
- [ ] 1.6 Validation via Form Request classes (weight, food entry)
- [ ] 1.7 Manual validation instructions (no automated tests per project conventions)

## 2. Validation
- [ ] 2.1 Confirm weight saves and persists per date
- [ ] 2.2 Confirm food entries save and list for selected date
- [ ] 2.3 Confirm totals and progress widget calculations match inputs
- [ ] 2.4 Confirm date navigation shows different days independently
- [ ] 2.5 Confirm behavior when Nutrition Profile targets are absent (fallback copy)

## 3. Notes
- No browser or backend tests per project conventions; verify via browser.
- Ensure formatting: `vendor/bin/pint --dirty` after code changes.
