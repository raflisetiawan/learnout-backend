<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JobListing as job_listing;

class Application extends Model
{
    protected $table = "applications";
    use HasFactory;
    protected $fillable = ['student_id', 'joblisting_id', 'cover_letter', 'resume', 'status'];

    protected function resume(): Attribute
    {
        return Attribute::make(
            get: fn ($resume) => asset('/storage/applications/' . $resume),
        );
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function joblisting()
    {
        return $this->belongsTo(job_listing::class);
    }
}
