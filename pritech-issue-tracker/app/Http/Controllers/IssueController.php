<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;

class IssueController extends Controller
{
    public function index()
    {
        $issues = Issue::with(['project', 'tags'])
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('priority'), fn($q) => $q->where('priority', request('priority')))
            ->when(request('tag_id'), fn($q) => $q->whereHas('tags', fn($q) => $q->where('tags.id', request('tag_id'))))
            ->when(request('search'), fn($q) => $q->where(fn($q) => $q
                ->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('description', 'like', '%' . request('search') . '%')
            ))
            ->get();

        if (request()->wantsJson()) {
            return response()->json($issues);
        }

        $tags = Tag::all();
        return view('issues.index', compact('issues', 'tags'));
    }

    public function create()
    {
        $projects = Project::all();
        $tags     = Tag::all();
        return view('issues.create', compact('projects', 'tags'));
    }

    public function store(StoreIssueRequest $request)
    {
        $issue = Issue::create($request->validated());
        return redirect()->route('issues.show', $issue)->with('success', 'Issue created successfully.');
    }

    public function show(Issue $issue)
    {
        $issue->load(['project', 'tags', 'users']);
        $users = User::all();
        $tags  = Tag::all();
        return view('issues.show', compact('issue', 'users', 'tags'));
    }

    public function edit(Issue $issue)
    {
        $projects = Project::all();
        $tags     = Tag::all();
        return view('issues.edit', compact('issue', 'projects', 'tags'));
    }

    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        $issue->update($request->validated());
        return redirect()->route('issues.show', $issue)->with('success', 'Issue updated successfully.');
    }

    public function destroy(Issue $issue)
    {
        $issue->delete();
        return redirect()->route('issues.index')->with('success', 'Issue deleted successfully.');
    }

    public function attachTag(Issue $issue, Tag $tag)
    {
        $issue->tags()->syncWithoutDetaching([$tag->id]);
        return response()->json(['attached' => true, 'tag' => $tag]);
    }

    public function detachTag(Issue $issue, Tag $tag)
    {
        $issue->tags()->detach($tag->id);
        return response()->json(['attached' => false, 'tag' => $tag]);
    }

    public function assignUser(Issue $issue, User $user)
    {
        $issue->users()->syncWithoutDetaching([$user->id]);
        return response()->json(['assigned' => true, 'user' => $user->only(['id', 'name'])]);
    }

    public function unassignUser(Issue $issue, User $user)
    {
        $issue->users()->detach($user->id);
        return response()->json(['assigned' => false, 'user' => $user->only(['id', 'name'])]);
    }
}
