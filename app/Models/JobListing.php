<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory;
    // protected $table = 'joblistings';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'location',
        'schedule',
        'start_time',
        'end_time',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
