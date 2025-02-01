@extends('layouts.app')

@section('content')
<div id="page-wrapper">
    <div class="main-page">
        <h2 class="title1 text-primary">Commit Changes</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <p class="font-weight-bold {{ isset($message) && str_contains($message, 'Failed') ? 'text-danger' : 'text-success' }}">
                    {{ $message ?? 'Processing your request...' }}
                </p>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
