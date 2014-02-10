UPGRADE FROM 1.0 to 1.1
=======================

You no longer needed to pass native form objects to views and to add any extra functional in views.

1. Just pass the formView object as it's recommended in Symfony documentation and remove ```{{ fp_jsfv(form) }}``` from your views.
2. Remove the ```translation_domain``` key in your ```config.yml``` under ```fp_js_form_validator``` if it is in there.

3. See the [documentation](https://github.com/formapro/JsFormValidatorBundle/blob/master/README.md) to learn about new features.
