## Why
- Editing food entries currently redirects through a helper route before returning to the listing form. This adds extra navigation and feels disconnected from the inline detail popup the component already provides.
- The owner asked for the Edit action to open a popup so edits happen inline without leaving the listing context.

## What Changes
- Update the Global Listing Component capability so the Edit action launches an inline popup edit experience instead of navigating away.
- Define how the component accepts configuration to render an edit form inside the popup with the current item’s values prefilled.
- Plan follow-up implementation work to update the Blade component and at least one consumer page to use the popup-based edit flow.

## Impact
- Affected capability: ui-listing-component (modified requirement)
- Implementation touches: resources/views/components/listing.blade.php, resources/views/tracking.blade.php, related controller logic to supply edit data.
- Manual regression focus: ensure delete confirmation still works and detail popup remains accessible.
