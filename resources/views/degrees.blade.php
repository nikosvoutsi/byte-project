@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Degrees</h1><br><br>

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

    <!-- Button trigger modal -->
    <button type="button" class="btn add-candidate" data-toggle="modal" data-target="#addDegreeModal" style="margin-left:86%">
        <img src="{{ asset('images/plus-circle.png') }}" alt="Add">  Add Degree
    </button><br>

    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ΙD</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($degrees as $degree)
            <tr>
                <td>{{ $degree->id }}</td>
                <td>{{ $degree->degreeTitle }}</td>
                <td>
                    <div class="btn-group">
                       
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal{{$degree->id}}">
                                <img src="{{ asset('images/edit.svg') }}" alt="Edit">
                            </button>
                        
                            <!-- Delete Button -->
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{$degree->id}}">
                                <img src="{{ asset('images/ui-delete.svg') }}" alt="Delete">
                            </button>
                        
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

     <!-- Display pagination links -->
     {{ $degrees->links() }}


</div>

<!-- Edit Modals -->
@foreach ($degrees as $degree)
<div class="modal fade" id="editModal{{$degree->id}}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{$degree->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content  small-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{$degree->id}}">Edit Degree</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add your form fields here -->
                <form action="{{ route('degrees.edit', ['id' => $degree->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="degreeTitle">Degree Title</label>
                        <input type="text" class="form-control" id="degreeTitle" name="degreeTitle" value="{{ $degree->degreeTitle }}">
                    </div>
                    

                    <!-- Add other form fields here -->
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
@foreach ($degrees as $degree)
<div class="modal fade" id="deleteModal{{$degree->id}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{$degree->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content small-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{$degree->id}}">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the degree {{ $degree->degreeTitle }}?
            </div>
            <div class="modal-footer">
                <form action="{{ route('degree.delete', ['id' => $degree->id]) }}" method="POST">
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


<!-- Add Degree Modal -->
<div class="modal fade" id="addDegreeModal" tabindex="-1" role="dialog" aria-labelledby="addDegreeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content small-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="addDegreeModalLabel
                ">Add Degree</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add Degree Form -->
                <form action="{{ route('degree.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Add your form fields here -->
                    <!-- Example fields, customize as needed -->
                    <div class="form-group">
                        <label for="firstname">Degree Title</label>
                        <input type="text" class="form-control" id="degreeTitle" name="degreeTitle" required>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn submit">Add Degree</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
