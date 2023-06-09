<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'province',
        'name',
        'description',
        'location',
        'website',
        'email',
        'phone',
        'regency',
        'district'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function jobListings()
    {
        return $this->hasMany(JobListing::class);
    }
}
