## 1. Spec
- [x] 1.1 Extend the Global Listing Component spec to require the Edit action to open an inline popup form instead of navigating away.
- [x] 1.2 Document how pages configure the edit popup (form action, fields, and value binding) within the capability.

## 2. Implementation
- [x] 2.1 Update `resources/views/components/listing.blade.php` to render and control the inline edit popup form.
- [x] 2.2 Update `resources/views/tracking.blade.php` to supply edit form configuration and remove redirect-based editing.
- [x] 2.3 Adjust `TrackingController` (or related actions) to pass any additional data needed for the popup edit experience.

## 3. Validation (manual)
- [x] 3.1 Manually verify that clicking Edit opens the popup with the item’s current values prefilled.
- [x] 3.2 Manually verify that submitting the popup form updates the entry and rerenders the listing.
- [x] 3.3 Manually verify that the detail popup still opens from the item name and Delete confirmation behaves as before.
