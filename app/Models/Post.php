<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAuthor;
use App\Traits\Searchable;

class Post extends Model
{
    use HasFactory, HasAuthor, Searchable;

    protected $fillable = [
        'title',
        'body',
        'author_id',
    ];

    protected $searchable = [
        'title',
        'body',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
