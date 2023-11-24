<?php

namespace App\Models;

use App\Http\Resources\JobListing;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JobListing as job_listing;

class Application extends Model
{
    protected $table = "applications";
    use HasFactory;
    protected $fillable = ['student_id', 'joblisting_id', 'cover_letter', 'resume', 'status', 'is_canceled', 'transcripts', 'recommendation_letter', 'proposal'];

    protected function resume(): Attribute
    {
        return Attribute::make(
            get: fn ($resume) => asset('/storage/applications/resumes/' . $resume),
        );
    }
    protected function coverLetter(): Attribute
    {
        return Attribute::make(
            get: fn ($cover_letter) => asset('/storage/applications/cover_letters/' . $cover_letter),
        );
    }
    protected function proposal(): Attribute
    {
        return Attribute::make(
            get: fn ($proposal) => asset('/storage/applications/proposals/' . $proposal),
        );
    }
    protected function transcript(): Attribute
    {
        return Attribute::make(
            get: fn ($transcript) => asset('/storage/applications/transcripts/' . $transcript),
        );
    }
    protected function recommendationLetter(): Attribute
    {
        return Attribute::make(
            get: fn ($recommendation_letter) => asset('/storage/applications/recommendation_letters/' . $recommendation_letter),
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
