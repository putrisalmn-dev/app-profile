<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MenuLocation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MenuResource;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Relations\Relation;

class MenuController extends Controller
{
    public function show(MenuLocation $location): MenuResource
    {
        $menu = Menu::query()
            ->active()
            ->where('location', $location)
            ->with([
                'rootItems' => fn (Relation $query) => $query->active()->with([
                    'linkable',
                    'children' => fn (Relation $childQuery) => $childQuery->active()->with('linkable'),
                ]),
            ])
            ->firstOrFail();

        return MenuResource::make($menu);
    }
}
