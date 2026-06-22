@extends('layouts.app')

@section('content')
    {{-- Issue Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $issue->title }}</h1>
        <div>
            <a href="{{ route('issues.edit', $issue) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('issues.destroy', $issue) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    {{-- Issue Details --}}
    <div class="mb-3">
        <span class="badge bg-secondary">{{ $issue->status }}</span>
        <span class="badge bg-info">{{ $issue->priority }}</span>
        @if($issue->due_date)
            <span class="text-muted ms-2">Due: {{ $issue->due_date }}</span>
        @endif
        <span class="text-muted ms-2">Project: <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a></span>
    </div>

    <p>{{ $issue->description }}</p>

    <hr>

    {{-- Tags Section --}}
    <h4>Tags</h4>
    <div class="mb-3" id="attached-tag-badges">
        @foreach($issue->tags as $tag)
            <span class="badge me-1" id="badge-tag-{{ $tag->id }}" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
        @endforeach
    </div>

    <div class="mb-4">
        <strong>Attach / Detach Tags:</strong><br>
        @php $attachedIds = $issue->tags->pluck('id')->toArray(); @endphp
        @foreach(\App\Models\Tag::all() as $tag)
            <button
                class="btn btn-sm me-1 mt-1 tag-toggle {{ in_array($tag->id, $attachedIds) ? 'btn-success' : 'btn-outline-secondary' }}"
                data-attach-url="{{ route('issues.tags.attach', [$issue, $tag]) }}"
                data-detach-url="{{ route('issues.tags.detach', [$issue, $tag]) }}"
                data-attached="{{ in_array($tag->id, $attachedIds) ? '1' : '0' }}"
                style="border-color: {{ $tag->color }}">
                {{ $tag->name }}
            </button>
        @endforeach
    </div>

    <hr>

    {{-- Comments Section --}}
    <h4>Comments</h4>

    {{-- Add Comment Form --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6>Add a Comment</h6>
            <div id="comment-errors" class="mb-2"></div>
            <div class="mb-2">
                <input type="text" id="author_name" class="form-control" placeholder="Your name">
            </div>
            <div class="mb-2">
                <textarea id="body" class="form-control" placeholder="Write a comment..." rows="3"></textarea>
            </div>
            <button id="submit-comment" class="btn btn-primary">Submit</button>
        </div>
    </div>

    <div id="comments-list"></div>

    <button id="load-more" class="btn btn-outline-secondary mt-2" style="display:none">Load More</button>
@endsection

@push('scripts')
<script>
    const issueId = {{ $issue->id }};
    const csrfToken = document.querySelector('meta[name=csrf-token]').content;
    let nextPageUrl = `/issues/${issueId}/comments?page=1`;

    // Load first page on page load
    loadComments();

    function loadComments() {
        if (!nextPageUrl) return;

        fetch(nextPageUrl, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                data.data.forEach(comment => appendComment(comment));
                nextPageUrl = data.next_page_url;
                document.getElementById('load-more').style.display = nextPageUrl ? 'block' : 'none';
            });
    }

    // Load more uses next_page_url directly from Laravel
    document.getElementById('load-more').addEventListener('click', loadComments);

    // Submit comment via AJAX
    document.getElementById('submit-comment').addEventListener('click', () => {
        const author_name = document.getElementById('author_name').value;
        const body = document.getElementById('body').value;

        fetch(`/issues/${issueId}/comments`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ author_name, body })
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            if (status === 201) {
                prependComment(data);
                document.getElementById('author_name').value = '';
                document.getElementById('body').value = '';
                document.getElementById('comment-errors').innerHTML = '';
            } else {
                let html = '<ul class="text-danger mb-0">';
                Object.values(data.errors).forEach(errors => errors.forEach(e => html += `<li>${e}</li>`));
                html += '</ul>';
                document.getElementById('comment-errors').innerHTML = html;
            }
        });
    });

    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function commentHtml(comment) {
        return `<div class="card mb-2">
                    <div class="card-body">
                        <strong>${comment.author_name}</strong>
                        <small class="text-muted ms-2">${formatDate(comment.created_at)}</small>
                        <p class="mb-0 mt-1">${comment.body}</p>
                    </div>
                </div>`;
    }

    function appendComment(comment) {
        document.getElementById('comments-list').insertAdjacentHTML('beforeend', commentHtml(comment));
    }

    function prependComment(comment) {
        document.getElementById('comments-list').insertAdjacentHTML('afterbegin', commentHtml(comment));
    }

    // Tag attach/detach
    document.querySelectorAll('.tag-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const attached = btn.dataset.attached === '1';
            const url = attached ? btn.dataset.detachUrl : btn.dataset.attachUrl;

            fetch(url, {
                method: attached ? 'DELETE' : 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(r => r.json())
            .then(data => {
                btn.dataset.attached = data.attached ? '1' : '0';
                btn.classList.toggle('btn-success', data.attached);
                btn.classList.toggle('btn-outline-secondary', !data.attached);

                // Update the tag badges display at the top
                const badgesContainer = document.getElementById('attached-tag-badges');
                if (data.attached) {
                    badgesContainer.insertAdjacentHTML('beforeend',
                        `<span class="badge me-1" id="badge-tag-${data.tag.id}" style="background-color:${data.tag.color}">${data.tag.name}</span>`);
                } else {
                    const badge = document.getElementById(`badge-tag-${data.tag.id}`);
                    if (badge) badge.remove();
                }
            });
        });
    });
</script>
@endpush
