@extends('layouts.app')

@section('content')
    <form action="/projects" method="POST">
        @csrf

        <h1>Create a project</h1>

        @include('projects._form', [
            'project' => new \App\Project,
            'buttonText' => 'Create',
        ])
    </form>
@endsection
