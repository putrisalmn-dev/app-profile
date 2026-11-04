<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SlideResource;
use App\Models\Slide;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SlideController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $slides = Slide::query()
            ->showing()
            ->orderBy('sort_order')
            ->get();

        return SlideResource::collection($slides);
    }
}
