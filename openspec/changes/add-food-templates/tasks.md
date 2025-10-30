## 1. Implementation
- [ ] 1.1 Create `food_templates` table
  - [ ] 1.1.1 Columns: id, name (string), description (text nullable), calories (integer), protein_g (integer), carbs_g (integer), fat_g (integer), timestamps
- [ ] 1.2 Add `FoodTemplate` model with casts and fillable
- [ ] 1.3 Add routes and controller actions
  - [ ] 1.3.1 `GET /food-templates` index + search by `?q=` + create form on one page (minimal)
  - [ ] 1.3.2 `POST /food-templates` store
  - [ ] 1.3.3 `GET /food-templates/{template}` show detail view
  - [ ] 1.3.4 Optional: edit/update/delete (can be follow-up; keep minimal now)
- [ ] 1.4 Add `FoodTemplateRequest` for validation (name, numeric macros/calories, non-negative)
- [ ] 1.5 Build Blade view for index/create (list existing templates + create form)
  - [ ] 1.5.1 Add search input (submits with GET `q`)
  - [ ] 1.5.2 Add inline popup on index to show template details (open via click, close via button/Escape/backdrop)
- [ ] 1.6 Daily page integration
  - [ ] 1.6.1 Add nullable `food_template_id` FK to `food_entries` (or add when creating `food_entries` if not yet implemented)
  - [ ] 1.6.2 Add template selector on Daily page; selecting pre-fills calories/macros (Alpine.js)
  - [ ] 1.6.3 Save `food_template_id` when submitted via template
  - [ ] 1.6.4 In day list, render template name as link to show popup with template details (calories/macros)
- [ ] 1.7 Run `vendor/bin/pint --dirty` to format

## 2. Validation (manual)
- [ ] 2.1 Create a template; verify it appears in list
- [ ] 2.2 On Daily page, select template; confirm form pre-fills nutrients and calories
- [ ] 2.3 Submit a food entry with a selected template; confirm `food_template_id` is saved
- [ ] 2.4 In listing, template name links to a popup with correct data
- [ ] 2.5 Submit a custom (no template) entry; confirm no template link appears

## 3. Notes
- Keep UI minimal (single page list + create form) per simplicity-first guidance; can add edit/delete later.
- Daily integration depends on the Daily Tracking page change; implement in that flow once approved.
