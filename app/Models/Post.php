<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory; // Used to create factories for our model

    /**
     * Columns that are mass assignable (also correspond to our table columns)
     *
     * A mass assignment vulnerability occurs when a user passes an
     * unexpected HTTP request field and that changes a column in your database that you did not expect.
     *
     * To avoid this problem, we define a $fillable attribute for our models with the fields
     */
    protected $fillable = [
        'title', 'excerpt', 'body', 'category',
        'featured_image', 'published_date',
        'is_published', 'user_id', 'slug',
    ];

    /**
     * Returns the user for this post
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
