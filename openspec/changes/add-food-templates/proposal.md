## Why
Food Templates speed up daily logging by letting you reuse common meals/snacks with consistent calories and macros. They also allow linking daily entries back to their source to understand which entries were custom vs. template-based.

## What Changes
- Introduce a Food Templates page to create and manage templates with: name, description, calories, protein (g), carbs (g), fat (g).
- Integrate template selection on the Daily page to prefill food entry fields automatically.
- Persist a link between a food entry and its template (nullable) to distinguish custom vs. template-based entries.
- In the day’s food list, if an entry came from a template, display the template name as a link; clicking shows a small popup with the template’s calories and macros.

## Impact
- Affected specs: food-templates (new), daily-tracking (add integration requirements)
- Affected code: new model + migration + controller + views for templates; updates to daily page controller/view and food entries schema to add `food_template_id`.
