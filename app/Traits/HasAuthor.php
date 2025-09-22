<?php

namespace App\Traits;

trait HasAuthor
{
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function isAuthoredBy($userId): bool
    {
        return $this->author_id === $userId;
    }
}
