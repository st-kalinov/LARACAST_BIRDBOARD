@extends('layouts.app')

@section('content')
    <form action="{{ $project->path() }}" method="POST">
        @csrf
        @method('PATCH')

        <h1>Edit the project</h1>

        @include('projects._form', [
            'buttonText' => 'Update'
        ])
    </form>
@endsection
