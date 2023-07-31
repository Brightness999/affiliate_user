@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Choose a file') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('processUpload') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <input type="file" name="affiliates_file" class="form-control" id="fileInput" accept=".txt" aria-describedby="fileHelp">
                            <div id="fileHelp" class="form-text">Please select a file to upload.</div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" id="submit">Upload and Process</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection