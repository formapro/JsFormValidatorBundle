PHP Docker Development Image
============================

This Docker setup is maintained as an alternative way to install vendors and run
ad hoc local commands without host PHP or Composer.

The `php-fpm` image provides:

* PHP 8.5 FPM
* Composer 2
* Node.js 24 and npm
* PHP extensions used by the Symfony 8 fixture: `intl`, `pdo_mysql`,
  `pdo_sqlite`, and `zip`
* Linux libraries needed by the Cypress binary

Build the image:

```bash
docker compose build php-fpm
```

Install PHP and JavaScript vendors from the repository root:

```bash
docker compose run --rm --no-deps -u "$(id -u):$(id -g)" php-fpm composer update
docker compose run --rm --no-deps -u "$(id -u):$(id -g)" php-fpm npm install
```

Run ad hoc commands in the same image:

```bash
docker compose run --rm --no-deps -u "$(id -u):$(id -g)" php-fpm composer test
docker compose run --rm --no-deps -u "$(id -u):$(id -g)" php-fpm npm test
```

The compose file stores Composer, npm, and Cypress caches under `.cache/`.
