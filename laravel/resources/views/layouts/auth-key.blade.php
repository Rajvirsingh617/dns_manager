@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Authentication Key</h1>

    <div class="alert alert-info">
        <p><strong>Auth Key:</strong></p>
        <pre>{{ $authKey }}</pre>
    </div>

    <form action="{{ route('auth.key.regenerate') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-warning">Regenerate Auth Key</button>
    </form>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection
