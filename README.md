# FpJsFormValidatorBundle
[![Build Status](https://travis-ci.org/formapro/JsFormValidatorBundle.svg?branch=master)](https://travis-ci.org/formapro/JsFormValidatorBundle)
[![Total Downloads](https://poser.pugx.org/fp/jsformvalidator-bundle/downloads.png)](https://packagist.org/packages/fp/jsformvalidator-bundle)

This module enables validation of the Symfony 4 or later forms on the JavaScript side.
It converts form type constraints into JavaScript validation rules.

If you have Symfony 4.* - you need to use [Version 1.6.x-dev](https://github.com/formapro/JsFormValidatorBundle/tree/1.6)

If you have Symfony 3.1.* - you need to use [Version 1.5.*](https://github.com/formapro/JsFormValidatorBundle/tree/1.5)

If you have Symfony 3.0.* - you need to use [Version 1.4.*](https://github.com/formapro/JsFormValidatorBundle/tree/1.4)

If you have Symfony 2.8.* or 2.7.* - you need to use [Version 1.3.*](https://github.com/formapro/JsFormValidatorBundle/tree/1.3)

If you have Symfony 2.6.* or less - you need to use [Version 1.2.*](https://github.com/formapro/JsFormValidatorBundle/tree/1.2)

## 1 Installation<a name="p_1"></a>

### 1.1 Download FpJsFormValidatorBundle using composer<a name="p_1_1"></a>

Run in terminal:
```bash
$ composer require "fp/jsformvalidator-bundle":"dev-master"
```
Or if you do not want to unexpected problems better to use exact version.
```bash
$ composer require "fp/jsformvalidator-bundle":"v1.6.*"
```

### 1.2 Enable javascript libraries

There are two ways to initialize javascript's files for this library. 
You can create a new entry in the webpack or import the main file into your javascript.

#### 1.2.1 Add FpJsFormValidatorBundle to webpack.config.js
```diff
Encore
    ...
    .addEntry('app', './assets/js/app.js')
+   .addEntry('FpJsFormElement', './vendor/fp/jsformvalidator-bundle/Fp/JsFormValidatorBundle/Resources/public/js/FpJsFormValidatorWithJqueryInit.js')
    ...
    .configureBabel(null, {
        useBuiltIns: 'usage',
        corejs: 3,
    })
;
```

And include new entry in your template
```diff
+   {{ encore_entry_script_tags('FpJsFormElement') }}
    {{ encore_entry_script_tags('app') }}
```

#### 1.2.2 Import FpJsFormValidatorBundle in your main javascript
```diff
  import $ from 'jquery';
+  import 'path-to-bundles/fpjsformvalidator/js/FpJsFormValidator';
+  import 'path-to-bundles/fpjsformvalidator/js/jquery.fpjsformvalidator';
``` 

#### 1.2.3 Use inits in your template
```diff
{% block javascripts %}
+   {{ js_validator_config() }}
+   {{ init_js_validation() }}
{% endblock %}
```

### 1.4 Add routes<a name="p_1_4"></a>

If you use the UniqueEntity constraint, then you have to include the next part to your routing config: app/config/routing.yml
```yaml
# ...
fp_js_form_validator:
    resource: "@FpJsFormValidatorBundle/Resources/config/routing.xml"
    prefix: /fp_js_form_validator
```
Make sure that your security settings do not prevent these routes.

## 2 Usage<a name="p_2"></a>

After the previous steps the javascript validation will be enabled automatically for all your forms.

1. [Disabling validation](src/Resources/doc/2_1.md)<a name="p_2_1"></a>
2. [If your forms are placed in sub-requests](src/Resources/doc/2_2.md)<a name="p_2_2"></a>
3. If you need to initialize JS validation for your forms separately, or by some event, in this case you need to follow [these steps](src/Resources/doc/2_3.md) instead of the [chapter 1.3](#p_1_3)

## 3 Customization<a name="p_3"></a>

### Preface

This bundle finds related DOM elements for each element of a symfony form and attach to it a special object-validator.
This object contains list of properties and methods which fully define the validation process for the related form element.
And some of those properties and methods can be changed to customize the validation process.

If you render forms with a some level of customization - read [this note](src/Resources/doc/3_0.md).

1. [Disable validation for a specified field](src/Resources/doc/3_1.md)
2. [Error display](src/Resources/doc/3_2.md)
3. [Get validation groups from a closure](src/Resources/doc/3_3.md)
4. [Getters validation](src/Resources/doc/3_4.md)
5. [The Callback constraint](src/Resources/doc/3_5.md)
6. [The Choice constraint. How to get the choices list from a callback](src/Resources/doc/3_6.md)
7. [Custom constraints](src/Resources/doc/3_7.md)
8. [Custom data transformers](src/Resources/doc/3_8.md)
9. [Checking the uniqueness of entities](src/Resources/doc/3_9.md)
10. [Form submit by Javasrcipt](src/Resources/doc/3_10.md)
11. [onValidate callback](src/Resources/doc/3_11.md)
12. [Run validation on custom event](Resources/doc/3_12.md)
13. [Collections validation](src/Resources/doc/3_13.md)
