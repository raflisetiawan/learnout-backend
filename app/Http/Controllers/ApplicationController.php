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
            'cover_letter' => 'required|string',
            'resume' => 'nullable|mimes:docx,pdf|max:10240',
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $resume = $request->file('resume');
        $resume->storeAs('public/applications', $resume->hashName());

        $application = Application::create([
            'student_id' => $request->student_id,
            'joblisting_id' => $request->joblisting_id,
            'cover_letter' => $request->cover_letter,
            'resume' => $resume->hashName(),
            'status' => $request->status,
        ]);

        return new ApplicationResource(true, 'Data Application Berhasil Ditambahkan!', $application);
    }
}
