# FpJsFormValidatorBundle

[![CI](https://github.com/formapro/JsFormValidatorBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/formapro/JsFormValidatorBundle/actions/workflows/ci.yml)
[![Total Downloads](https://poser.pugx.org/fp/jsformvalidator-bundle/downloads.png)](https://packagist.org/packages/fp/jsformvalidator-bundle)

FpJsFormValidatorBundle converts Symfony form validation metadata into JavaScript
validation rules and attaches client-side validators to rendered forms.

## Status

This branch has been revived for a modern PHP/Symfony baseline:

- PHP 8.1+
- Symfony 5.4, 6.4, or 7.x components
- Twig 3
- PHPUnit 10/11
- PSR-4 autoloading from `src/`

The old Symfony 2/3 + Assetic + Selenium test application was replaced upstream
by a newer `Tests/app` fixture. The maintained test commands now cover the PHP
model/factory/controller core, JavaScript constraints, and a Cypress smoke test
against the Symfony test application.

For older Symfony applications, use the historical branches:

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
    resource: '@FpJsFormValidatorBundle/Resources/config/routing.xml'
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

Install dependencies:

```bash
composer update
```

Run the PHP test suite:

```bash
composer test
```

Run the JavaScript unit tests and Cypress browser smoke test:

```bash
npm test
```

Useful local checks:

```bash
composer validate --strict
git diff --check
```

The same maintained test contour is also run by GitHub Actions on pushes and
pull requests.
