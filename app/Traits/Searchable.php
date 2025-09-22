<?php

namespace App\Traits;

trait Searchable
{
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm) || !isset($this->searchable)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm) {
            foreach ($this->searchable as $field) {
                $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
            }
        });
    }
}
