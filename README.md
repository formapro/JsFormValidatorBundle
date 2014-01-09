# FpJsFormValidatorBundle [![Build Status](https://travis-ci.org/formapro/JsFormValidatorBundle.png?branch=master)](https://travis-ci.org/formapro/JsFormValidatorBundle) [![Coverage Status](https://coveralls.io/repos/yury-maltsev/FpJsFormValidatorBundle/badge.png?branch=master)](https://coveralls.io/r/yury-maltsev/FpJsFormValidatorBundle?branch=master)

This module enables validation of the Symfony2 forms on the JavaScript side.
It converts form type constraints into JavaScript validation rules.


## Installation

### Step 1: Download FpJsFormValidatorBundle using composer

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
### Step 2: Enable the bundle

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

### Step 3: Enable the javascript libraries

```twig
<html>
    <head>

        {{ include('FpJsFormValidatorBundle::javascripts.html.twig') }}
    </head>
    <body>

    </body>
</html>
```

### Step 4: Add routes

Include the next routes to your routing config:
```yaml
//app/config/routing.yml
# ...
fp_js_form_validator:
    resource: "@FpJsFormValidatorBundle/Resources/config/routing.xml"
    prefix: /fp_js_form_validator
```

At the moment we use routes to send ajax-requests to check the uniqueness of entities.
Pay attention that your security settings can prevent this action.
So if you are going to use this functional, please check that the requests has necessary permissions.
Or you can redefine this functional (see the Customization paragraph).

## Usage

There are three levels (app, form, field) and three statuses (default, true, false) of the validation.

### An application level:

The validation can be switched on/off globally in your config:
```yaml
//app/config/config.yml
# ...
fp_js_form_validator:
    js_validation: true
```

### A form level:

You can enable/disable the validation for a specified form in its own from builder:
```php
namespace Acme\DemoBundle\Form;

class UserFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'js_validation' => true
        ));
    }
}
```

or in a controller:
```php
class DefaultController extends Controller
{
    public function indexAction()
    {
        $form = $this->createForm(new UserType(), new User(), array(
            'js_validation' => true
        ));

        return $this->render('AcmeDemoBundle:Default:index.html.twig',
            array('form' => $form->createView())
        );
    }
```

### A field level:

You can enable/disable the validation for specified fields only:
```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('name', 'text', array(
            'js_validation' => true
        ));
}
```

### Statuses of the validation:

1. Default (value is not set) - the validation is disabled.
2. ```js_validation = true``` - the validation is enabled, but can be disabled for children levels. **Example:** if you set ```true``` for a form, you can disable it for a specified field.
3. ```js_validation = false``` - the validation is disabled forcibly for this level and all the children levels. **Example:** if you set ```false``` in the config - that means the validation will be disabled for all the forms and form-elements regardless of their settings.

### Issue with sub-requests

Currently js-data for each your form is enabled inside of the template that you have included on the installation step 3.
So if your form was rendered in sub-request:
```twig
<div id="email">
    {{ render(controller('AcmeDemoBundle:Default:sendEmail')) }}
</div>
```
in this way the main template does not know anything about that form.
To fix it, you have to add the initialization to that template manually:
```twig
{# AcmeDemoBundle:Default:sendEmail.html.twig #}

{{ init_js_validation() }}

{{ form(form) }}
```

## Customization

### Configure translations

By default, this bundle uses (just like Symfony2 forms) the "validators" domain for message translation.
To change this domain for JS errors add the next option:
```yaml
//app/config/config.yml
# ...
fp_js_form_validator:
    translation_domain: "custom_domain_name"
```

### Checking the uniqueness of entities

By default our module sends an ajax request to own controller to check the uniqueness using the Doctrine ORM as database manager.
If you use another database manager or you have another reason to customize this action,
you can do it by adding the next option:
```yaml
//app/config/config.yml
# ...
fp_js_form_validator:
    routing:
        check_unique_entity: "custom_route_name"
```

Do not forget to create a controller that will be matched with this route:
```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
// ...
/**
 * @Route("/check_unique_entity", name="custom_route_name")
 */
public function customCheckUniqueEntityAction()
{
    $data = $this->getRequest()->request->all();
    // ...
}
```

The ```$data``` array contains all the properties of the UniqueEntity constraint, the entity name and values of the necessary fields.
This is all the necessary data to make a custom validation action.

### Customizing error output

If you are disagree with an initial error output functionality, you can customize it by redefining the following function:
To redefine it globally for all the forms:
```js
FpJsFormValidatorFactory.showErrors = function(form, errors) {
    // put here your logic to show errors
}
```

To redefine for a specified form:
```js
document.getElementById('specified_form_id').showErrors = function(form, errors) {
    // put here your logic to show errors
}
```

The "form" parameter is the current HTMLFormElement element.
The "errors" parameter is an objech which has the next structure:
```js
var errors = {
    user_gender: {      // This is the DOM identifier of the current field
        type: 'choice', // This is the form type which you've set up in a form builder
        errors: [       // An array of error-messages
            'This field should not be blank.'
        ]

    }
}
```

### Adding extra actions after validation

The next action will be globally called for all the forms on the current page:
```js
FpJsFormValidatorFactory.onvalidate = function(errors) {
    // put here your extra actions
}

// The "errors" parameter has the same format as on the previous step:
```

The next action will be called for the specified form:
```js
document.getElementById('specified_form_id').onvalidate = function(errors) {
    // put here your extra actions
}

// The "errors" parameter has the same format as on the previous step:
```

**Pay attention** that the the second action does not override bot complement the first one.
In the next example you will receive two alerts for the specified form - 'global' and then 'local':

```js
FpJsFormValidatorFactory.onvalidate = function(errors) {
    alert('global')
}
document.getElementById('specified_form_id').onvalidate = function(errors) {
    alert('local')
}
```

### Validation groups from a closure

If you have defined validation groups as a callback:
```php
namespace Acme\DemoBundle\Form;

class UserFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => function() {
                return array('test');
            }
        ));
    }
```

you have to implement it on the JS side.
Just add a JS class with name similar to the full class name of the related form (but without slashes),
and add there the **'getValidationGroups'** method:
```javascript
function AcmeDemoBundleFormUserFormType() {

    this.getValidationGroups = function(model) {
        return ['test_group'];
    }
}
```

### The getters validation

If you have rules for [getters](http://symfony.com/doc/current/book/validation.html#getters), you could implement it in the same way as on the prev. step.
For the next case:
```php
namespace Acme\DemoBundle\Form;

class UserEntity
{
    // Other entity definitions

    /**
     * @return bool
     * @Assert\True(message="Pass")
     */
    public function isPasswordValid()
    {
        return $this->password !== $this->name;
    }
}
```

you should add the next JS:
```javascript
function AcmeDemoBundleFormUserEntity() {

    this.isPasswordValid = function(model) {
        // Check that the name field is not equal to password
    }
}
```

### Custom constraints

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

To cover it you have to create:

```javascript
function AcmeDemoBundleValidatorConstraintsContainsAlphanumeric() {
    /**
     * This value will be filled with the real message received from your php constraint
     */
    this.message = '';

    /**
     * This method is required
     * Should return an error message or an array of messages
     */
    this.validate = function(value, model) {
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
```

### Custom data transformers

You can read [here](http://symfony.com/doc/current/cookbook/form/data_transformers.html) about data transformers.
If you already have a custom composite field with the custom Acme\DemoBundle\Form\DataTransformer\MyTransformer view transformer - you should implement it on JS side to prepare the value for the JS validation:

```javascript
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
```