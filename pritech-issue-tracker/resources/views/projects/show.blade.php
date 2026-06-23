@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $project->name }}</h1>
        @can('update', $project)
        <div>
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>
        </div>
        @endcan
    </div>

    <p>{{ $project->description }}</p>

    @if($project->start_date || $project->deadline)
        <p class="text-muted">
            {{ $project->start_date }} — {{ $project->deadline }}
        </p>
    @endif

    <hr>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Issues</h3>
        <a href="{{ route('issues.create') }}" class="btn btn-primary btn-sm">New Issue</a>
    </div>

    @forelse($project->issues as $issue)
        <div class="card mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('issues.show', $issue) }}">{{ $issue->title }}</a>
                    <div>
                        <span class="badge bg-secondary">{{ $issue->status }}</span>
                        <span class="badge bg-info">{{ $issue->priority }}</span>
                    </div>
                </div>
                <div class="mt-1">
                    @foreach($issue->tags as $tag)
                        <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <p>No issues yet.</p>
    @endforelse

    <a href="{{ route('projects.index') }}" class="btn btn-secondary mt-3">Back</a>
@endsection
