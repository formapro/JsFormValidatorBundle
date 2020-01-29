Contributing
============

## Setup the development environment for this bundle (Linux/Ubuntu)

1) Clone the bundle
```bash
cd /path/to/your/projects
git clone https://github.com/formapro/JsFormValidatorBundle.git
```
2) Install vendors
```bash
cd JsFormValidatorBundle
php composer.phar install --dev
```
3) Install assests
```bash
Tests/app/console assets:install Tests/app
```
4) Fix permissions
```bash
sudo chmod -R 0777 Tests/app/cache Tests/app/logs Resources/public/js/fp_js_validator.js
```
5) Create a new virtual host and set the ```Tests/app``` directory as a root
6) Restart apache
```bash
sudo service apache2 restart
```
7) Copy the ```Tests/app/Resources/local_config.php.tpl``` file as local_config.php
8) Open this file and change the host name to your own
9) Run selenium. If you do not have it yet:
```bash
curl http://selenium.googlecode.com/files/selenium-server-standalone-2.33.0.jar > selenium.jar
java -jar selenium.jar
```
10) Run tests
```bash
phpunit
```

## Tests

Basically the bundle covered by functional selenium tests
The main test case is placed in ```Tests/Functional/MainFunctionalTest.php```
It calls pages which are defined in ```Tests/TestBundles/DefaultTestBundle```

The main idea of most tests is:
1) create an action ```mytestAction``` in ```Tests/TestBundles/DefaultTestBundle/Controller/FunctionalTestsController.php``` with a form that has specified list of validation rules
1.1) use a general route to call this action ```test/{controller}/{type}/{js}```
1.2) so, in your action you can get the ```$type``` and ```$js``` parameters
1.3) ```type``` variable can be used to check if the form should be filled out with valid or invalid data (1/0)
1.4) ```js``` variable - to check if JS should be enabled or disabled (1/0)
2) bind valid values to the form (call with ```type = 1```)
3) call this form with disabled JS (native Symfony validation) and get the list of errors ```test/mytest/1/0``` (1 - form is valid, 0 - js is disabled)
4) call the form with enabled JS (here works our bundle) and get list of errors ```test/mytest/1/1``` (1 - form is valid, 1- js is enabled)
5) compare errors from #3 and #4 steps - they should be equal
6) bind invalid values to the form  (call with ```type = 0```)
7) repeat steps #3, #4, #5 (```test/mytest/0/0``` and ```test/mytest/0/0```)

[How to run tests on a real project](Resources/doc/4.md)

## Javascripts

All the javascripts placed in the ```Resources/pulic/js``` folder
All of them are merged and included by assetic bundle for the dev/test environments.
The ```/Resources/public/js/fp_js_validator.js``` file is a merged library that uses as main included file.
Thera no reason to edit this file becauset it is updated automatically each time when you run dev/test