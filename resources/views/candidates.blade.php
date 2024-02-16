@extends('layouts.app')

@section('content')
    <h1>Candidates</h1><br><br>

    <!-- Display success and error messages -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- Button to open modal -->
<button id="filters-button" type="button" title="Φίλτρα" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filters">
    <img src="{{ asset('images/filter-st.jpg') }}" width="18">
</button>
    <br>

    <!-- Button trigger modal -->
    <button type="button" class="btn add-candidate" data-toggle="modal" data-target="#addCandidateModal" style="margin-left:86%">
        <img src="{{ asset('images/plus-circle.png') }}" alt="Add">  Add Candidate
    </button><br>

    <table class="table table-striped table-shadow">
        <thead class="thead-dark">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Degree</th>
                <th>Resume</th>
                <th>Job Applied For</th>
                <th>Application Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($candidates as $candidate)
            <tr>
                <td>{{ $candidate->firstname }}</td>
                <td>{{ $candidate->lastname }}</td>
                <td>{{ $candidate->email }}</td>
                <td>{{ $candidate->mobile }}</td>
                <td>{{ $candidate->degree->degreeTitle ?? '-' }}</td>
                <td>
                    @if ($candidate->resume)
                    @php
                    $candidateFolder = 'resumes/' . $candidate->id;
                    $resumePath = storage_path('app/' . $candidateFolder . '/' . $candidate->resume);
                    @endphp
                    <a href="{{ route('download.resume', ['id' => $candidate->id]) }}" download="{{ $candidate->resume }}">{{ $candidate->resume }}</a>
                    @else
                    No resume
                    @endif
                </td>
                <td>
                    @php $count = count($candidate->jobApplications); @endphp
                    @foreach ($candidate->jobApplications as $key => $application)
                        @if ($key != 0)
                            , 
                        @endif
                        {{ $application->job->title }}
                    @endforeach
                </td>
                
                
                <td>{{ $candidate->applicationDate }}</td>
                <td>
                    <div class="btn-group">
                       
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal{{$candidate->id}}">
                                <img src="{{ asset('images/edit.svg') }}" alt="Edit">
                            </button>
                        
                            <!-- Delete Button -->
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{$candidate->id}}">
                                <img src="{{ asset('images/ui-delete.svg') }}" alt="Delete">
                            </button>
                        
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

     <!-- Display pagination links -->
{{ $candidates->appends(request()->query())->links() }}

     <!-- Filter Modal -->
<div class="modal fade" id="filters" tabindex="-1" aria-labelledby="filtersLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content small-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="filtersLabel">Search Candidates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                
            </div>
            <div class="modal-body">
                <!-- Search Form -->
                <form action="{{ route('index') }}" method="GET">
                    <div class="form-group">
                        <label for="jobs">Select Jobs:</label>
                        <select class="form-control" id="jobs" name="jobs[]" multiple>
                            @foreach ($jobs as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn search">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Modals -->
    @foreach ($candidates as $candidate)
    <div class="modal fade" id="editModal{{$candidate->id}}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{$candidate->id}}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{$candidate->id}}">Edit Candidate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Add your form fields here -->
                    <form action="{{ route('candidates.edit', ['id' => $candidate->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="{{ $candidate->firstname }}" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $candidate->lastname }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ $candidate->email }}" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $candidate->mobile }}">
                        </div>
                        <div class="form-group">
                            <label for="resume">Resume</label>
                            <input type="file" class="form-control" id="resume" name="resume">
                            @if ($candidate->resume)
                            <p>Current Resume: {{ $candidate->resume }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="applicationDate">Application date</label>
                            <input type="date" class="form-control" id="applicationDate" name="applicationDate" value="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $candidate->applicationDate)->format('Y-m-d') }}">
                        </div>

                        <!-- Jobs dropdown -->
                        <div class="form-group">
                            <label for="jobs">Jobs Applied For</label>
                            <select class="form-control" id="jobs" name="jobs[]" multiple required>
                                @foreach ($jobs as $job)
                                <option value="{{ $job->id }}" @if (in_array($job->id, $candidate->jobApplications->pluck('job_id')->toArray())) selected @endif>{{ $job->title }}</option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Degree dropdown -->
                        <div class="form-group">
                            <label for="degree">Degree</label>
                            <select class="form-control" id="degree" name="degree" required>
                                <option value="">Please choose</option>
                                @foreach ($degrees as $degree)
                                <option value="{{ $degree->id }}" {{ $degree->id == $candidate->degree_id ? 'selected' : '' }}>{{ $degree->degreeTitle }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn submit">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Delete Modal -->
    @foreach ($candidates as $candidate)
    <div class="modal fade" id="deleteModal{{$candidate->id}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{$candidate->id}}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content small-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel{{$candidate->id}}">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the candidate {{ $candidate->name }} {{ $candidate->surname }}?
                </div>
                <div class="modal-footer">
                    <form action="{{ route('candidates.delete', ['id' => $candidate->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    
<!-- Add Candidate Modal -->
<div class="modal fade" id="addCandidateModal" tabindex="-1" role="dialog" aria-labelledby="addCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCandidateModalLabel">Add Candidate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add Candidate Form -->
                <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Add your form fields here -->
                    <!-- Example fields, customize as needed -->
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile">
                    </div>
                    <!-- Resume upload -->
                    <div class="form-group">
                        <label for="resume">Resume</label>
                        <input type="file" class="form-control-file" id="resume" name="resume">
                    </div>
                    <div class="form-group">
                        <label for="applicationDate">Application Date</label>
                        <input type="date" class="form-control" id="applicationDate" name="applicationDate">
                    </div>
                    
                    <!-- Jobs dropdown -->
                    <div class="form-group">
                        <label for="jobs">Jobs Applied For</label>
                        <select class="form-control" id="jobs" name="jobs[]" multiple required>
                            @foreach ($jobs as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Degree dropdown -->
                    <div class="form-group">
                        <label for="degree">Degree</label>
                        <select class="form-control" id="degree" name="degree" required>
                            <option value="">Please choose</option>
                            @foreach ($degrees as $degree)
                            <option value="{{ $degree->id }}">{{ $degree->degreeTitle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn submit">Add Candidate</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to update job selection based on URL query parameters -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get the current URL query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const selectedJobs = urlParams.getAll("jobs[]");

        // Update the job selection in the dropdown
        const jobDropdown = document.getElementById("jobs");
        if (selectedJobs.length > 0) {
            selectedJobs.forEach((jobId) => {
                const option = jobDropdown.querySelector(`option[value="${jobId}"]`);
                if (option) {
                    option.selected = true;
                }
            });
        }
    });

    // Event handling for the filter button
    document.getElementById("filters-button").addEventListener("click", function () {
        // Add your custom logic here if needed
    });
</script>


@endsection
