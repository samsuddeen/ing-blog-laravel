<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
   protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);

                $originalSlug = $post->slug;
                $count = 1;
                while (static::where('slug', $post->slug)->exists()) {
                    $post->slug = $originalSlug . '-' . $count++;
                }
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }


    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }





    public function scopePublished(Builder $query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }


   


    public function isPublished(): bool
    {
        return $this->status === 'published'
               && $this->published_at
               && $this->published_at->lte(now());
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}

