
# Finance App Symfony

[![MIT License](https://img.shields.io/apm/l/atomic-design-ui.svg?)](https://github.com/tterb/atomic-design-ui/blob/master/LICENSEs)
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
## Authors

- [@luizmoura](https://www.github.com/luiz-moura)


## Run Locally

Clone the project

```bash
  git clone https://github.com/luiz-moura/finance-app-symfony.git
```

Go to the project directory

```bash
  cd finance-app-symfony
```

Install dependencies PHP

```bash
  composer install
```

Install dependencies JS

```bash
  yarn install && yarn dev
```

Start the docker compose

```bash
  sudo docker compose up
```

Start the server

```bash
  symfony server:start
```


## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`DATABASE_URL`
