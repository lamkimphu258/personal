## Why
Ensure consistent behavior across all listing pages: searchable by name, quick edit/delete actions, and an inline popup to view item details without navigation.

## What Changes
- Introduce a global Listing UX standard that applies to all listing pages.
- Define name-based search behavior (case-insensitive substring).
- Define required edit and delete actions available from the list.
- Define a detail popup pattern (open via click; close via button, Escape, or backdrop).
- State applicability: all current and future listing pages MUST conform unless explicitly excepted in their spec.

## Impact
- Affected specs: listing-ux (new capability). Other listing specs should reference this capability rather than duplicating details.
- Affected code (later): list pages across the app will adopt a consistent pattern.
