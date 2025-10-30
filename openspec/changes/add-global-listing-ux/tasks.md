## 1. Adoption Plan
- [ ] 1.1 Finalize listing-ux spec and validate
- [ ] 1.2 Update in-flight changes to reference listing-ux (e.g., Food Templates)
- [ ] 1.3 Inventory existing listing pages and add follow-up deltas if needed

## 2. Validation
- [ ] 2.1 `openspec validate add-global-listing-ux --strict`
- [ ] 2.2 `openspec show add-global-listing-ux --json --deltas-only` sanity-check

## 3. Notes
- Individual pages can extend the base behaviors (e.g., additional filters) but MUST still meet the standard unless explicitly excepted.
