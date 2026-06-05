# Agent Notes

Repository: `formapro/JsFormValidatorBundle`
Local path: `/Volumes/SRC/JsFormValidatorBundle`

## Current Repository State

- PR #173 revived the project and was merged into `formapro/master`.
- PR #174 restored GitHub Actions CI and was merged into `formapro/master`.
- PR #175 restored selected old PR fixes and was merged into `formapro/master`.
- Replacement PRs #176, #177, and #178 were merged into `formapro/master`.
- `1.7.0-beta1` was published from `formapro/master`.

## CI

- Added `.github/workflows/ci.yml`.
- The workflow runs on `push` and `pull_request`.
- The PHP job runs on PHP `8.4`, `8.5`, and `8.6` nightly.
- The PHP job runs `composer update`, `composer validate --strict`, and `composer test`.
- The JavaScript job runs on Node `22` with PHP `8.5`.
- The JavaScript job installs Cypress system dependencies, then runs `composer update`, `npm install`, and `npm test`.
- The PHPStan job runs on PHP `8.5`, installs dependencies with `composer update`, warms the Symfony test cache, and runs `composer phpstan`.
- The Coverage job runs on PHP `8.5` with Xdebug and Node `22`, then runs `composer coverage` and `npm run test:coverage`.
- Coverage generates Cobertura XML, uploads it with `actions/upload-code-coverage@v1` when GitHub permissions allow, and keeps raw reports as workflow artifacts.
- Coverage thresholds are enforced by `tools/check-coverage.php`: PHP line coverage at least `50%`, JavaScript line coverage at least `60%`.
- The old `.travis.yml` file was removed.
- `README.md` was updated to use a GitHub Actions badge and test instructions instead of Travis CI references.
- `package.json` no longer advertises the old Travis CI badge in the package description.

## Local Validation

- `php /tmp/jsfv-composer.phar test` passes: `5 tests, 18 assertions`.
- `php /tmp/jsfv-composer.phar phpstan` runs PHPStan with `phpstan.neon`.
- `php /tmp/jsfv-composer.phar coverage` generates PHP Cobertura coverage and checks the `50%` line threshold.
- `npm test` passes: Jest `197 tests`; Cypress e2e `16 tests`.
- `npm run test:coverage` generates Jest coverage and checks the `60%` line threshold.
- The local `composer` shim is broken with `Could not open input file: /Users/ton/bin/composer`, so use `/tmp/jsfv-composer.phar` locally if needed.

## Nix Development Environment

- Prefer `nix develop` from the repository root when Nix is available.
- The Nix shell provides the latest PHP available in pinned nixpkgs with Xdebug coverage support, Composer, Node.js 22, npm, zip/unzip, and Linux Cypress runtime libraries. It currently resolves to PHP 8.5.
- The Docker `php-fpm` development image is maintained for vendor installation and ad hoc commands. It uses PHP 8.5, Composer 2, Node.js 22, and the PHP extensions required by the Symfony 8 fixture.
- Run one-off commands with `nix develop -c <command>`, for example:
  - `nix develop -c composer validate --strict`
  - `nix develop -c composer test`
  - `nix develop -c composer phpstan`
  - `nix develop -c composer coverage`
  - `nix develop -c npm test`
  - `nix develop -c npm run test:coverage`
- On a fresh checkout, run `nix develop -c npm install` first; if Cypress reports a missing binary, run `nix develop -c npx cypress install`.
- If flakes are not enabled globally, prefix commands with `nix --extra-experimental-features "nix-command flakes"`.
- `.envrc` uses `use flake`; run `direnv allow` once if using direnv.
- Inside the Nix shell, use `composer` directly. Outside the Nix shell, keep using `/tmp/jsfv-composer.phar` if the local Composer shim is still broken.

## GitHub Checks Note

- `gh pr checks 174 --repo formapro/JsFormValidatorBundle --watch=false` reported `no checks reported`.
- This is expected because PR #174 introduces the GitHub Actions workflow for the first time.
- After PR #174 is merged, the workflow should exist on `master` and run on subsequent `push` and `pull_request` events.
