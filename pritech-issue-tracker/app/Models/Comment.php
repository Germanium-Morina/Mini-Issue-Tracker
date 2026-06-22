<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['issue_id', 'author_name', 'body'];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
