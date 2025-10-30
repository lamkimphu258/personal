## ADDED Requirements
### Requirement: Maintain Weight Loss Profile
The system MUST store a single set of weight loss inputs and let me review/update them from a profile page.

#### Scenario: View profile page
- **GIVEN** I navigate to the profile page
- **THEN** I see fields for age (years), sex (female or male), current weight (kg), goal weight (kg), height (cm), activity level, and desired weight loss per week (kg)
- **AND** the weekly weight loss field presents preset options of 0.25 kg, 0.5 kg, 0.75 kg, and 1.0 kg so I can pick a safe rate.
- **AND** each field is pre-filled with the last saved values or sensible defaults if nothing was saved.

#### Scenario: Validate profile inputs
- **GIVEN** I submit the profile form
- **THEN** the app rejects values outside the safe ranges:
  - age MUST be between 18 and 80
  - current weight MUST be between 40 kg and 250 kg
  - goal weight MUST be between 35 kg and (current weight - 2 kg)
  - height MUST be between 120 cm and 210 cm
  - desired weight loss per week MUST be one of 0.25 kg, 0.5 kg, 0.75 kg, or 1.0 kg
  - activity level MUST be one of sedentary, lightly-active, moderately-active, very-active, or athlete
- **AND** the form redisplays with validation errors so I can correct the values.

### Requirement: Calculate Safe Calorie Targets
The system MUST use established formulas to calculate daily calorie needs tailored for safe weight loss.

#### Scenario: Derive calorie targets when the profile is saved
- **GIVEN** I save the profile with valid inputs
- **WHEN** the app calculates targets
- **THEN** it computes Basal Metabolic Rate with the Mifflin-St Jeor equation using metric inputs
- **AND** multiplies BMR by the activity factor for the selected activity level (sedentary 1.2, lightly-active 1.375, moderately-active 1.55, very-active 1.725, athlete 1.9) to get maintenance calories
- **AND** subtracts a daily deficit of `(desired_weight_loss_per_week * 7700) / 7`, capped at 25% of maintenance calories, to get the weight-loss calorie target
- **AND** applies a calorie floor of 1,500 kcal for male profiles and 1,200 kcal for female profiles so the target never drops below these values.

### Requirement: Provide Macro and Fibre Guidance
The system MUST offer macro targets that prioritise protein and fibre while keeping carbohydrates modest.

#### Scenario: Present macro guidance after calculation
- **GIVEN** the calorie target is calculated
- **THEN** the app derives macro goals as:
  - protein grams = round(max(1.6 × goal_weight_kg, 1.2 × current_weight_kg))
  - fat grams = round(max(0.8 × goal_weight_kg, 0.25 × calorie_target / 9))
  - carbohydrate grams = round(max(100, (calorie_target - (protein_g × 4) - (fat_g × 9)) / 4))
  - fibre grams = round(max(28, calorie_target × 0.014))
- **AND** if the carbohydrate calculation would make carbs exceed 45% of total calories, reduce carbs to 45% and reallocate any remaining calories proportionally to protein and fat (preserving their minimums)
- **AND** the page shows the calorie target plus the computed grams for protein, fat, carbohydrates, and fibre in a results section immediately below the profile inputs.
