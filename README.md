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
3) for the specified [field]()

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

## Customization

### Preface. Understanding the bundle

This bundle finds related DOM elements for each element of a symfony form and attach to it a special object-validator, that contains list of properties and methods, which fully define the validation process for the related form element.

To work with the customization you have to understand general principles how this bundle works:
1) If you display a symfony form using the default twig function ```{{ form(form) }}```, then each element of this form has its own related DOM element in html.
For example you have the next form structure:
form_user:
    name
    email
    address_sub_form:
        number
        street
        city

```html
<form name="form" method="post" action="">
    <div id="form">
        <div>
            <label for="form_name" class="required">Name</label>
            <input type="text" id="form_name" name="form[name]" required="required"></div><div><label for="form_clear" class="required">Clear</label><input type="text" id="form_clear" name="form[clear]" required="required" maxlength="50"></div><div><label class="required">Email</label><div id="form_email"><div><label for="form_email_name" class="required">Name</label><input type="text" id="form_email_name" name="form[email][name]" required="required"></div></div></div><input type="hidden" id="form__token" name="form[_token]" value="9A_A0C6Phrvhitd4KsoWA4pS_qB_QpvILT8yb1UMVS0"></div></form>
```

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
<script type="text/javascript">
    FpJsFormValidatorFactory.showErrors = function(form, errors) {
        // put here your logic to show errors
    }
<script/>
```

To redefine for a specified form:
```js
<script type="text/javascript">
    document.getElementById('specified_form_id').showErrors = function(form, errors) {
        // put here your logic to show errors
    }
<script/>
```

The "form" parameter is the current HTMLFormElement element.
The "errors" parameter is an objech which has the next structure:
```js
<script type="text/javascript">
    var errors = {
        user_gender: {      // This is the DOM identifier of the current field
            type: 'choice', // This is the form type which you've set up in a form builder
            errors: [       // An array of error-messages
                'This field should not be blank.'
            ]

        }
    }
<script/>
```

### Adding extra actions after validation

The next action will be globally called for all the forms on the current page:
```js
<script type="text/javascript">
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
<script/>
```

**Pay attention** that the the second action does not override but complement the first one.
In the next example you will receive two alerts for the specified form - 'global' and then 'local':

```js
<script type="text/javascript">
    FpJsFormValidatorFactory.onvalidate = function(errors) {
        alert('global')
    }
    document.getElementById('specified_form_id').onvalidate = function(errors) {
        alert('local')
    }
<script/>
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
```js
<script type="text/javascript">
    function AcmeDemoBundleFormUserFormType() {

        this.getValidationGroups = function(model) {
            return ['test_group'];
        }
    }
<script/>
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
```js
<script type="text/javascript">
    function AcmeDemoBundleFormUserEntity() {

        this.isPasswordValid = function(model) {
            // Check that the name field is not equal to password
        }
    }
<script/>
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
<script/>
```

### Custom data transformers

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