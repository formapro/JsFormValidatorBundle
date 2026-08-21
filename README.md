# FpJsFormValidatorBundle

[![CI](https://github.com/formapro/JsFormValidatorBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/formapro/JsFormValidatorBundle/actions/workflows/ci.yml)
[![Total Downloads](https://poser.pugx.org/fp/jsformvalidator-bundle/downloads.png)](https://packagist.org/packages/fp/jsformvalidator-bundle)

FpJsFormValidatorBundle converts Symfony form validation metadata into JavaScript
validation rules and attaches client-side validators to rendered forms.

## Development Moves To Svaroh/JsFormValidatorBundle

Active development continues at
[Svaroh/JsFormValidatorBundle](https://github.com/Svaroh/JsFormValidatorBundle),
maintained by Svaroh, under the new Composer package
`svaroh/jsformvalidator-bundle`. That is a new package with its own version
history starting at `1.0`, not a `2.0` of this one, and it renames the project's
identifiers from `Fp`/`fp` to `Svaroh`/`svaroh`, so switching over requires
changes in your application.

**1.x stays here.** This repository remains the home of the 1.x line and of the
`fp/jsformvalidator-bundle` package, and `1.8.0` stays available. With
development moved to Svaroh, 1.x receives no further fixes from the maintainers.
Pull requests against 1.x are still welcome here and will be merged.

Existing 1.x installations keep working and need no changes.

## Status

This branch has been revived for the current PHP/Symfony baseline:

- PHP 8.4+ runtime support; CI currently verifies PHP 8.4, 8.5, and
  8.6 nightly
- Symfony 8.x components as declared in `composer.json`
- Twig 3
- PHPUnit 13
- PHPStan static analysis
- PHP and JavaScript coverage thresholds
- PSR-4 autoloading from `src/`

The old Symfony 2/3 + Assetic + Selenium test application was replaced upstream
by a newer `Tests/app` fixture. The maintained test commands now cover the PHP
model/factory/controller core, JavaScript constraints, and a Cypress smoke test
against the Symfony test application.

For older Symfony applications, use the historical branches:

- Symfony 5.4, 6.4, or 7.x: `1.7`
- Symfony 4: `1.6`
- Symfony 3.1: `1.5`
- Symfony 3.0: `1.4`
- Symfony 2.8 or 2.7: `1.3`
- Symfony 2.6 or older: `1.2`

## Installation

Install the bundle with Composer:

```bash
composer require fp/jsformvalidator-bundle
```

If you are testing this revived branch before a tagged release, require the
development branch explicitly:

```bash
composer require fp/jsformvalidator-bundle:"dev-master"
```

Register the bundle in `config/bundles.php`:

```php
<?php

return [
    // ...
    Fp\JsFormValidatorBundle\FpJsFormValidatorBundle::class => ['all' => true],
];
```

## Configuration

Validation is enabled for every form by default. You can disable it globally:

```yaml
# config/packages/fp_js_form_validator.yaml
fp_js_form_validator:
    js_validation: false
```

Per-form and per-field disabling is documented in
[Disabling validation](src/Resources/doc/2_1.md).

## UniqueEntity Route

If you use Symfony's Doctrine `UniqueEntity` constraint, import the bundle route:

```yaml
# config/routes/fp_js_form_validator.yaml
fp_js_form_validator:
    resource: '@FpJsFormValidatorBundle/Resources/config/routing.yaml'
    prefix: /fp_js_form_validator
```

Make sure your security configuration allows requests to this route.

## JavaScript Assets

There are two common ways to load the JavaScript files.

### Add an Encore Entry

```diff
Encore
    // ...
    .addEntry('app', './assets/js/app.js')
+   .addEntry(
+       'FpJsFormValidator',
+       './vendor/fp/jsformvalidator-bundle/src/Resources/public/js/FpJsFormValidatorWithJqueryInit.js'
+   )
    // ...
;
```

Then include the entry in your template:

```twig
{{ encore_entry_script_tags('FpJsFormValidator') }}
{{ encore_entry_script_tags('app') }}
```

### Import From Your Main JavaScript

```diff
 import $ from 'jquery';
+import '../vendor/fp/jsformvalidator-bundle/src/Resources/public/js/FpJsFormValidator';
+import '../vendor/fp/jsformvalidator-bundle/src/Resources/public/js/jquery.fpjsformvalidator';
```

Adjust the import path to your application structure.

### Render Bundle Config And Form Models

After the scripts are loaded, render the generated config and queued form models:

```twig
{{ js_validator_config() }}
{{ init_js_validation() }}
```

If you need manual initialization for a specific form or event, see
[manual initialization](src/Resources/doc/2_3.md).

## Usage

After the bundle is registered, the form extension adds every enabled root form
to an internal queue. Calling `init_js_validation()` renders JavaScript models
for the queued forms:

```twig
{{ form_start(form) }}
    {{ form_widget(form) }}
{{ form_end(form) }}

{{ init_js_validation(form) }}
```

You can pass `false` as the second argument to avoid automatic initialization on
page load:

```twig
{{ init_js_validation(form, false) }}
```

## Documentation

1. [Disabling validation](src/Resources/doc/2_1.md)
2. [Forms in sub-requests](src/Resources/doc/2_2.md)
3. [Manual initialization](src/Resources/doc/2_3.md)

### Customization

This bundle finds related DOM elements for each Symfony form element and
attaches an object validator to them. The validator contains the properties and
methods that define the validation process for that form element.

If your form rendering is customized, start with
[custom rendering notes](src/Resources/doc/3_0.md).

1. [Disable validation for a specified field](src/Resources/doc/3_1.md)
2. [Error display](src/Resources/doc/3_2.md)
3. [Get validation groups from a closure](src/Resources/doc/3_3.md)
4. [Getters validation](src/Resources/doc/3_4.md)
5. [The Callback constraint](src/Resources/doc/3_5.md)
6. [The Choice constraint callback](src/Resources/doc/3_6.md)
7. [Custom constraints](src/Resources/doc/3_7.md)
8. [Custom data transformers](src/Resources/doc/3_8.md)
9. [Checking entity uniqueness](src/Resources/doc/3_9.md)
10. [Form submit by JavaScript](src/Resources/doc/3_10.md)
11. [onValidate callback](src/Resources/doc/3_11.md)
12. [Run validation on custom event](src/Resources/doc/3_12.md)
13. [Collections validation](src/Resources/doc/3_13.md)

## Development

The recommended development environment is the Nix shell:

```bash
nix develop
```

It provides the latest PHP available in the pinned nixpkgs input, currently
PHP 8.5, with Xdebug coverage support, Composer, Node.js 24, npm, zip/unzip,
and Cypress runtime libraries. If flakes are not enabled globally, prefix Nix
commands with `nix --extra-experimental-features "nix-command flakes"`.

For a fresh checkout:

```bash
composer update
npm install
```

If Cypress reports a missing binary, install it into the local cache:

```bash
npx cypress install
```

You can also run commands without entering the shell:

```bash
nix develop -c composer test
nix develop -c npm test
```

Without Nix, install PHP 8.4 or newer, Composer, Node.js 24, npm, and the
Cypress system dependencies locally before running the same commands.

To install vendors via Docker instead of host PHP/Composer:

```bash
docker compose build php-fpm
docker compose run --rm --no-deps -u "$(id -u):$(id -g)" php-fpm composer update
docker compose run --rm --no-deps -u "$(id -u):$(id -g)" php-fpm npm install
```

Install or refresh Composer dependencies:

```bash
composer update
```

Run the PHP test suite:

```bash
composer test
```

Run PHPStan static analysis:

```bash
composer phpstan
```

Run PHP coverage:

```bash
composer coverage
```

Run the JavaScript unit tests and Cypress browser smoke test:

```bash
npm test
```

Run JavaScript coverage:

```bash
npm run test:coverage
```

Useful local checks:

```bash
composer validate --strict
git diff --check
```

The same maintained test, static-analysis, and coverage checks are also run by
GitHub Actions on pushes and pull requests. Coverage runs generate Cobertura
reports and upload them to GitHub Code Quality when workflow permissions allow.

The Docker stack uses PHP 8.5, Composer 2, and Node.js 24. It is maintained for
dependency installation and ad hoc local commands; Nix remains the preferred
environment for exact local parity with the documented checks.
