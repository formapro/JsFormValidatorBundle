# Contributing

## Development Environment

Prefer the Nix development shell from the repository root:

```bash
nix develop
```

The shell provides PHP 8.5, Xdebug coverage support, Composer, Node.js 22, npm,
zip/unzip, and the Linux runtime libraries needed by Cypress. It also keeps
Composer, npm, and Cypress caches under `.cache/`.

If flakes are not enabled globally, run commands with:

```bash
nix --extra-experimental-features "nix-command flakes" develop
```

The `.envrc` file uses `use flake`; run `direnv allow` once if you use direnv.

## Setup

```bash
git clone https://github.com/formapro/JsFormValidatorBundle.git
cd JsFormValidatorBundle
nix develop
composer update
npm install
```

If Cypress reports that its binary is missing, install it into the local cache:

```bash
npx cypress install
```

Without Nix, install a supported PHP runtime, Composer, Node.js 22, npm, and the
Cypress system dependencies locally before running the same commands.

## Checks

Run the same checks that GitHub Actions runs on pushes and pull requests:

```bash
composer validate --strict
composer test
composer phpstan
composer coverage
npm test
npm run test:coverage
git diff --check
```

For one-off commands without entering the shell:

```bash
nix develop -c composer test
nix develop -c npm test
```

## Test Layout

- PHP unit and controller tests live under `Tests/Unit` and `Tests/Controller`.
- JavaScript unit tests live beside the source files under
  `src/Resources/public/js` and use the `.test.js` suffix.
- Browser smoke tests live under `cypress/` and run against `Tests/app`.
- `Tests/app` is a Symfony fixture application used by the e2e tests.

Do not commit generated dependencies or build output such as `vendor/`,
`node_modules/`, `Tests/app/node_modules/`, `build/`, coverage reports, Cypress
videos, or local cache directories.

## Legacy Docker Files

The historical `docker-compose.yml` and `phpdocker/` files still target PHP 7.2
and Node 10. They are not the maintained development environment for the revived
branch. Use Nix or a locally installed modern PHP/Node toolchain for current
development.
