@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Upload Excel File to Add Users</h2>
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ url('/upload-excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Choose Excel File</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload Excel</button>
        </form>
    </div>
@endsection
