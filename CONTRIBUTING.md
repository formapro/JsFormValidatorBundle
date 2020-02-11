Contributing
============

## Setup the development environment for this bundle (Linux/Ubuntu)

1) Clone the bundle
```bash
cd /path/to/your/projects
git clone https://github.com/formapro/JsFormValidatorBundle.git
```
2) Install vendors via docker
```bash
cd JsFormValidatorBundle
docker-compose up -d
docker exec -it PHP-CONTAINER-NAME bash
composer install --dev
```
4) Create var folder and set permissions
```bash
mkdir Tests/app/var
sudo chmod -R 0777 Tests/app/var
```
3) Install assests
```bash
npm i

cd Tests/app/
./bin/console assets:install Tests/app/public
npm run build
```
4) Run tests
```bash
npm run test
```

## Tests

Basically the bundle covered by unit jest's test and e2e cypress test.
The main test case is placed in ```Tests/Functional/MainFunctionalTest.php```
Unit tests are placed in main resource folder with suffix ```.test.js```
e2e test is placed in cypress folder in project root ```cypress/integration/form_spec.js```

The main idea of unit tests is covered constrain logic.
The main idea of e2e test is visit route with example form with all symfony constraint and find used error messages.

## Javascripts

All the javascripts placed in the ```Resources/pulic/js``` folder
All of them are merged and included by assets command to the dev/test environments.
