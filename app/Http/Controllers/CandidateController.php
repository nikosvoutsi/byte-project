<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobAppliedFor;
use App\Models\Degree;
use App\Models\Job;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        // Get the selected job IDs from the request
        $jobIds = $request->input('jobs', []);

        if($jobIds){
             // Query candidates based on selected jobs
            $candidates = Candidate::with('degree')->orderBy('updated_at', 'desc')->whereHas('jobApplications', function ($query) use ($jobIds) {
             $query->whereIn('job_id', $jobIds);
           })->paginate(5);
        }else{
            // Fetch all candidates from the database
            $candidates = Candidate::with('degree')->orderBy('updated_at', 'desc')->paginate(5);
        }

             
        $degrees = Degree::all();
        $jobs = Job::all();

        // Pass the candidates data to the view
        return view('candidates', compact('candidates', 'degrees', 'jobs'));
    }

    public function update(Request $request, $id)
    {
        // Define validation rules
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'nullable|string|max:10',
            'applicationDate' => 'nullable|date',
            'resume' => 'nullable|file|max:10240|mimes:pdf,doc,docx',
            'degree' => 'required|exists:degrees,id',
            'jobs.*' => 'required|exists:jobs,id',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Find the candidate by ID
            $candidate = Candidate::findOrFail($id);

            // Update the candidate attributes
            $candidate->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'degree_id' => $request->degree,
            ]);

            // Convert the application date format and update
            $applicationDate = $request->get('applicationDate');
            $candidate->update([
                'applicationDate' => $applicationDate,
            ]);

            // Delete previous resume files
            $candidateFolder = 'resumes/' . $id;
            if (Storage::exists($candidateFolder)) {
                Storage::deleteDirectory($candidateFolder);
            }

            // Store new resume file
            if ($request->hasFile('resume')) {
                $resumeName = $request->file('resume')->getClientOriginalName();
                $request->file('resume')->storeAs($candidateFolder, $resumeName);
                $candidate->update(['resume' => $resumeName]);
            }

            // Sync job applications
            $this->syncJobApplications($candidate, $request->jobs);

            // Redirect back or to a specific route after updating
            return redirect()->route('index')->with('success', 'Candidate updated successfully');
        } catch (QueryException $e) {
            // Check if the exception is due to mobile number length
            if (strpos($e->getMessage(), 'Data too long for column \'mobile\'') !== false || strlen($request->mobile) > 10) {
                return redirect()->back()->withInput()->withErrors(['mobile' => 'Invalid mobile number. Please provide a valid mobile number with a maximum of 10 characters.']);
            }

            // Redirect back with a generic error message
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while updating the candidate. Please try again.']);
        }
    }

    private function syncJobApplications($candidate, $jobIds)
    {
        // Get the current job application IDs
        $currentJobIds = $candidate->jobApplications()->pluck('job_id')->toArray();

        // Determine job IDs to be attached (new)
        $newJobIds = array_diff($jobIds, $currentJobIds);

        // Determine job IDs to be detached (removed)
        $removedJobIds = array_diff($currentJobIds, $jobIds);

        // Detach removed job applications
        $candidate->jobApplications()->whereIn('job_id', $removedJobIds)->delete();

        // Attach new job applications
        foreach ($newJobIds as $jobId) {
            $candidate->jobApplications()->create(['job_id' => $jobId]);
        }
    }

    public function downloadResume($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidateFolder = 'resumes/' . $candidate->id;
        $resumePath = storage_path('app/' . $candidateFolder . '/' . $candidate->resume);

        if (Storage::exists($candidateFolder . '/' . $candidate->resume)) {
            return response()->download($resumePath, $candidate->resume);
        } else {
            return back()->with('error', 'Resume not found.');
        }
    }

    public function delete($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $candidate->delete();

            return redirect()->route('index')->with('success', 'The candidate ' . $candidate->name . ' ' . $candidate->surname . ' deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('index')->with('error', 'Failed to delete the candidate.');
        }
    }

    public function store(Request $request)
{
    try {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:candidates,email',
            'mobile' => 'nullable|string|max:10',
            'applicationDate' => 'nullable|date',
            'resume' => 'nullable|file|max:10240|mimes:pdf,doc,docx',
            'degree' => 'required|exists:degrees,id',
            'jobs' => 'required|array',
            'jobs.*' => 'required|exists:jobs,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create a new candidate instance
        $candidate = new Candidate();
        $candidate->firstname = $request->input('firstname');
        $candidate->lastname = $request->input('lastname');
        $candidate->email = $request->input('email');
        $candidate->mobile = $request->input('mobile');
        $candidate->applicationDate = $request->input('applicationDate');
        
        // Associate degree if provided
        if ($request->filled('degree')) {
            $candidate->degree_id = $request->input('degree');
        }

        // Save the candidate
        $candidate->save();

        // Delete previous resume files
        $candidateFolder = 'resumes/' . $candidate->id;
        if (Storage::exists($candidateFolder)) {
            Storage::deleteDirectory($candidateFolder);
        }

        // Store new resume file
        if ($request->hasFile('resume')) {
            $resumeName = $request->file('resume')->getClientOriginalName();
            $request->file('resume')->storeAs($candidateFolder, $resumeName);
            $candidate->update(['resume' => $resumeName]);
        }

        // Sync job applications
        $this->syncJobApplications($candidate, $request->input('jobs'));

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Candidate added successfully.');
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error in storing candidate: ' . $e->getMessage());
        
        // Redirect back with an error message
        return redirect()->back()->with('error', 'Failed to add candidate. Please try again later.');
    }
}

    
}

