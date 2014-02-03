# FpJsFormValidatorBundle [![Build Status](https://travis-ci.org/formapro/JsFormValidatorBundle.png?branch=master)](https://travis-ci.org/formapro/JsFormValidatorBundle) [![Coverage Status](https://coveralls.io/repos/yury-maltsev/FpJsFormValidatorBundle/badge.png?branch=master)](https://coveralls.io/r/yury-maltsev/FpJsFormValidatorBundle?branch=master)

This module enables validation of the Symfony2 forms on the JavaScript side.
It converts form type constraints into JavaScript validation rules.


## 1 Installation<a name="p_1"></a>

### 1.1 Download FpJsFormValidatorBundle using composer<a name="p_1_1"></a>

Add the next line to your ``composer.json`` file:
```json
"require": {
    ...
    "fp/jsformvalidator-bundle": "dev-master"
}
```

Now run:
```bash
$ php composer.phar update fp/jsformvalidator-bundle
```
### 1.2 Enable the bundle<a name="p_1_2"></a>

Enable the bundle in the kernel:
```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Fp\JsFormValidatorBundle\FpJsFormValidatorBundle(),
    );
}
```

### 1.3 Enable the javascript libraries<a name="p_1_3"></a>

```twig
<html>
    <head>
        {{ include('FpJsFormValidatorBundle::javascripts.html.twig') }}
    </head>
    <body>

    </body>
</html>
```

### 1.4 Add routes<a name="p_1_4"></a>

If you use check the uniqueness of entities, then you have to include the next part to your routing config:
```yaml
//app/config/routing.yml
# ...
fp_js_form_validator:
    resource: "@FpJsFormValidatorBundle/Resources/config/routing.xml"
    prefix: /fp_js_form_validator
```
Make sure that your security settings do not prevent these routes.

## 2 Usage<a name="p_2"></a>

After the previous steps the javascript validation will be enabled automatically for all your forms.

### 2.1 Disabling<a name="p_2_1"></a>

You can disable the validation in three ways:
1) globally
```yaml
//app/config/config.yml
# ...
fp_js_form_validator:
    js_validation: false
```
2) for the specified form:
```php
namespace Acme\DemoBundle\Form;

class UserFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'js_validation' => false
        ));
    }
}
```
3) for the [specified field](#p_3_2)

### 2.2 Issue with sub-requests<a name="p_2_2"></a>

All the necessary data for validation forms are initialized in the included template (initializer) that was defined on the step [1.3](#p_1_3)
So if your form was rendered in sub-request, e.g.:
```twig
<div id="email">
    {{ render(controller('AcmeDemoBundle:Default:sendEmail')) }}
</div>
```
in this way the initializer does not know anything about that form.
To fix it, you have to add the initialization to your sub-template manually:
```twig
{# AcmeDemoBundle:Default:sendEmail.html.twig #}

{{ init_js_validation() }}

{{ form(form) }}
```

## 3 Customization<a name="p_3"></a>

### 3.1 Preface<a name="p_3_1"></a>

This bundle finds related DOM elements for each element of a symfony form and attach to it a special object-validator.
That object contains list of properties and methods which fully define the validation process for the related form element.
And some of those properties and methods can be changed to customize the validation process.

### 3.2 Disable the validation for a specified field<a name="p_3_2"></a>

jQuery plugin:
```js
$('#user_email').jsFormValidator({
    disabled: true
});
```

Native Javascript:
```js
var field = document.getElementById('user_email');
FpJsFormValidator.customize(field, {
    disabled: true
});
```

### 3.3 Error display<a name="p_3_3"></a>

The example below shows errors in the same way as the default functional of this bundle.
Each field can contain not only its own errors, but also errors that have come from other sources of validation.
For example, this field (user_email) may contain the Email constraint, and its own form may contain the UniqueEntity constraint by this field.
Both of these errors should be displayed for the email field.
The similar situations may occur when you use the Callback constraint.
So, to prevent any confusion between the field's errors and other the errors which have come from other sources, we've added the 'sourceClass' variable that shows you a unique id of the source of errors.
By default we use this variable to add it as a class name to 'li' tags, and then we use it to remove the errors by this class name.
You can see that in the example below:

```js
$('#user_email').jsFormValidator({
    showErrors: function(errors, sourceClass) {
        var list = $(this).prev('ul.form-errors');
        if (!list.length) {
            list = $('<ul class="form-errors"></ul>');
            $(this).before(list);
        }
        list.find('.' + sourceClass).remove();

        for (var i in errors) {
            var li = $('<li></li>', {
                'class': sourceClass,
                'text': 'custom_'+ errors[i]
            });
            list.append(li);
        }
    }
});
```

Native Javascript:
```js
var field = document.getElementById('user_email');
FpJsFormValidator.customize(field, {
    showErrors: function(errors, sourceClass) {
        for (var i in errors) {
            // do something with each error
        }
    }
});
```

### 3.4 Get validation groups from a closure<a name="p_3_4"></a>

**In progress**

### 3.5 Getters validation<a name="p_3_5"></a>

**In progress**

### 3.6 The Callback constraint<a name="p_3_6"></a>

**In progress**

### 3.7 Custom constraints<a name="p_3_7"></a>

If you have your own constraint, you can do the same as on the prev. steps:

```php
// src/Acme/DemoBundle/Validator/Constraints/ContainsAlphanumeric.php
namespace Acme\DemoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ContainsAlphanumeric extends Constraint
{
    public $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';
}

// src/Acme/DemoBundle/Validator/Constraints/ContainsAlphanumericValidator.php
namespace Acme\DemoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[a-zA-Za0-9]+$/', $value, $matches)) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));
        }
    }
}
```

To cover it on JS side, you have to create:

```js
<script type="text/javascript">
    function AcmeDemoBundleValidatorConstraintsContainsAlphanumeric() {
        /**
         * This value will be filled with the real message received from your php constraint
         */
        this.message = '';

        /**
         * This method is required
         * Should return an error message or an array of messages
         */
        this.validate = function(value) {
            if (value.length && !/^[a-zA-Za0-9]+$/.test(value)) {
                return this.message.replace('%string%', value);
            }
        }

        /**
         * Optional method
         */
        this.onCreate = function() {
            // You can put here some extra actions which will be called after build of this constraint
            // E.g. you can make some preparing actions with the properties
        }
    }
<script/>
```

### 3.8 Custom data transformers<a name="p_3_8"></a>

You can read [here](http://symfony.com/doc/current/cookbook/form/data_transformers.html) about data transformers.
If you already have a custom composite field with the custom Acme\DemoBundle\Form\DataTransformer\MyTransformer view transformer - you should implement it on JS side to prepare the value for the JS validation:

```js
<script type="text/javascript">
    function AcmeDemoBundleFormDataTransformerMyTransformer() {
        /**
         * Some extra option, defined in your transformer. It will be filled from your php class
         */
        this.extraOption = '';

        /**
         * This method is required
         * should return the resulting value
         */
        this.reverseTransform = function(value, model) {
            // Some actions to compose the real value
            return value;
        }
    }
<script/>
```

### 3.9 Checking the uniqueness of entities<a name="p_3_9"></a>

**In progress**