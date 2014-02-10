# FpJsFormValidatorBundle
[![Build Status](https://travis-ci.org/formapro/JsFormValidatorBundle.png?branch=master)](https://travis-ci.org/formapro/JsFormValidatorBundle)

This module enables validation of the Symfony2 forms on the JavaScript side.
It converts form type constraints into JavaScript validation rules.


## 1 Installation<a name="p_1"></a>

[Upgrade from the previous version](https://github.com/formapro/JsFormValidatorBundle/blob/master/UPGRADE-1.1.md)

### 1.1 Download FpJsFormValidatorBundle using composer<a name="p_1_1"></a>

Run in terminal:
```bash
$ php composer.phar require "fp/jsformvalidator-bundle":"dev-master"
```
### 1.2 Enable the bundle<a name="p_1_2"></a>

Enable the bundle:
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

### 1.3 Enable javascript libraries<a name="p_1_3"></a>

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

If you use the UniqueEntity constraint, then you have to include the next part to your routing config:
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
This object contains list of properties and methods which fully define the validation process for the related form element.
And some of those properties and methods can be changed to customize the validation process.

### 3.2 Disable the validation for a specified field<a name="p_3_2"></a>

jQuery plugin:
```js
$('#user_email').jsFormValidator({
    disabled: true
});
```

Pure Javascript:
```js
var field = document.getElementById('user_email');
FpJsFormValidator.customize(field, {
    disabled: true
});
```

### 3.3 Error display<a name="p_3_3"></a>

The showErrors function should delete previous errors and display new ones.
The example below shows you how it works in our default implementation.
The ```sourceId``` variable is an identifier of validation source.
It can be used to prevent any confusion between the field's errors and other errors which have come from other sources.
For example, this field (user_email) may contain the Email constraint, and its own parent may contain the UniqueEntity constraint by this field.
Both of these errors should be displayed for the email field, but the first one will be displayed/deleted by the 'user_email' validator and the second one - by the parent.
By default we use this variable to add it as a class name to 'li' tags, and then we use it to remove the errors by this class name:

```js
$('#user_email').jsFormValidator({
    showErrors: function(errors, sourceId) {
        var list = $(this).prev('ul.form-errors');
        if (!list.length) {
            list = $('<ul class="form-errors"></ul>');
            $(this).before(list);
        }
        list.find('.' + sourceId).remove();

        for (var i in errors) {
            var li = $('<li></li>', {
                'class': sourceId,
                'text': 'custom_'+ errors[i]
            });
            list.append(li);
        }
    }
});
```

Pure Javascript:
```js
var field = document.getElementById('user_email');
FpJsFormValidator.customize(field, {
    showErrors: function(errors, sourceId) {
        for (var i in errors) {
            // do something with each error
        }
    }
});
```

### 3.4 Get validation groups from a closure<a name="p_3_4"></a>

If you have defined validation groups as a callback:

```php
namespace Acme\DemoBundle\Form;

class UserType extends AbstractType
{
    // ...

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'        => 'Acme\DemoBundle\Entity\User',
                'validation_groups' => function () {
                        if (...) {
                            return array(...);
                        } else {
                            return array(...);
                        }
                    }
            )
        );
    }
}
```

Then you have to implement it on the JS side:
```js
$('form#user').jsFormValidator({
    groups: function () {
        if (...) {
            return [...];
        } else {
            return [...];
        }
    }
});
```

Pure Javascript:
```js
var field = document.getElementById('user');
FpJsFormValidator.customize(field, {
    groups: function () {
        if (...) {
            return [...];
        } else {
            return [...];
        }
    }
});
```

### 3.5 Getters validation<a name="p_3_5"></a>

If you have getters validation:
```php
namespace Acme\DemoBundle\Entity;
class User
{
    // ...

    /**
     * @return bool
     *
     * @Assert\True(message="The password cannot match your first name")
     */
    public function isPasswordLegal()
    {
        return $this->firstName != $this->password;
    }
}
```

then you have to create a callback:

Then you have to implement it on the JS side:
```js
$('form#user').jsFormValidator({
    callbacks: {
        'isPasswordLegal': function() {
            var firstName = $('#user_firstName').val();
            var password = $('#user_password').val();
            return firstName != password;
        }
    }
});
```

Pure Javascript:
```js
var field = document.getElementById('user');
FpJsFormValidator.customize(field, {
    callbacks: {
        'isPasswordLegal': function() {
            var firstName = document.getElementById('user_firstName').value;
            var password = document.getElementById('user_password').value;
            return firstName != password;
        }
    }
});
```

### 3.6 The Callback constraint<a name="p_3_6"></a>

#### 3.6.1 Callback by a method name<a name="p_3_6_1"></a>

For the next cases:

```php
namespace Acme\DemoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\Callback("checkEmail")
 * or
 * @Assert\Callback({"checkEmail"})
 */
class User
{
    // ...

    public function checkEmail()
    {
        if (...) {
            $context->addViolationAt('email', 'Email is not valid');
        }
    }
}
```
or
```php
/**
 * @Assert\Callback
 */
public function checkEmail()
{
    if (...) {
        $context->addViolationAt('email', 'Email is not valid');
    }
}
```

You have to create the next callback (pay attention that here you should pass the [sourceId](#p_3_3) parameter):
```js
$('form#user').jsFormValidator({
    callbacks: {
        'checkEmail': function() {
            var errors = [];
            if (...) {
                errors.push('Email is not valid');
            }
            $('#form_email').jsFormValidator('showErrors', {
                errors: errors,
                sourceId: 'check-email-callback'
            });
        }
    }
});
```

Pure Javascript:
```js
var field = document.getElementById('user');
FpJsFormValidator.customize(field, {
    callbacks: {
        'checkEmail': function() {
            var errors = [];
            if (...) {
                errors.push('Email is not valid');
            }
            var email = document.getElementById('user_email');
            FpJsFormValidator.customize(email, 'showErrors', {
                errors: errors,
                sourceId: 'check-email-callback'
            });
        }
    }
});
```

#### 3.6.2 Callback by class and method names<a name="p_3_6_2"></a>

In case if you have defined a callback like this:
```php
namespace Acme\DemoBundle\Entity;

/**
 * @Assert\Callback({"Acme\DemoBundle\Validator\ExternalValidator", "checkEmail"})
 */
class User
{
    // ...
}
```

then you can define it on the JS side like:
```js
// ...
    callbacks: {
        'Acme\\DemoBundle\\Validator\\ExternalValidator': {
            'checkEmail': function () {
                // ...
            }
        }
    }
```

<a name="p_3_6_2_1"></a>or you can also define it without nesting (like in the [3.6.1](#p_3_6_1) paragraph), but only if the method name is unique:
```js
// ...
    callbacks: {
        'checkEmail': function () {
            // ...
        }
    }
```

### 3.7 The Choice constraint. How to get the choices list from a callback<a name="p_3_7"></a>

In general, it works in the same way as the previous step.
In case if you have:
```php
namespace Acme\DemoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\Choice(callback = {"Acme\DemoBundle\Entity\Util", "getGenders"})
     */
    protected $gender;
}
```
```php
namespace Acme\DemoBundle\Entity;

class Util
{
    public static function getGenders()
    {
        return array('male', 'female');
    }
}
```

Then:
```js
$('form#user').jsFormValidator({
    callbacks: {
        'Acme\\DemoBundle\\Entity\\Util': {
            'getGenders': function() {
                return ['male', 'female'];
            }
        }
    }
});
```

Pure Javascript:
```js
var field = document.getElementById('user');
FpJsFormValidator.customize(field, {
    callbacks: {
        'Acme\\DemoBundle\\Entity\\Util': {
            'getGenders': function() {
                return ['male', 'female'];
            }
        }
    }
});
```

also, you can simplify it as it was described [here](#p_3_6_2_1)

### 3.7 Custom constraints<a name="p_3_7"></a>

If you have your own constraint, you have to implement it on the JS side too.
Just add a JS class with a name, similar to the full class name of the related constraint (but without slashes).
For example, you have created the next constraint:

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

If you want to customize validation of the UniqueEntity constraint on the server side, then you can create your own controller and route, and change it in the config:
```yaml
//app/config/config.yml
# ...
fp_js_form_validator:
    routing:
        check_unique_entity: custom_unique_controller
```
```yaml
//app/config/routing.yml
# ...
custom_unique_controller:
    pattern:  /custom_unique_controller
    defaults: { _controller: "AcmeDemoBundle:CustomUnique:index" }
```
```php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomUniqueController extends  Controller
{
    public function indexAction(Request $request)
    {
        $data = $request->request->all();
        // ...
    }
}
```
Here you can see what kind of data you will receive in the request:
```php
$data = array (
  'message'          => 'This value is already used.',
  'service'          => 'doctrine.orm.validator.unique',
  'repositoryMethod' => 'findBy',
  'fields'           => array ('email'),
  'ignoreNull'       => '1',
  'groups'           => array ('Default', 'User'),
  'entityName'       => 'Acme\DemoBundle\Entity\User',
  'data'             => array (
      'email' => 'john_doe@example.com',
  )
)
```

Now, you can create your own logic to check the uniqueness using this input data

## 4 Run tests<a name="p_4"></a>

If you want to run tests for this bundle on your project, you have to:
- copy all the dev requirements from [here](https://github.com/formapro/JsFormValidatorBundle/blob/master/composer.json) to your main composer.json, which are not added yet.
- run ```php composer.phar update```
- navigate to the vendor/fp/jsformvalidator-bundle/Fp/JsFormValidatorBundle folder
- run in terminal ```sh run_tests.sh```
This shell script will make all the necessary settings to run the tests. This will require sudo.
If you do not trust this script - read it and do everything yourself step by step.

- do not forget to remove extra bundles from your composer.json after the testing