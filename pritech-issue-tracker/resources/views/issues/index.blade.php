@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Issues</h1>
        <a href="{{ route('issues.create') }}" class="btn btn-primary">New Issue</a>
    </div>

    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Search issues..." value="{{ request('search') }}">
    </div>

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

    <div id="issues-list">
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
    </div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('search');
    let debounceTimer;

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = searchInput.value.trim();
            fetch(`/issues?search=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(issues => {
                const list = document.getElementById('issues-list');
                if (issues.length === 0) {
                    list.innerHTML = '<p>No issues found.</p>';
                    return;
                }
                list.innerHTML = issues.map(issue => `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="/issues/${issue.id}">${issue.title}</a>
                                <div>
                                    <span class="badge bg-secondary">${issue.status}</span>
                                    <span class="badge bg-info">${issue.priority}</span>
                                </div>
                            </div>
                            <small class="text-muted">${issue.project.name}</small>
                        </div>
                    </div>
                `).join('');
            });
        }, 300);
    });
</script>
@endpush
