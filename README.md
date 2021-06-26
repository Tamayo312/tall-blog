# TALL Blog

This project will serve as a testing ground and learning platform. I will be using this blog to develop new features and architectures and improve my knowledge about Laravel, PHP and the [TALL Stack](https://tallstack.dev/).

Based on the following tutorial from [Alhassan Kamil](https://dev.to/nayi10): [Create your first blog with TALL](https://dev.to/nayi10/series/11547).

## Technologies

The [TALL Stack](https://tallstack.dev/) is a combination of both front-end and back-end libraries/frameworks that makes developing web apps a breeze. TALL is an acronym for Tailwind CSS, Alpine.js, Laravel and Livewire.

The back-end framework here is Laravel, while Alpine.js and Tailwind CSS are front-end libraries. Laravel Livewire is a bridge between the front-end and the Laravel back-end and brings dynamic data binding to Laravel so you can achieve reactivity and interactivity from the user facing parts of your website with Laravel, instead of using JavaScript.

---

## App scaffolding

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

## App configuration

The next step is to configure Jetstream and Fortify, our database connection, routes and creating a symlink to the `public` folder in order to make file uploads possible.

### Configuring Jetstream

To configure Jetstream, we will need to modify the file `config/jetstream.php` to enable photo uploads uncommenting `Features::profilePhotos()`.

### Configuring Fortify

Jetstream is in charge of managing the user interface of our app, and Fortify deals with the authentication. In this step we will enable email verification for all users. To do so, we need to uncomment `Features::emailVerification()` in `config/fortify.php`.

### Configuring the .env file

Inside our .env files we store sensitive information such as API Keys or DB connection credentials. In this case we want to set our DB credentials in order to use a MySQL database for this project.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tall_blog
DB_USERNAME=root
DB_PASSWORD=
```

### Configuring our routes

In Laravel, all routes definitions are held in the routes folder. Web routes(user facing webpage routes) are defined in a file named `routes/web.php`.

### Creating a symlink

To make uploads to `storage/app/public` available in the `public` directory, we create a symbolic link using the following command:

```bash
sail artisan storage:link
```

### Changing the app logo

To be able to customize the logo, and other parts of our predefined Jetstream views, we need to publish them using:

```bash
sail artisan vendor:publish --tag=jetstream-views
```

## DB Management with Laravel

Laravel provides several options to manage an query our databases. In this app we will be focusing on [Eloquent](https://laravel.com/docs/8.x/eloquent#introduction) an Object Relational Mapper (ORM). With Eloquent, each DB table corresponds to a Model in our app that is used to interact with that table.

Models work as data warehouses, any data entering or leaving our app goes through them, they keep track of and update records, they can filter and sort items out, they know how to sanitize and check data for integrity and so much more.

### Database Migrations

As defined in [Laravel Migrations](https://laravel.com/docs/8.x/migrations#introduction): 

> Migrations are like version control for your database, allowing your team to define and share the application's database schema definition. If you have ever had to tell a teammate to manually add a column to their local database schema after pulling in your changes from source control, you've faced the problem that database migrations solve.

This means that migrations allow us to automatically update or modify our DB based on our model structure using config files and shell commands.

### How to create Eloquent models

```bash
sail artisan make:Model ModelName
```

```bash
sail artisan make:model ModelName -m // creates migration too
sail artisan make:model ModelName -c // creates controller too
sail artisan make:model ModelName -f // also creates a factory
sail artisan make:model ModelName -s // also creates a database seeder
sail artisan make:model ModelName -mfcs // creates migration, factory, controller and seeder
```

By default, models will be created in `app/Models`. We can specify a custom directory when creating new models.

### How to create Migrations

```bash
sail artisan make:migration create_model_names_table
```

This command will create a create_model_names_table migration inside `database/migrations` with two methods `up` (used to create tables, indices and columns) and `down` (used to reverse the actions made by `up`)

### Relationships between Models

In our models, we can define our DB relationships by adding simple methods. In our app, one user will have many posts and a post can't belong to multiple users so we will define a one-to-many relationship.

In `app/Models/User.php` we will add this method to create the relationship:

```php
/**
* Get the posts for this user
*/
public function posts() {
    return $this->hasMany(Post::class);
}
```

This method will allow us to get a user's posts with `$user->posts` for an existing `User` object.

### Creating a Post model

To create our `Post` model in Laravel we will use the previous command, adding the options to generate the migration, factory and seeder for this model.

```bash
sail artisan make:Model Post -mfs
```

With this command we just generated three files for our posts in `database/migrations`, `database/factories` and `database/seeders` respectively.

Our first action will be to modify `Post.php` to define a reverse one-to-many relationship between a post and its owner, a user. Just as the relationship we defined before in `User.php` this piece of code allows us to query an existing `Post` object's user by simply calling `$post->user`.

### Creating a migration for Posts

Next we will define the migration required to create a table to store our `Post` objetcs in the DB. In essence, this migration will serve as the blueprint to define the columns of the table.

Inside the `up` method, we define the columns that will be used in our DB. In this case we will use the following code:

```php
/**
 * Add columns for each of the fields defined in our Post model.
 */
Schema::create('posts', function (Blueprint $table) {
    $table->id(); // Auto-incrementing ID
    $table->string('category');
    $table->string('excerpt');
    $table->longText('body');
    $table->string('title');
    $table->boolean('is_published')->default(false);
    $table->string('featured_image');
    $table->dateTime('published_date');
    $table->foreignId('user_id')->constrained(); // Foreing Key that references the users table
    $table->timestamps(); // Automatic creation of created_at and updated_at columns
});
```

With this ready, we need to run our migrations in order to update the DB with the new table.

```bash
sail artisan migrate
```


---

## Links

-   [Laravel](https://laravel.com/)
-   [Laravel Sail](https://laravel.com/docs/8.x/sail#introduction)
-   [Jetstream](https://jetstream.laravel.com/2.x/introduction.html)
-   [TALL Stack](https://tallstack.dev/)
