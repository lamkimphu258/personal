## Why
- I want to store my current body metrics and goals so the app can personalise calorie and macro targets for weight loss.
- Today there is no page to review or update these details, so everything has to be calculated manually.

## What Changes
- Add a “Profile” page that shows a form with age, sex, weight, height, activity level, goal weight, and desired weight loss per week.
- When I save the form, calculate a safe daily calorie target plus macro guidance that emphasises protein and fibre while keeping carbohydrates lower.
- Persist the profile and computed targets so the page can show the current values the next time it is opened.

## Impact
- Introduces a database record to store profile inputs and derived nutrition targets for the single user.
- Adds a controller, route, and Blade view responsible for the profile form and calculated results.
- Brings in a calculation helper/service to encapsulate BMR, activity, and macro formulas so they can be reused elsewhere later.
