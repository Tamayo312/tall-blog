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
    $table->string('slug');
    $table->boolean('is_published')->default(false);
    $table->string('featured_image')->nullable();
    $table->dateTime('published_date');
    $table->foreignId('user_id')->constrained(); // Foreing Key that references the users table
    $table->timestamps(); // Automatic creation of created_at and updated_at columns
});
```

With this ready, we need to run our migrations in order to update the DB with the new table.

```bash
sail artisan migrate
```

### Post Factory

Model factories in Laravel allow us to generate dummy data using the Faker PHP library. We will be able to generate random data by specifying various attributes.

We already created a factory the moment we generated the model for our posts, but we could do this separatedly with the following command:

```bash
sail artisan make:factory PostFactory
```

With the `--model` flag we can create a model at the same time. This command generates a file inside `database/factories`, inside this file we will use a `$model` attribute and a `definition` method to define the values to be applied when the factory is executed.

For our `Post` model we will define a method likeso:

```php
public function definition()
{
    return [
        'category' => $this->faker->text(100),
        'body' => $this->faker->paragraphs(15, true),
        'title' => $this->faker->sentence(15),
        'excerpt' => $this->faker->sentences(3, true),
        'featured_image' => "post.png", // Hardcoded value for convenience
        'published_date' => $this->faker->date(),
        'user_id' => 1, // Hardcoded value for convenience
        // We didn't define an is_published definition because we already setted a default 'false' value
    ];
}
```

We will also add a published state to our factory. This will be used to modify the default value of the `is_published` column whenever we want to make a particular post as published.

```php
/**
 * Indicates the post is published.
 *
 * @return \Illuminate\Database\Eloquent\Factories\Factory
 */
public function published()
{
    return $this->state(function (array $attributes) {
        return [
            'is_published' => true,
            'published_date' => now(),
        ];
    });
}
```

To use factories we could do it using the following code:

```php
//Creates a post without persisting to database
$post = Post::factory()->make();

// Same as above, but creates five(5) posts
$post = Post::factory()->count(5)->make();

// Same as above, but sets the published state to true
$post = Post::factory()->count(5)->published()->make()﻿;

//Creates a post and persists it to database
$post = Post::factory()->create();

// Same as persisting, but creates five(5) posts
$post = Post::factory()->count(5)->create();

// Same as persisting, but sets the published state to true
$post = Post::factory()->count(5)->published()->create();
```

### Post Seeder

While model facotries let you create sample data for your models, database seeders actually insert these data into the database. Seeders don't necessarily have to depend on factories to insert data.

Seeders are placed in the `database/seeders` directory an contain a `run` method, called when the `db:seed` command is executed. Using the base `DatabaseSeeder` class provided by Laravel we can run our seeders by providing them in the `call` method of this class.

To create a DB seeder we can use the following command:

```bash
sail artisan make:seeder PostSeeder
```

In our case, we will define our `run` method in `PostSeeder` using the factory we defined previously using the following code

```php
/**
 * Run the database seeds.
 *
 * @return void
 */
public function run()
{
    Post::factory()
            ->count(20)
            ->published()
            ->create();
}
```

With this seeder we will create 20 post records in our DB, specifying that each of them will be published.

Lastly, we will open `DatabaseSeeder` and add this seeder to the class, this will ensure that it will be run when executing our seeders.

```php
 /**
 * Seed the application's database.
 *
 * @return void
 */
public function run()
{
    $this->call([
        PostSeeder::class,
    ]);
}
```

Though we could just run the `PostSeeder` without adding it here, doing it this way keeps your seeders organized an decoupled and can be run from a single central point.

We are ready to run the seeder using:

```bash
sail artisan db:seed
```

## Livewire

Livewire components are reusable pieces of code that you can define once and use in different parts of your application.

They are just as Laravel components but comes with the power of both Laravel and Livewire. By default, a Livewire component is created with a correpsonding view file.

Livewire component classes are also placed in `app/Http/Livewire` directory while their corresponding views are placed in the `resources/views/livewire` directory.

### Creating Livewire components

We can use `make:livewire` to create a component and its associated views.

```bash
sail artisan make:livewire PostItem
```

This command will create the most basic Livewire component, which extends the base Livewire `Component` class and contains a `render` method - used for rendering views and inline text:

```php
use Livewire\\Component;

class PostItem extends Component {
    public function render() {
        return view('livewire.post-item');
    }
}
```

The correspondent view will be in `resources/views/livewire/post-item.blade.php`.

With the `--inline` flag we create *inline* components which won't be returning any view in their `render` method.

```php
sail artisan make:livewire PostItem --inline
```

It is important to understand that any `public` property in a Livewire component are readily available in the component's view file, so we don't need to pass it through in the `view` method as we did with a Blade component.

### Rendering Livewire Components

Livewire components are meant to be reusable. As a aresult, you can use them anywhere you would a Laravel component. Rendering a component can be done by either using the `<livewire:component-name/>` tag syntax or by using the `@livewire('component-name')`.

### Passing parameters to Components

We can pass parameters to a component by specifying those parameters like so:

```php
<livewire:post-item :post="$post" />
{{-- or --}}
@livewire('post-item', ['post' => $post])
```

### Accesing route rest_parameters

In a situation whereby you need to access route parameters like you would in a traditional Laravel controller, Livewire allows you to do that in the `mount` method.

```php
class MyComponent extends Component {
    public $userId;

    public function mount($userId) {
        $this->userId = $userId;
    }

    public function render() {
        return view('livewire.my-component');
    }
}
```

### Creating the blog components

Firstly, we are creating a `PostItem` component which will show the information of a single post.

We will use the following command to create the component and the view:

```bash
sail artisan make:livewire PostItem
```

Next, we will add a `public $post` attribute in the component. This will make available the post information inside the corresponding view.

To show all the posts we will need yet another component, which will fetch all the post in the DB and display each one in the post item component we just created.

```bash
sail artisan make:livewire ShowPosts
```

Inside `ShowPosts` we will use the `mount` method to fetch all the post from DB and load them inside a `public` attribute called `$posts`

---

## Links

-   [Laravel](https://laravel.com/)
-   [Laravel Sail](https://laravel.com/docs/8.x/sail#introduction)
-   [Jetstream](https://jetstream.laravel.com/2.x/introduction.html)
-   [TALL Stack](https://tallstack.dev/)