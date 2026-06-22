<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $issues = Issue::with(['project', 'tags'])
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('priority'), fn($q) => $q->where('priority', request('priority')))
            ->when(request('tag_id'), fn($q) => $q->whereHas('tags', fn($q) => $q->where('tags.id', request('tag_id'))))
            ->get();
        $tags = Tag::all();
        return view('issues.index', compact('issues', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $tags     = Tag::all();
        return view('issues.create', compact('projects', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIssueRequest $request)
    {
        $issue = Issue::create($request->validated());
        return redirect()->route('issues.show', $issue)->with('success', 'Issue created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issue)
    {
        $issue->load(['project', 'tags', 'comments']);
        return view('issues.show', compact('issue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Issue $issue)
    {
        $projects = Project::all();
        $tags     = Tag::all();
        return view('issues.edit', compact('issue', 'projects', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        $issue->update($request->validated());
        return redirect()->route('issues.show', $issue)->with('success', 'Issue updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
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
}
