# TALL Blog

This project will serve as a testing ground and learning platform. I will be using this blog to develop new features and architectures and improve my knowledge about Laravel, PHP and the [TALL Stack](https://tallstack.dev/).

Based on the following tutorial from [Alhassan Kamil](https://dev.to/nayi10): [Create your first blog with TALL](https://dev.to/nayi10/series/11547).

## Technologies

The [TALL Stack](https://tallstack.dev/) is a combination of both front-end and back-end libraries/frameworks that makes developing web apps a breeze. TALL is an acronym for Tailwind CSS, Alpine.js, Laravel and Livewire.

The back-end framework here is Laravel, while Alpine.js and Tailwind CSS are front-end libraries. Laravel Livewire is a bridge between the front-end and the Laravel back-end and brings dynamic data binding to Laravel so you can achieve reactivity and interactivity from the user facing parts of your website with Laravel, instead of using JavaScript.

---

## App configuration

The first step will be to create our app locally. We will be using [Laravel Sail](https://laravel.com/docs/8.x/sail#introduction) in order to use [Docker](https://www.docker.com/) as our development enviroment.

```bash
curl -s "https://laravel.build/tall-blog?with=mysql,redis" | bash
```

With our app created we will launch the app with Sail using the following command inside our app's directory.

```bash
sail up
```

Now it's time to install the required tools for this project, the TALL Stack and Laravel's starter pack [Jetstream](https://jetstream.laravel.com/2.x/introduction.html) which provides some services out of the box to speed up the development process.

```bash
sail composer require laravel/jetstream
```

```bash
sail artisan jetstream:install livewire
```

```bash
sail npm install
sail npm run dev
sail artisan migrate
```

With npm dependencies installed, built and our migrations run, we've finished the scaffolding of this app. Our app now provides traditional and two-factor authentication, session management and API support. With our containers running we should be able to access our app in [http://localhost:80](http://localhost:80).

## Links

-   [Laravel](https://laravel.com/)
-   [Laravel Sail](https://laravel.com/docs/8.x/sail#introduction)
-   [Jetstream](https://jetstream.laravel.com/2.x/introduction.html)
-   [TALL Stack](https://tallstack.dev/)
