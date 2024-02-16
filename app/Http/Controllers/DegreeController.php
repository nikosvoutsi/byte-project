<?php

namespace App\Http\Controllers;


use App\Models\JobAppliedFor;
use App\Models\Degree;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class DegreeController extends Controller
{
    public function index()
{
    
    // Fetch all degrees from the database
    $degrees = Degree::paginate(5);

    // Pass the degrees data to the view
    return view('degrees', compact('degrees'));
}

public function store(Request $request)
{
    try {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'degreeTitle' => 'required|string|max:255'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        // Create a new degree instance
        $degree = new Degree();
        $degree->degreeTitle = $request->input('degreeTitle');
       

        // Save the degree
        $degree->save();


        // Redirect back with a success message
        return redirect()->back()->with('success', 'Degree added successfully.');
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error in storing degree: ' . $e->getMessage());
        
        // Redirect back with an error message
        return redirect()->back()->with('error', 'Failed to add degree. Please try again later.');
    }
}

public function update(Request $request, $id)
    {
        // Define validation rules
        $rules = [
            'degreeTitle' => 'required|string|max:255',
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
            $degree = Degree::findOrFail($id);

            // Update the candidate attributes
            $degree->update([
                'degreeTitle' => $request->degreeTitle,
            ]);

            // Redirect back or to a specific route after updating
            return redirect()->route('degrees.index')->with('success', 'Degree updated successfully');
        } catch (QueryException $e) {
            // Check if the exception is due to mobile number length
            if (strpos($e->getMessage(), 'Data too long for column \'mobile\'') !== false || strlen($request->mobile) > 10) {
                return redirect()->back()->withInput()->withErrors(['mobile' => 'Invalid mobile number. Please provide a valid mobile number with a maximum of 10 characters.']);
            }

            // Redirect back with a generic error message
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while updating the candidate. Please try again.']);
        }
    }

public function delete($id)
{
    try {
        $degree = Degree::findOrFail($id);

        // Check if there are candidates associated with the degree
        $candidatesCount = Candidate::where('degree_id', $id)->count();
        if ($candidatesCount > 0) {
            return redirect()->route('degrees.index')->with('error', 'Cannot delete the degree because it is associated with candidates.');
        }

        // If no associated candidates, proceed with deletion
        $degree->delete();

        return redirect()->route('degrees.index')->with('success', 'The degree ' . $degree->degreeTitle .  ' deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->withErrors(['error' => 'Failed to delete the degree.']);
    }
}


}
