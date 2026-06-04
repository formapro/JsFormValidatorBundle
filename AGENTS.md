# Agent Notes

Repository: `formapro/JsFormValidatorBundle`
Local path: `/Volumes/SRC/JsFormValidatorBundle`

## Current Repository State

- PR #173 revived the project and was merged into `formapro/master`.
- PR #174 restored GitHub Actions CI and was merged into `formapro/master`.
- PR #175 restored selected old PR fixes and was merged into `formapro/master`.
- Replacement PRs #176, #177, and #178 are open, ready for review, and were last checked as `MERGEABLE / CLEAN` with green CI.

## CI

- Added `.github/workflows/ci.yml`.
- The workflow runs on `push` and `pull_request`.
- The PHP job runs on PHP `8.1`, `8.2`, `8.3`, `8.4`, and `8.5`.
- The PHP job runs `composer update`, `composer validate --strict`, and `composer test`.
- The JavaScript job runs on Node `22` with PHP `8.3`.
- The JavaScript job installs Cypress system dependencies, then runs `composer update`, `npm install`, and `npm test`.
- The old `.travis.yml` file was removed.
- `README.md` was updated to use a GitHub Actions badge and test instructions instead of Travis CI references.
- `package.json` no longer advertises the old Travis CI badge in the package description.

## Local Validation

- `php /tmp/jsfv-composer.phar test` passes: `3 tests, 14 assertions`.
- `npm test` passes: Jest `184 tests`; Cypress e2e `16 tests`.
- The local `composer` shim is broken with `Could not open input file: /Users/ton/bin/composer`, so use `/tmp/jsfv-composer.phar` locally if needed.
- The working tree was clean after the latest validation.

## GitHub Checks Note

- `gh pr checks 174 --repo formapro/JsFormValidatorBundle --watch=false` reported `no checks reported`.
- This is expected because PR #174 introduces the GitHub Actions workflow for the first time.
- After PR #174 is merged, the workflow should exist on `master` and run on subsequent `push` and `pull_request` events.
