<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::latest()->paginate(5);

        //return collection of applications as a resource
        return new ApplicationResource(true, 'List Data applications', $applications);
    }

    public function show(string $id)
    {
        $application = Application::findOrFail($id);
        return new ApplicationResource(true,  $application, 'Detail Data Application');
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

    public function getApplicationsHistoryByUserId(string $id)
    {
        $student = Student::where('user_id', $id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $applications = Application::where('student_id', $student->id)
            ->where('is_canceled', false)
            ->with('joblisting.company')
            ->get();

        return response()->json($applications);
    }

    public function getApplicationsHistoryByStudentId(string $id)
    {
        $applications = Application::where('student_id', $id)
            ->where('is_canceled', false)
            ->with('joblisting.company')
            ->get();

        if ($applications->isEmpty()) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        return response()->json($applications);
    }

    public function update(string $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Data Application tidak ditemukan'], 404);
        }


        if ($request->file('cover_letter')) {
            Storage::delete('public/applications/cover-letters/' . basename($application->cover_letter));
        }
        if ($request->file('resume')) {
            Storage::delete('public/applications/resumes/' . basename($application->resume));
        }

        $coverLetter = $request->file('cover_letter');
        $resume = $request->file('resume');

        if ($coverLetter && $resume) {
            $coverLetter->storeAs('public/applications/cover-letters', $coverLetter->hashName());
            $application->cover_letter = $coverLetter->hashName();
            $resume->storeAs('public/applications/resumes', $resume->hashName());
            $application->resume = $resume->hashName();

            $application->update([
                'student_id' => $request->student_id,
                'joblisting_id' => $request->joblisting_id,
                'cover_letter' => $coverLetter->hashName(),
                'resume' => $resume->hashName(),
                'status' => $request->status,
            ]);
        } else {
            $application->update([
                'student_id' => $request->student_id,
                'status' => $request->status,
            ]);
        }


        return new ApplicationResource(true, 'Data Application Berhasil Diperbarui!', $application);
    }

    public function destroy(string $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Data Application tidak ditemukan'], 404);
        }

        if ($application->cover_letter) {
            Storage::delete('public/applications/cover-letters/' . basename($application->cover_letter));
        }
        if ($application->resume) {
            Storage::delete('public/applications/resumes/' . basename($application->resume));
        }

        $application->joblisting()->dissociate();
        $application->student()->dissociate();

        $application->delete();

        return response()->json(['message' => 'Data Application berhasil dihapus'], 200);
    }

    public function cancel(string $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Data Application tidak ditemukan'], 404);
        }

        $application->is_canceled = true;
        $application->save();

        return response()->json(['message' => 'Pekerjaan berhasil dibatalkan'], 200);
    }
}
