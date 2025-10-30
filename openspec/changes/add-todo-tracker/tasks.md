## Implementation
- [ ] Create database structures for tasks and recurring schedules (base task definition plus per-day occurrence/completion tracking).
- [ ] Wire up Todo routes, controller, and view with date selector, summary widget row, and global listing component integration.
- [ ] Build create/edit flows with form request validation for title, priority options, due date, and recurrence settings.
- [ ] Implement completion toggle, delete handling, and occurrence regeneration so recurring tasks surface on the correct days.
- [ ] Populate Blade templates with Tailwind styling and Alpine interactions for inline editing and summary widget updates.
- [ ] Manually verify CRUD flows, recurrence behaviours, and summary counts for a variety of dates; capture any seed data adjustments needed for QA.
- [ ] Validate each scenario in `openspec/changes/add-todo-tracker/specs/todo-planner/spec.md` through manual walkthroughs and note any discrepancies for follow-up fixes.
- [ ] Run `vendor/bin/pint --dirty` on touched PHP files before finishing.
