# FpJsFormValidatorBundle [![Build Status](https://travis-ci.org/yury-maltsev/FpJsFormValidatorBundle.png?branch=master)](https://travis-ci.org/yury-maltsev/FpJsFormValidatorBundle)

This module enables validation of the Symfony2 forms on the JavaScript side.

It converts form type constraints into JavaScript validation rules.


## Installation

### Step 1: Download FpJsFormValidatorBundle using composer

Add the next line to your ``composer.json`` file:

```json
"require-dev": {
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

### Step 4: Set up the routing

If you don't use checking the uniqueness of entities, you can skip this step.

Otherwise you should to include routes:

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

First of all pay attention that you should pass to your view NOT the form view object (```$form->createView()```) but the native form object (```$form```):

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
