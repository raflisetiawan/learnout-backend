<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'name',
        'address',
        'email',
        'phone',
        'university_id',
        'regency',
        'district',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'student_category');
    }
}
