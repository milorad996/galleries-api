<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',



    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function filter($term, $userId = null)
    {
        $query = Gallery::query();

        $query->with(['images', 'author']);

        if ($userId) {
            $query->where('user_id', '=', $userId);
        }

        if ($term) {
            $query->where(function ($que) use ($term) {
                $que->where('title', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%')
                    ->orWhereHas('author', function ($q) use ($term) {
                        $q->where('first_name', 'like', '%' . $term . '%')
                            ->orWhere('last_name', 'like', '%' . $term . '%');
                    });
            });
        }


        return response()->json([
            'galleries' =>  $query->latest()->paginate(10)
        ]);
    }
}
