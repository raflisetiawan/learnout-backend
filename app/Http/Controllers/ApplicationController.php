<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\JobApplicationRequisite;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $application = Application::with('student', 'joblisting')->find($id);
        $user = User::find($application->student->user_id);
        return new ApplicationResource(true,  ['application' => $application, 'user' => $user], 'Detail Data Application');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'joblisting_id' => 'required|exists:joblistings,id',
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $requisites = JobApplicationRequisite::where('joblisting_id', $request->joblisting_id)->first();

        if (!$requisites) {
            return response()->json(['message' => 'Requisites not found for this job listing'], 422);
        }

        $fileFields = ['cover_letter', 'resume', 'transcripts', 'recommendation_letter', 'proposal', 'health_insurance'];

        foreach ($fileFields as $field) {
            $isRequired = $requisites->{'is_' . $field};
            $file = $request->file($field);

            if ($isRequired && !$file) {
                return response()->json(['message' => 'File ' . $field . ' is required'], 422);
            }

            if ($file) {
                $maxFileSize = 5242880; // 5MB
                $allowedMimes = ['pdf', 'docx', 'txt'];

                $validator = Validator::make(
                    [$field => $file],
                    [
                        $field => 'file|mimes:' . implode(',', $allowedMimes) . '|max:' . $maxFileSize,
                    ]
                );

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }
                if ($field != 'transcripts') {

                    $file->storeAs('public/applications/' . $field . 's', $file->hashName());
                } else {
                    $file->storeAs('public/applications/' . $field, $file->hashName());
                }
            }
        }

        $applicationData = ['student_id' => $request->student_id, 'joblisting_id' => $request->joblisting_id, 'status' => $request->status];

        foreach ($fileFields as $field) {
            if ($request->file($field)) {
                $applicationData[$field] = $request->file($field)->hashName();
            }
        }

        $existingApplication = Application::where('student_id', $request->student_id)
            ->where('joblisting_id', $request->joblisting_id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'Anda sudah pernah melamar pekerjaan ini'], 422);
        }

        $application = Application::create($applicationData);

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

    public function getApplicationsHistoryByJobId(string $id)
    {
        $applications = Application::where('joblisting_id', $id)
            ->where('is_canceled', false)
            ->with('student')
            ->get();

        if ($applications->isEmpty()) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        // Ambil data student beserta waktu mendaftar dari setiap aplikasi
        $students = $applications->map(function ($application) {
            return [
                'student' => $application->student,
                'created_at' => $application->created_at,
                'status' => $application->status,
                'id' => $application->id
            ];
        });

        return response()->json($students);
    }
    public function getApplicationByStudentId(string $id)
    {
        $applications = Application::where('student_id', $id)
            ->where('is_canceled', false)
            ->with('student', 'joblisting.company')
            ->get();

        if ($applications->isEmpty()) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        return response()->json($applications[0]);
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

        // Hapus berkas yang sudah ada jika ada pengiriman berkas baru
        $fileFields = ['cover_letter', 'resume', 'proposal', 'recommendation_letter', 'transcripts', 'health_insurance'];
        foreach ($fileFields as $field) {
            if ($request->file($field)) {
                if ($field !== 'transcripts') {
                    Storage::delete('public/applications/' . $field . 's/' . basename($application->$field));
                } else {
                    Storage::delete('public/applications/' . $field . basename($application->$field));
                }
            }
        }

        // Proses penyimpanan berkas baru jika ada
        foreach ($fileFields as $field) {
            $file = $request->file($field);
            if ($file) {
                if ($field !== 'transcripts') {
                    $file->storeAs('public/applications/' . $field . 's', $file->hashName());
                } else {
                    $file->storeAs('public/applications/' . $field, $file->hashName());
                }
                $application->$field = $file->hashName();
            }
        }

        // Perbarui data aplikasi
        $application->update([
            'student_id' => $request->student_id,
            'joblisting_id' => $request->joblisting_id,
            'status' => $request->status,
        ]);

        return new ApplicationResource(true, 'Data Application Berhasil Diperbarui!', $application);
    }


    public function destroy(string $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Data Application tidak ditemukan'], 404);
        }

        // Hapus berkas yang terkait jika ada
        $fileFields = ['cover_letter', 'resume', 'proposal', 'recommendation_letter', 'transcripts'];

        foreach ($fileFields as $field) {
            if ($application->$field) {
                if ($field !== 'transcripts') {

                    Storage::delete('public/applications/' . $field . 's/' . basename($application->$field));
                } else {
                    Storage::delete('public/applications/' . $field  . basename($application->$field));
                }
            }
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

    public function rejectApplication(string $id)
    {
        $application = Application::find($id);
        if (!$application) {
            return response()->json(['message' => 'Data Application tidak ditemukan'], 404);
        }

        $application->status = 'reject';
        $application->save();

        return response()->json(['message' => 'Pekerjaan berhasil di Tolak'], 200);
    }
    public function acceptApplication(string $id)
    {
        $application = Application::find($id);
        if (!$application) {
            return response()->json(['message' => 'Data Application tidak ditemukan'], 404);
        }

        $application->status = 'accept';
        $application->save();

        return response()->json(['message' => 'Pekerjaan berhasil di Tolak'], 200);
    }

    public function getApplicationCountByStudentId(string $studentId)
    {
        $applicationCount = Application::where('student_id', $studentId)->count();
        $applicationPendingCount = Application::where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();
        $applicationRejectCount = Application::where('student_id', $studentId)
            ->where('status', 'reject')
            ->count();

        $applications = Application::where('student_id', $studentId)->get(['created_at']); // Retrieve the created_at field for each application

        $applicationsPerDay = [];
        foreach ($applications as $application) {
            $timestamp = $application->created_at->timestamp;
            $day = date('D', $timestamp); // Get the day of the week (e.g., "Mon")

            if (isset($applicationsPerDay[$day])) {
                $applicationsPerDay[$day]++;
            } else {
                $applicationsPerDay[$day] = 1;
            }
        }

        return response()->json([
            'count' => $applicationCount,
            'pendingCount' => $applicationPendingCount,
            'applicationRejectCount' => $applicationRejectCount,
            'applicationsPerDay' => $applicationsPerDay
        ]);
    }

    public function getApplicationCount()
    {
        $applicationCount = Application::count();

        return response()->json(['count' => $applicationCount]);
    }

    public function getApplicationsCountPerDay()
    {
        $startDate = Carbon::now()->now()->startOfMonth();
        $endDate = Carbon::now()->now()->endOfMonth();

        $applications = DB::table('applications')
            ->select(DB::raw('MONTHNAME(created_at) as month'), DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month', 'date')
            ->get();

        $result = [];
        foreach ($applications as $application) {
            $result[$application->month][] = [
                'date' => $application->date,
                'count' => $application->count,
            ];
        }

        $json = [];
        foreach ($result as $month => $data) {
            $json[] = [
                'month' => $month,
                'date' => array_column($data, 'date'),
                'count' => array_column($data, 'count'),
            ];
        }

        return response()->json($json);
    }

    public function getApplicationCountPerMonth()
    {
        $startDate = Carbon::now()->startOfYear(); // Start from the beginning of the year
        $endDate = Carbon::now(); // End at the current date

        $applications = Application::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = [];
        $counts = [];
        foreach ($applications as $application) {
            $months[] = $application->month;
            $counts[] = $application->count;
        }

        $result = [
            'month' => $months,
            'count' => $counts,
        ];

        return response()->json([$result]);
    }

    public function getAcceptedApplicationsCountPerMonth()
    {
        $startDate = Carbon::now()->startOfYear(); // Start from the beginning of the year
        $endDate = Carbon::now(); // End at the current date

        $applications = Application::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('status', 'accept') // Filter by accepted applications
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = [];
        $counts = [];
        foreach ($applications as $application) {
            $months[] = $application->month;
            $counts[] = $application->count;
        }

        $result = [
            'month' => $months,
            'count' => $counts,
        ];

        return response()->json([$result]);
    }
    public function getRejectedApplicationsCountPerMonth()
    {
        $startDate = Carbon::now()->startOfYear(); // Start from the beginning of the year
        $endDate = Carbon::now(); // End at the current date

        $applications = Application::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('status', 'reject') // Filter by accepted applications
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = [];
        $counts = [];
        foreach ($applications as $application) {
            $months[] = $application->month;
            $counts[] = $application->count;
        }

        $result = [
            'month' => $months,
            'count' => $counts,
        ];

        return response()->json([$result]);
    }
    public function getPendingApplicationsCountPerMonth()
    {
        $startDate = Carbon::now()->startOfYear(); // Start from the beginning of the year
        $endDate = Carbon::now(); // End at the current date

        $applications = Application::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('status', 'pending') // Filter by accepted applications
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = [];
        $counts = [];
        foreach ($applications as $application) {
            $months[] = $application->month;
            $counts[] = $application->count;
        }

        $result = [
            'month' => $months,
            'count' => $counts,
        ];

        return response()->json([$result]);
    }

    public function getApplicationsPerMonth()
    {
        $applications = Application::selectRaw('MONTHNAME(applications.created_at) AS month, companies.name AS company_name, joblistings.title, students.name AS student_name, applications.status')
            ->join('joblistings', 'applications.joblisting_id', '=', 'joblistings.id')
            ->join('students', 'applications.student_id', '=', 'students.id')
            ->join('companies', 'joblistings.company_id', '=', 'companies.id')
            ->orderBy('applications.created_at', 'asc')
            ->get();

        $result = [];
        foreach ($applications as $application) {
            $result[] = [
                'month' => $application->month,
                'company_name' => $application->company_name,
                'title' => $application->title,
                'student_name' => $application->student_name,
                'status' => $application->status,
            ];
        }

        return response()->json($result);
    }
}
