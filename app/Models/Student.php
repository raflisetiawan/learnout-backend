<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'province',
        'phone',
        'university_id',
        'regency',
        'district',
        'resume',
        'curriculum_vitae',
        'student_role_id',
        'linkedin'
    ];

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_student', 'student_id', 'application_id', 'linkedin',);
    }

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

    public function joblistings()
    {
        return $this->belongsToMany(Joblisting::class, 'student_joblisting', 'student_id', 'joblisting_id');
    }
    protected function curriculumVitae(): Attribute
    {
        return Attribute::make(
            get: fn ($curriculum_vitae) => asset('/storage/students/curriculum_vitae/' . $curriculum_vitae),
        );
    }


    public function student_roles()
    {
        return $this->belongsTo(StudentRole::class, 'student_role_id');
    }
}
