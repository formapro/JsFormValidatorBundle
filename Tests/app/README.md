# Test Application

`Tests/app` is a Symfony fixture application used by the root project test
suite. It is not a standalone Symfony Demo installation.

The root npm scripts build and serve this application for Cypress:

```bash
npm run test:e2e:build
npm run test:e2e:serve
npm run test:e2e:run
```

The full browser test flow is:

```bash
npm test
```

When using the Nix development shell, run those commands from the repository
root after `composer update` and `npm install`.

The fixture installs the bundle assets into `Tests/app/public` during
`npm run test:e2e:build`; those generated files should not be committed.
