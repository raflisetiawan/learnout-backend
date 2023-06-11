<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::latest()->paginate(5);

        //return collection of applications as a resource
        return new ApplicationResource(true, 'List Data applications', $applications);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'joblisting_id' => 'required|exists:joblistings,id',
            'status' => 'required|in:pending,accepted,rejected',
            'cover_letter' => 'nullable|file|mimes:pdf,docx,txt|max:5242880',
            'resume' => 'nullable|file|mimes:pdf,docx,txt|max:5242880',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $existingApplication = Application::where('student_id', $request->student_id)
            ->where('joblisting_id', $request->joblisting_id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'Anda sudah pernah melamar pekerjaan ini'], 422);
        }

        $coverLetter = $request->file('cover_letter');
        $coverLetter->storeAs('public/applications/cover-letters', $coverLetter->hashName());

        $resume = $request->file('resume');
        $resume->storeAs('public/applications/resumes', $resume->hashName());

        $application = Application::create([
            'student_id' => $request->student_id,
            'joblisting_id' => $request->joblisting_id,
            'cover_letter' => $coverLetter->hashName(),
            'resume' => $resume->hashName(),
            'status' => $request->status,
        ]);

        return new ApplicationResource(true, 'Data Application Berhasil Ditambahkan!', $application);
    }
}
