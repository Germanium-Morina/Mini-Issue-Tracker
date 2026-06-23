@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Projects</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">New Project</a>
    </div>

    @forelse($projects as $project)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $project->name }}</h5>
                <p class="card-text">{{ $project->description }}</p>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-info">View</a>
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
    @empty
        <p>No projects yet.</p>
    @endforelse
@endsection
