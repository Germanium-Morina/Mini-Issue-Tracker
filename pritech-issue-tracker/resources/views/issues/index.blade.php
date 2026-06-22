@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Issues</h1>
        <a href="{{ route('issues.create') }}" class="btn btn-primary">New Issue</a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('issues.index') }}" class="row g-2 mb-4">
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="priority" class="form-select">
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="tag_id" class="form-select">
                <option value="">All Tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-secondary">Filter</button>
            <a href="{{ route('issues.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    {{-- Issues List --}}
    @forelse($issues as $issue)
        <div class="card mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('issues.show', $issue) }}">{{ $issue->title }}</a>
                    <div>
                        <span class="badge bg-secondary">{{ $issue->status }}</span>
                        <span class="badge bg-info">{{ $issue->priority }}</span>
                    </div>
                </div>
                <small class="text-muted">{{ $issue->project->name }}</small>
                <div class="mt-1">
                    @foreach($issue->tags as $tag)
                        <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <p>No issues found.</p>
    @endforelse
@endsection
