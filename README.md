# Finance App Symfony
[![PHP Version](https://img.shields.io/packagist/php-v/symfony/symfony?)](https://github.com/symfony/symfony)

A simple financial transaction crud

![symfony](https://user-images.githubusercontent.com/57726726/168887452-c54118e2-e3c9-42ef-be5e-19ef3faa45f8.gif)

## Technologies
 - [Symfony](https://symfony.com)
 - [Symfony Encore](https://symfony.com/doc/current/frontend/encore/installation.html)
 - [Symfony MakerBundle](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html)
 - [Symfony Asset](https://symfony.com/doc/current/components/asset.html)
 - [Symfony Validator](https://symfony.com/doc/current/validation.html)
 - [Doctrine ORM](https://packagist.org/packages/symfony/orm-pack)
 - [Twig](https://twig.symfony.com)
 - [Twig Components](https://github.com/symfony/ux-twig-component)
 - [Twig intl-extra](https://packagist.org/packages/twig/intl-extra)
 - [Inputmask](https://www.npmjs.com/package//inputmask)
 - [Tailwindcss](https://tailwindcss.com)
 - [daisyUI](https://daisyui.com)

## Run Locally

1. Clone the project
```bash
  git clone https://github.com/luiz-moura/finance-app-symfony.git
```

2. Go to the project directory
```bash
  cd finance-app-symfony
```

3. Start the server in background
```bash
  docker-compose up -d
```

4. Install composer dependencies
```bash
  docker-compose run composer install
```

5. Install NPM dependencies
```bash
  docker-compose run nodejs yarn install
  docker-compose run nodejs yarn dev
```

6. Create tables and fill populate with fictitious data
```bash
  docker-compose run php bin/console doctrine:migrations:migrate
```

http://localhost:8080/

## Helpful commands

### Compile changes to template classes
```bash
npm run dev
``````

## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`DATABASE_URL`

## Author

- [@luizmoura](https://www.github.com/luiz-moura)
