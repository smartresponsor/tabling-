# CMCP Execution Journal

## 2026-09-13 — repository implementation / RC hardening

### Iteration 1 — reconnaissance and baseline

- Read `README.md`, `composer.json`, `src/`, `tests/`, `config/services.yaml`, current Git state, and declared Composer scripts.
- Read the mandatory dependency contour from local `Objecting`, `Cruding`, `Viewing`, and `Interfacing` repositories, plus Canonization and Gating reference material.
- Consulted Canonization rules `Canon000`, `Canon001`, `Canon002`, `Canon003`, `Canon004`, `Canon012`, and `Canon022`.
- Target-to-canon mapping: `App\\Tabling\\` remains the component namespace; DTOs remain under `src/DTO`; service contracts remain mirrored under `src/ServiceInterface`; no `Domain`, Port, Adapter/Adaptor, generic CRUD controller, or generic CRUD route surface is introduced; provider payloads are treated as a dynamic integration boundary but stable action metadata must be normalized before leaving Tabling.
- `Canon022` is not directly applicable because Tabling is a reusable bundle/package, not a standalone Symfony application (`bin/console` / application boot surface absent). Adding `cruding/crud` would also invert the documented dependency direction because Cruding already consumes Tabling.
- Existing worktree state before CMCP-owned changes: branch `backend-table-actions`, HEAD `18c2dd46bd9fe958fc82759e480753c96e647aee`, with pre-existing untracked `.gating/` left untouched.
- Baseline gates: PHPUnit 4/4 green; PHPStan level 8 green; PHP-CS-Fixer dry-run green.
- RC-critical work selected: prevent `TableActionDTO` objects from leaking into provider payloads; normalize actions to provider-neutral serializable metadata before provider mapping output.
- Growth workstream (post-RC): richer filter/faceting contracts, saved/personalized column state, capability negotiation, and server-side grid protocol metadata.
- Material risks: preserve provider-neutral ownership; do not move CRUD execution into Tabling; do not silently absorb pre-existing `.gating/` artifacts.
- Gates after implementation: PHPUnit, PHPStan level 8, PHP-CS-Fixer dry-run, changed-file PHP lint, Git diff/status review.

### Iteration 2 — material implementation

- Normalize `TableActionDTO` values through `TableActionMetadataBuilder` in both provider mappers.
- Extend provider mapper coverage so Ant Design and PrimeReact expose the same provider-neutral action metadata contract.

### Iteration 3 — verification and fix

- PHPUnit: 4/4 tests green, 25 assertions.
- PHPStan level 8: green with no errors.
- PHP-CS-Fixer dry-run: green with no fixable files.
- PHP syntax lint: green for all three changed PHP files.
- Removed constructor injection from provider mappers after review to preserve the existing zero-argument public construction contract and avoid unnecessary RC compatibility risk.

### Iteration 4 — debt closure and integration

- Reviewed the final diff for responsibility leakage: no CRUD execution, query execution, JavaScript provider implementation, or new cross-component runtime dependency was introduced.
- Pre-existing untracked `.gating/` remains intentionally outside the owned change set.
- Implementation commit was created and pushed to the configured `origin/backend-table-actions` upstream; unrelated `.gating/` content was preserved and excluded.

### Iteration 5 — final acceptance and handoff

- RC-critical implementation and local verification are green.
- Remaining post-RC growth stays explicitly out of scope: richer faceting/filter contracts, saved/personalized views, capability negotiation, and server-side grid protocol metadata.
- The actual remote integration base was identified as `initial-platform-primitive`.
- PR #3 (`backend-table-actions` → `initial-platform-primitive`) was created, inspected as mergeable with no merge-gate blockers, and squash-merged.
- Local `initial-platform-primitive` was fast-forwarded to the merged remote state (`679200172fc8ba224649417eef3c8fd013c06903`).
- Post-merge acceptance: PHPUnit 4/4 tests with 25 assertions; PHPStan level 8 with no errors.

## 2026-09-14 — repository implementation / RC package hardening

### Reconnaissance and baseline

- Current workspace: branch `initial-platform-primitive`, baseline HEAD `bc26adf0a9f7a2dbe88b7929ec44338493f47ce4`, upstream `origin/initial-platform-primitive`, ahead/behind `0/0`; pre-existing untracked `.gating/` remains outside the owned change set.
- Read current `README.md`, `composer.json`, source/services/interfaces/DTOs, unit tests, service configuration, quality scripts and Git state. Root `AGENTS.md` and `MANIFEST.json` are absent.
- Read the current local contracts for `Collectioning`, `Objecting`, `Cruding`, `Viewing`, and `Interfacing`, plus the authoritative `Canonization` rule texts and the executable `Gating` companion.
- Canonization rules consulted for this pass: Canon000, Canon001, Canon002, Canon003, Canon012, Canon017, Canon018, Canon021, Canon023, Canon024, Canon029, Canon032, Canon033, Canon034, Canon036, Canon039, Canon040, Canon043 and Canon045.
- Target-to-canon mapping: `tabling/table` maps to `App\\Tabling\\` and `Table*`; DTOs remain under `src/DTO`, service interfaces under `src/ServiceInterface`, and provider serialization arrays remain an intentional dynamic boundary under Canon012. No `Domain`, Port, Adapter/Adaptor, generic CRUD controller or generic CRUD route surface is introduced.
- Dependency mapping: `Collectioning` is the direct runtime dependency because `TableDefinitionDTO` embeds `CollectionDefinitionDTO`. `Cruding` already requires `tabling/table`; adding the reverse direct dependency would create a Composer cycle, so Tabling keeps CRUD execution/route ownership outside its package. `Objecting`, `Viewing`, and `Interfacing` have no Tabling-owned runtime imports or contracts in the current tree and are host/application integration dependencies rather than fabricated direct package requirements.
- Canon045 closure check: current `Collectioning` has no first-party sibling runtime dependency/path-repository closure, so Tabling needs only the direct `../Collectioning` development repository.
- Market/enterprise baseline: mature grid ecosystems separate table-definition metadata from server-side filtering/sorting/pagination execution. RC therefore focuses on package correctness, typed metadata boundaries and executable quality contracts; growth remains separate.
- Baseline gates: `composer validate --strict --check-lock` green; PHPUnit 4/4 with 25 assertions green; PHPStan level 8 green; PHP-CS-Fixer dry-run failed on formatting/line-ending normalization.

### RC-critical work selected

- Normalize the local `collectioning/collection` development identity to exact `dev-master` and pin its path repository version per Canon043.
- Add a path-free `composer.prod.json` with development/production identity parity per Canon024/Canon033.
- Add repository-owned PHPStan configuration per Canon029.
- Add repository-owned PHPUnit source/coverage configuration and persistent branch-coverage script per Canon039/Canon040.
- Normalize source/test formatting through the repository-owned PHP-CS-Fixer configuration, then rerun Composer validation, tests, PHPStan, CS, PHP lint, coverage/Gating where executable, and inspect final Git state.

### Growth workstream (post-RC)

- Keep grouping/aggregation metadata, saved/personalized table views, capability negotiation, richer facet/filter metadata, and additional provider adapters outside the RC gate unless required by a concrete consumer contract.

### Verification and acceptance

- Added `.gitattributes` with a PHP-only LF rule after the baseline demonstrated Windows line-ending drift in the PHP-CS-Fixer gate.
- Added focused tests for action metadata variants/disabled state, Symfony Security visibility resolution, column metadata edge cases, and `TablingExtension` service loading/alias behavior.
- PHPUnit: 8/8 tests green with 42 assertions.
- Coverage evidence: lines 100.00% (89/89), methods 88.23% (15/17), branches 95.55% (43/45); all Canon040 thresholds are satisfied.
- PHPStan: green with repository-owned level-8 configuration.
- PHP-CS-Fixer dry-run: green with 0 fixable files.
- Root Composer validation: `--strict --check-lock` green. Production manifest validation is executable through `validate:prod` and green.
- `composer audit`: no security vulnerability advisories.
- Composite `composer quality` is green and runs tests, branch coverage, PHPStan, CS and production-manifest validation.
- Code Memory graph planning resolved the repo-local project `D-PhpstormProjects-www-Tabling`; the repository does not declare `memory:scope:resolve`, and no graph mutation surface is available in the current execution toolset, so no graph update is claimed.
- The pre-existing untracked `.gating/` contains a materialized Gating runtime/autoload bridge, but the current safe Console MCP capability set has no arbitrary PHP runner for `.gating/bin/gating`; executable Gating CLI completion is therefore not claimed. Textual Canonization mapping and all repository-owned gates above remain factual.
- During lock refresh the local `Collectioning` `dev-master` reference advanced while this run was active; the final lock was refreshed against the then-current local `dev-master` and root Composer validation remained green.

## 2026-09-15 — PHP-first table declaration continuation

- Re-fetched Collectioning, Tabling, and Cruding and reviewed the post-RC journals before continuing. Collectioning now owns typed filters, cursor pagination, diagnostics, metrics, provider-neutral query planning, and canonical stable sorts; Cruding has completed Collectioning/Tabling delegation plus substantial RC coverage/package hardening; Tabling RC package contracts remain green.
- Selected the next product-facing gap from the original table architecture goal: a concise PHP declaration layer. Prior Tabling APIs exposed DTOs and provider mappers but did not yet provide the `AbstractTable`-style developer ergonomics intended for application authors.
- Added `AbstractTable` plus fluent `TableColumns`, `TableActions`, and `TableFilters` declaration builders. The compiler produces the existing provider-neutral `TableDefinitionDTO`; no Cruding dependency, query execution, React implementation, or provider-specific runtime was introduced.
- Extended `TableDefinitionDTO` with trailing structured `filters` and `bulkActions` fields to preserve existing positional constructor compatibility. Both Ant Design Pro and PrimeReact mappers now serialize those structures through canonical Tabling metadata builders.
- Added end-to-end declaration coverage from an anonymous PHP table definition through Ant Design provider metadata, including row actions, bulk actions, filters, column typing and custom meta.
- Verification is green: PHPUnit 9/9 with 56 assertions, PHPStan level 8 with zero errors, PHP-CS-Fixer with zero pending fixes, and the composite `composer quality` gate passes including branch coverage and production-manifest validation.
- Fresh coverage remains above Canon040 thresholds: lines 97.10% (168/173), methods 80.00% (32/40), branches 85.13% (63/74).

### Default ordering and capability continuation

- Closed the remaining README/runtime gap for Tabling-owned default ordering and table capabilities.
- Added `TableSorting`, which emits Collectioning `CollectionSortDTO` values rather than defining a duplicate sorting language in Tabling.
- Added `TableCapabilitiesDTO` for requested provider-native features (`pagination`, global search, row/bulk selection, column visibility, export, virtual scrolling, inline edit). These flags are metadata only; Tabling does not implement provider UI behavior.
- `TableDefinitionDTO` now carries trailing backward-compatible `defaultSorts` and optional `capabilities`; `AbstractTable` exposes `configureDefaultSorting()` and `capabilities()` hooks.
- Ant Design Pro and PrimeReact metadata now expose the same provider-neutral default-sort and capability payloads, leaving provider-specific rendering/behavior downstream.
- Final quality gate is green after canonical formatting: PHPUnit 9/9 with 62 assertions, PHPStan level 8 zero errors, PHP-CS-Fixer zero pending fixes, production manifest valid. Coverage remains above Canon040 thresholds: lines 96.58% (198/205), methods 78.72% (37/47), branches 83.95% (68/81).

### Server-side grid protocol continuation

- Added provider-specific request adapters without leaking provider grammar into Collectioning. `AntDesignCollectionQueryMapper` translates ProTable-style `current`/`pageSize`, search, filters, sorter and projection fields; `PrimeReactCollectionQueryMapper` translates lazy DataTable-style `first`/`rows`, global filter, field filters and single/multi-sort metadata.
- Added `TableCollectionQueryBuilder` as the shared validation bridge. It checks provider-derived filters, sorts and projected fields against the `CollectionDefinitionDTO` field policy, applies table default sorts when the provider supplies none, clamps page size to Collectioning policy, and emits `CollectionQueryDTO`.
- Disallowed provider fields/operators are dropped before query execution. Provider-specific request syntax therefore terminates in Tabling while Collectioning remains the canonical owner of search/filter/sort/projection/pagination execution semantics.
- Added parity/regression coverage for Ant Design and PrimeReact mapping, including default sort fallback, multi-sort override and rejected non-projectable/non-sortable fields.
- Final `composer quality` is green: PHPUnit 12/12 with 80 assertions, PHPStan level 8 zero errors, PHP-CS-Fixer zero pending fixes, production manifest valid. Coverage remains above Canon040 thresholds: lines 94.55% (295/312), methods 71.42% (40/56), branches 81.78% (229/280).

### Saved and personalized view continuation

- Added `TableColumnStateDTO` and `TableViewDTO` for provider-neutral persisted table state: column visibility/order/width/pinning, search, Collectioning filters/sorts, page size, default marker and metadata.
- Added `TableViewStoreInterface` as a host-owned persistence boundary; Tabling intentionally does not choose Doctrine/Redis/storage ownership for per-user views.
- Added `TableViewNormalizer` to revalidate persisted state against current table columns and Collectioning field policy, dropping stale/unauthorized filters and sorts, clamping page size, normalizing direction/pinning and minimum widths.
- Added Ant Design Pro and PrimeReact saved-view mappers so one normalized backend view produces each provider's native column/search/filter/sort state shape.
- Added regression coverage for stale column rejection, policy enforcement, page-size clamping and provider parity. Final `composer quality` is green: PHPUnit 14/14 with 93 assertions, PHPStan level 8 zero errors, PHP-CS-Fixer zero pending fixes, production manifest valid. Coverage remains above thresholds: lines 95.66% (397/415), methods 72.13% (44/61), branches 82.30% (293/356).

### Provider multi-value filter hardening

- Re-read the current Tabling query bridge plus Objecting, Cruding, Viewing, Interfacing, Gating, Collectioning filter policy, and the relevant Canonization rules (Canon001/009/010/018/019/020/022/025/026/032/033/039/041/042).
- Market baseline confirmed that mature table engines treat controlled filtering state and server-side filtering as first-class capabilities; RC work remains inside Tabling's provider-request translation boundary.
- RC-critical fix: Ant Design array filters and PrimeReact `in` match-mode arrays are preserved and translated to Collectioning's `in` operator instead of being silently discarded.
- Collectioning field/operator policy remains authoritative: an `in` filter is emitted only when the table's `CollectionFieldPolicyDTO` allows it; unsupported operators remain rejected in `TableCollectionQueryBuilder`.
- Added regression tests for Ant Design multi-value filters, PrimeReact `in`, and policy rejection of unauthorized `in` filters.
- Growth remains separate: broader provider match-mode translation should wait until the corresponding Collectioning operator vocabulary is standardized.
- Follow-up reconnaissance re-read Objecting, Cruding, Viewing, Interfacing, Gating, and the relevant Canonization rule texts: Canon001, Canon009, Canon010, Canon011, Canon012, Canon017, Canon018, Canon019, Canon020, Canon021, Canon023, Canon024, Canon026, Canon029, Canon039, Canon040, Canon043, and Canon045.
- Target-to-canon mapping remains unchanged: `tabling/table` maps to `App\\Tabling\\` / `Table*`; provider request arrays remain a legitimate dynamic boundary under Canon012; no alternative layer taxonomy, generic CRUD surface, host implementation dependency, or new first-party runtime edge is introduced. Composer path/version/production-manifest and PHP quality/test contracts remain aligned with Canon023/024/026/029/039/043/045.
- Market comparison against current MUI X and TanStack Table documentation reaffirmed that controlled table state and server-side data/query translation are mature baseline capabilities; persistence, CRUD execution, final rendering, and client-grid behavior remain outside Tabling ownership.
- RC-critical follow-up hardening rejects explicit unsupported PrimeReact `matchMode` values instead of silently reinterpreting them as equality. Canon011 review tightened the initial implementation further: unsupported explicit match modes now raise `InvalidArgumentException` so provider grammar failure remains observable rather than being silently dropped.
- The provider-operator safety contract is documented in README. Follow-up coverage closure added a minimal-table regression that exercises every optional `AbstractTable` default hook; final `composer quality` is green with 19/19 tests and 112 assertions, PHPStan, PHP-CS-Fixer, and production-manifest validation. Fresh Canon040 evidence is lines 97.19% (416/428), methods 85.24% (52/61), branches 85.52% (319/373), all above threshold. Root `composer validate --strict --check-lock` and `composer audit` are also green with no advisories.
- No UI rendering/template/browser surface changed, so visual evidence is not applicable for this pass.

### Malformed provider payload hardening

- RC-critical follow-up: Ant Design and PrimeReact request adapters now normalize non-array `filters` containers to an empty filter set; Ant Design also normalizes a non-array `sorter` container. This prevents PHP `foreach` warnings at the dynamic provider boundary while preserving Collectioning-owned validation and table default sorting.
- Added regression coverage proving malformed provider containers are ignored safely and canonical default sorts still apply.
- Verification is green: `composer quality` passes with 20/20 tests and 116 assertions, branch-coverage execution, PHPStan, PHP-CS-Fixer, and production-manifest validation; changed-PHP lint passes for all three changed PHP files.
- No UI rendering/template/browser surface changed, so visual evidence is not applicable for this pass.

