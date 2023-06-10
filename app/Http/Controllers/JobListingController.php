<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobListing as ResourcesJobListing;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Http\Request;
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
    public function index()
    {
        $jobs = JobListing::latest()->paginate(5);

        //return collection of jobs as a resource
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
            'district' => 'required|string'
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
            'district' => $request->district
        ]);

        $categoryIds = $request->input('categories');
        $job->categories()->sync($categoryIds);

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
            'categories' => 'required|array'
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
        ]);

        $categoryIds = $request->input('categories');
        $job->categories()->sync($categoryIds);

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

    public function destroy($id)
    {
        $jobListing = JobListing::findOrFail($id);

        // Menghapus relasi dengan kategori
        $jobListing->categories()->detach();

        // Menghapus job listing
        $jobListing->delete();
        return new ResourcesJobListing(true, 'Hapus Data job ', $jobListing);
    }
}
