<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_id',
        'commentable_type',
        'content'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function commentable()
    {
        return $this->morphTo();
    }


    public function belongsToUser($userId): bool
    {
        return $this->user_id === $userId;
    }


    public function isPostOwner($userId): bool
    {
        if ($this->commentable_type === 'App\Models\Post') {
            return $this->commentable->user_id === $userId;
        }
        return false;
    }


    public function canBeDeletedBy(User $user): bool
    {

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($this->belongsToUser($user->id)) {
            return true;
        }

        if ($this->isPostOwner($user->id)) {
            return true;
        }

        return false;
    }


    public function canBeEditedBy(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $this->belongsToUser($user->id);
    }
}
