<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory;
    protected $table = 'joblistings';

    protected $fillable = [
        'company_id',
        'title',
        'province',
        'description',
        'location',
        'schedule',
        'regency',
        'district',
        'start_time',
        'end_time',
        'isClosed',
        'jobtype_id',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'joblistings_category', 'joblistings_id', 'category_id');
    }

    // public function applications()
    // {
    //     return $this->hasMany(Application::class, 'joblisting_id');
    // }

    public function jobtype()
    {
        return $this->belongsTo(Jobtype::class, 'jobtype_id');
    }
}
