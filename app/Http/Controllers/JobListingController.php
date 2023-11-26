<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobListing as ResourcesJobListing;
use App\Models\Company;
use App\Models\JobApplicationRequisite;
use App\Models\JobListing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobListingController extends Controller
{
    public function getJobWithCompanyByCategoryIdFromStudentIdFromUserId(string $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Find the student associated with the user
        $student = $user->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        // Retrieve the categories associated with the student
        $categories = $student->categories;

        if ($categories->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Categories not found for the student'], 404);
        }

        // Retrieve the job listings associated with the categories
        $jobs = collect();
        foreach ($categories as $category) {
            $jobs = $jobs->merge($category->jobs()->with('company', 'categories')->get());
        }

        if ($jobs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No job listings found for the categories'], 200);
        }

        return response()->json(['success' => true, 'jobs' => $jobs], 200);
    }

    //
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $categories = $request->input('category');
        $regency = $request->input('regency');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Query job listings based on the title, category, regency, and time range
        $jobs = JobListing::query()
            ->where('title', 'like', "%{$keyword}%")
            ->when($categories, function ($query) use ($categories) {
                $query->whereHas('categories', function ($query) use ($categories) {
                    $query->whereIn('categories.id', $categories);
                });
            })
            ->when($regency, function ($query, $regency) {
                return $query->where('regency', $regency);
            })
            ->when($startTime && $endTime, function ($query) use ($startTime, $endTime) {
                return $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->with('company', 'categories')
            ->paginate(12);

        // Return collection of jobs as a resource
        return new ResourcesJobListing(true, 'List Data Job', $jobs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'required|string',
            'location' => 'required|string',
            'schedule' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'categories' => 'required|array',
            'regency' => 'required|string',
            'district' => 'required|string',
            'province' => 'required',
            'jobtype_id' => 'required'
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $job = JobListing::create([
            'title' => $request->title,
            'company_id' => $request->company_id,
            'description' => $request->description,
            'location' => $request->location,
            'schedule' => $request->schedule,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'regency' => $request->regency,
            'district' => $request->district,
            'province' => $request->province,
            'jobtype_id' => $request->jobtype_id
        ]);

        $categoryIds = $request->input('categories');
        $job->categories()->sync($categoryIds);

        JobApplicationRequisite::create([
            'is_cover_letter' => $request->is_cover_letter,
            'is_transcript' => $request->is_transcript,
            'is_recommendation_letter' => $request->is_recommendation_letter,
            'is_proposal' => $request->is_proposal,
            'is_resume' =>  $request->is_resume,
            'is_health_insurance' =>  $request->is_health_insurance,
            'joblisting_id' => $job->id
        ]);

        return new ResourcesJobListing(true, 'Data job berhasil di tambahkan', $job);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'required|string',
            'location' => 'required|string',
            'schedule' => 'nullable|string',
            'regency' => 'required|string',
            'district' => 'required|string',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'province' => 'required',
            'categories' => 'required|array',
            'jobtype_id' => 'required'
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $job = JobListing::find($id);

        $job->update([
            'title' => $request->title,
            'company_id' => $request->company_id,
            'description' => $request->description,
            'location' => $request->location,
            'schedule' => $request->schedule,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'regency' => $request->regency,
            'district' => $request->district,
            'province' => $request->province,
            'jobtype_id' => $request->jobtype_id
        ]);

        $categoryIds = $request->input('categories');
        $job->categories()->sync($categoryIds);

        $jobApplicationRequisite = JobApplicationRequisite::where('joblisting_id', $job->id);
        $jobApplicationRequisite->update([
            'is_cover_letter' => $request->is_cover_letter,
            'is_transcript' => $request->is_transcript,
            'is_recommendation_letter' => $request->is_recommendation_letter,
            'is_proposal' => $request->is_proposal,
            'is_resume' =>  $request->is_resume,
            'joblisting_id' => $job->id,
            'is_health_insurance' => $request->is_health_insurance
        ]);


        return new ResourcesJobListing(true, 'Data job berhasil di update', $job);
    }

    public function show(string $id)
    {
        $job = JobListing::with('company')->find($id);
        return new ResourcesJobListing(true, 'Detail Data job ', $job);
    }

    public function showJobWithCompanyAndCategories(string $id)
    {
        $job = JobListing::with('company', 'categories')->find($id);
        return new ResourcesJobListing(true, 'job ', $job);
    }

    public function getJobByCompanyId(string $companyId)
    {
        $jobs = JobListing::where('company_id', $companyId)->where('isClosed', false)->get();
        if (!$jobs) {
            return response()->json(['message' => 'Job Not Found'], 404);
        }
        return response()->json(['data' => $jobs], 200);
    }
    public function getJobByUserId(string $userId)
    {
        $company = Company::where('user_id', $userId)->first();
        $jobs = JobListing::where('company_id', $company->id)->get();
        if (!$jobs) {
            return response()->json(['message' => 'Job Not Found'], 404);
        }
        return response()->json(['data' => $jobs], 200);
    }


    public function destroy($id)
    {
        $jobListing = JobListing::findOrFail($id);
        $jobListing->categories()->detach();
        $jobListing->delete();
        return new ResourcesJobListing(true, 'Hapus Data job ', $jobListing);
    }

    public function closeJob(string $id)
    {
        $jobListing = JobListing::findOrFail($id);
        if (!$jobListing) {
            return response()->json(['message' => 'Data jobListing tidak ditemukan'], 404);
        }

        $jobListing->isClosed = true;
        $jobListing->save();
    }

    public function getIsClosedJobCount()
    {
        $closedCount = JobListing::where('isClosed', true)->count();
        $openCount = JobListing::where('isClosed', false)->count();
        return response()->json(['closedCount' => $closedCount, 'openCount' => $openCount], 200);
    }

    public function jobListingsPerMonth()
    {
        $currentYear = Carbon::now()->year;
        $jobListings = JobListing::selectRaw('MONTHNAME(created_at) AS month, COUNT(*) AS count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = $jobListings->pluck('month')->toArray();
        $counts = $jobListings->pluck('count')->toArray();

        $data = [
            'month' => $months,
            'count' => $counts
        ];

        return response()->json([$data]);
    }


    public function jobListingsReport()
    {
        $currentYear = Carbon::now()->year;
        $jobListings = JobListing::selectRaw('MONTHNAME(joblistings.created_at) AS month, COUNT(*) AS count, categories.name AS category')
            ->join('joblistings_category', 'joblistings.id', '=', 'joblistings_category.joblistings_id')
            ->join('categories', 'joblistings_category.category_id', '=', 'categories.id')
            ->whereYear('joblistings.created_at', $currentYear)
            ->groupBy('month', 'category')
            ->orderByRaw('MONTH(joblistings.created_at)')
            ->get();

        $months = $jobListings->pluck('month')->unique()->toArray();
        $categories = $jobListings->pluck('category')->unique()->toArray();

        $data = [];

        foreach ($categories as $category) {
            $countPerMonth = [];

            foreach ($months as $month) {
                $count = $jobListings->where('category', $category)
                    ->where('month', $month)
                    ->pluck('count')
                    ->first() ?? 0;

                $countPerMonth[] = $count;
            }

            $data[] = [
                'category' => $category,
                'count' => $countPerMonth
            ];
        }

        return response()->json($data);
    }

    public function getOpenJobListingsCountPerMonth()
    {
        $startDate = Carbon::now()->startOfYear(); // Start from the beginning of the year
        $endDate = Carbon::now(); // End at the current date

        $jobListings = JobListing::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('isClosed', false) // Filter by open job listings
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = [];
        $counts = [];
        foreach ($jobListings as $jobListing) {
            $months[] = $jobListing->month;
            $counts[] = $jobListing->count;
        }

        $result = [
            'month' => $months,
            'count' => $counts,
        ];

        return response()->json([$result]);
    }
    public function getCloseJobListingsCountPerMonth()
    {
        $startDate = Carbon::now()->startOfYear(); // Start from the beginning of the year
        $endDate = Carbon::now(); // End at the current date

        $jobListings = JobListing::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('isClosed', true) // Filter by open job listings
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $months = [];
        $counts = [];
        foreach ($jobListings as $jobListing) {
            $months[] = $jobListing->month;
            $counts[] = $jobListing->count;
        }

        $result = [
            'month' => $months,
            'count' => $counts,
        ];

        return response()->json([$result]);
    }

    public function getJoblistingPerMonth()
    {
        $jobListings = JobListing::selectRaw('MONTHNAME(joblistings.created_at) AS month, companies.name AS company_name, joblistings.title, joblistings.isClosed')
            ->join('companies', 'joblistings.company_id', '=', 'companies.id')
            ->orderBy('joblistings.created_at')
            ->get();

        $result = [];
        foreach ($jobListings as $jobListing) {
            $result[] = [
                'month' => $jobListing->month,
                'company_name' => $jobListing->company_name,
                'title' => $jobListing->title,
                'status' => $jobListing->isClosed ? 'Closed' : 'Open',
            ];
        }

        return response()->json($result);
    }
}
