<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\QuestionTopicController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\SlideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$registerPublicRoutes = function (string $namePrefix): void {
    Route::get('/posts', [PostController::class, 'index'])->name("{$namePrefix}posts.index");
    Route::get('/posts/{slug}', [PostController::class, 'show'])->name("{$namePrefix}posts.show");

    Route::get('/pages/{slug}', [PageController::class, 'show'])->name("{$namePrefix}pages.show");

    Route::get('/categories', [CategoryController::class, 'index'])->name("{$namePrefix}categories.index");
    Route::get('/slides', [SlideController::class, 'index'])->name("{$namePrefix}slides.index");
    Route::get('/menus/{location}', [MenuController::class, 'show'])->name("{$namePrefix}menus.show");
    Route::get('/settings', [SettingController::class, 'index'])->name("{$namePrefix}settings.index");

    Route::get('/faqs', [FaqController::class, 'index'])->name("{$namePrefix}faqs.index");
    Route::get('/question-topics', [QuestionTopicController::class, 'index'])->name("{$namePrefix}question-topics.index");

    Route::get('/questions', [QuestionController::class, 'index'])->name("{$namePrefix}questions.index");
    Route::post('/questions', [QuestionController::class, 'store'])
        ->middleware('throttle:questions')
        ->name("{$namePrefix}questions.store");
};

Route::prefix('v1')->group(fn () => $registerPublicRoutes('api.v1.'));

$registerPublicRoutes('api.');

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');
