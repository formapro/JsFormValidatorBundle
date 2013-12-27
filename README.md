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

In the head of your document you should include the next twig template which collects all the necessary libraries:

```twig
<html>
    <head>

        {{ include('FpJsFormValidatorBundle::javascripts.html.twig') }}
    </head>
    <body>

    </body>
</html>
```

This method uses assets, so if you don't use the Assetic bundle, you have to add manually all the scripts
placed in the @FpJsFormValidatorBundle/Resources/public/js folder

### Step 4: Add routes

Include the next routes to your routing config:

```yaml
//app/config/routing.yml
# ...
fp_js_form_validator:
    resource: "@FpJsFormValidatorBundle/Resources/config/routing.xml"
    prefix: /fp_js_form_validator
```

and add this paths to your security settings:

```yaml
//app/config/security.yml
# ...
access_control:
    - { path: ^/fp_js_form_validator/, roles: IS_AUTHENTICATED_ANONYMOUSLY }
```

## Usage

First of all, pay attention that you should pass NOT a form view object to your view (```$form->createView()```), but a native form object (```$form```):

```php
class ExampleController extends Controller
{
    public function indexAction()
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        return $this->render('AcmeDemoBundle:Example:index.html.twig', array(
            'form' => $form
        ));
    }
}
```

Now you can enable the javascript validation in your view after initializing the form:

```twig
{{ form(form.createView()) }}

{{ fp_jsfv(form) }}
```

## Customization

### Configure translations

By default, this bundle uses (just like Symfony2 forms) the "validation" domain for message translation.

If you had changed this option for the server side, you should also make changes for our bundle.

Just add this option to your config:

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

To redefine a global method:

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
            'This field shoul not be blank.'
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
