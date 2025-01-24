@extends('layouts.app')

@section('content')
<div id="page-wrapper">
    <div class="main-page">
        <h2 class="title1 text-primary">Commit Changes</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <p class="text-success font-weight-bold">
                    The changes have been committed successfully.
                </p>
                <div class="text-center mt-4">
                    <a href="dashboard" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
