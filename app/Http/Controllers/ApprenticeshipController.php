<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use Illuminate\Http\Request;

class ApprenticeshipController extends Controller
{
    public function index(Request $request)
    {
        $partTimeJobListings = $this->filterJobs($request);

        // You can return the data or pass it to a view
        return response()->json($partTimeJobListings);
    }


    public function filterJobs(Request $request)
    {
        $keyword = $request->input('keyword');
        $province = $request->input('province');
        $regency = $request->input('regency');
        $district = $request->input('district');
        $category = $request->input('category');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Query job listings with the part-time job type and apply filters
        $query = JobListing::with(['company', 'categories'])
            ->whereHas('jobtype', function ($query) {
                $query->where('name', 'magang');
            })
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', '%' . $keyword . '%');
            })
            ->when($province, function ($query, $province) {
                return $query->where('province', $province);
            })
            ->when($regency, function ($query, $regency) {
                return $query->where('regency', $regency);
            })
            ->when($district, function ($query, $district) {
                return $query->where('district', $district);
            })
            ->when($category, function ($query, $category) {
                return $query->whereHas('categories', function ($categoryQuery) use ($category) {
                    $categoryQuery->where('name', $category);
                });
            })
            ->when($startTime, function ($query, $startTime) {
                return $query->where('start_time', '>=', $startTime);
            })
            ->when($endTime, function ($query, $endTime) {
                return $query->where('end_time', '<=', $endTime);
            });

        // Execute the query and get the results
        $partTimeJobListings = $query->get();

        return $partTimeJobListings;
    }
}
