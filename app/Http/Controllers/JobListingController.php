<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobListing as ResourcesJobListing;
use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobListingController extends Controller
{
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
            'categories' => 'required|array'
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
        $job = JobListing::find($id);

        return new ResourcesJobListing(true, 'Detail Data job ', $job);
    }

    public function destroy(string $id)
    {
        $job = JobListing::find($id);

        $job->delete();
        return new ResourcesJobListing(true, 'Hapus Data job ', $job);
    }
}
