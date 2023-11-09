<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicationRequisite extends Model
{
    use HasFactory;
    protected $fillable = ['is_cover_letter', 'is_transcript', 'is_recommendation_letter', 'is_proposal', 'is_resume', 'joblisting_id'];
}
