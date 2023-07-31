@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <h6 class="display-6">Affiliates List</h6>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Distance</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($affiliates) > 0)
                    @foreach($affiliates as $affiliate)
                    <tr>
                        <td>{{ $affiliate['affiliate_id'] }}</td>
                        <td>{{ $affiliate['name'] }}</td>
                        <td>{{ $affiliate['distance'] }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="3">No matching affiliates found within 100km.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection