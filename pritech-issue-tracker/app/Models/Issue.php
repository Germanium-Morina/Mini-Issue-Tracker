<?php
namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Issue extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'title', 'description', 'status', 'priority', 'due_date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function users()
{
    return $this->belongsToMany(User::class);
}
}
