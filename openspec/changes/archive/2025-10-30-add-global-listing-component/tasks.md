## 1. Spec + Guidance
- [x] 1.1 Add Listing Component capability spec (props/slots, search, actions, popup contract)
- [x] 1.2 Prefer component by default (no global mandate); update proposal accordingly
- [x] 1.3 Update AGENTS.md guidance to reference and prefer the component

## 2. Implementation
- [x] 2.1 Implement Blade component/partial

## 3. Validation (manual)
- [x] 3.1 Verify default latest-first sort when `dateKey` exists
- [x] 3.2 Verify name search is case-insensitive and Clear restores list
- [x] 3.3 Verify inline popup opens from item click and closes via button/Escape/backdrop
- [x] 3.4 Verify popup shows configured fields (labels/values)
- [x] 3.5 Verify Edit/Delete actions render when routes provided and Delete confirms
