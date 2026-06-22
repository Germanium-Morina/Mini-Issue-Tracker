<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;

Route::get('/', fn() => redirect()->route('projects.index'));

Route::resource('projects', ProjectController::class);
Route::resource('issues', IssueController::class);
Route::resource('tags', TagController::class)->only(['index', 'store']);


Route::post('issues/{issue}/tags/{tag}/attach', [IssueController::class, 'attachTag'])->name('issues.tags.attach');
Route::delete('issues/{issue}/tags/{tag}/detach', [IssueController::class, 'detachTag'])->name('issues.tags.detach');


Route::get('issues/{issue}/comments', [CommentController::class, 'index']);
Route::post('issues/{issue}/comments', [CommentController::class, 'store']);
