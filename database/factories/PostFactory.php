<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
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

    /**
     * Indicates the post is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        // We use the state method provided by Factory.
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
                'published_date' => now(),
            ];
        });
    }
}
