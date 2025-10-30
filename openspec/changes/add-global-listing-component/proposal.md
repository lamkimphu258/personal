## Why
To enforce consistency and reduce rework, listings should use a single reusable UI building block. A global Listing Component clarifies expected features (search, edit/delete, inline detail popup) and makes adoption automatic.

## What Changes
- Define a Global Listing Component capability with required behaviors and inputs.
- Prefer using the component by default (guidance), without a global hard mandate.
- Update agent guidance to default to this component for all listing implementations.

## Impact
- Affected specs: ui/listing-component (new capability)
- Agent guidance updated to prefer the component by default
- Implementation later: create a Blade component/partial and update existing pages progressively.
