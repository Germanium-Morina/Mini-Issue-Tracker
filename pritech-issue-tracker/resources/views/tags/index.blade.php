@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Tags</h1>

    <div class="row">
        {{-- Create Tag Form --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5>New Tag</h5>
                    <form action="{{ route('tags.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror" value="{{ old('color', '#6c757d') }}">
                            @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Create Tag</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tags List --}}
        <div class="col-md-8">
            <div class="d-flex flex-wrap gap-2">
                @forelse($tags as $tag)
                    <span class="badge fs-6 p-2" style="background-color: {{ $tag->color }}">
                        {{ $tag->name }}
                    </span>
                @empty
                    <p>No tags yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
