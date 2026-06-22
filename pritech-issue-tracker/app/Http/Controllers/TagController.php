<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request)
    {
        Tag::create($request->validated());
        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }
}
