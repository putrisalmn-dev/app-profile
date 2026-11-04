<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPostsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Latest Posts'))
            ->query(fn (): Builder => Post::query()->with(['category', 'author'])->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->badge(),
                TextColumn::make('author.name')
                    ->label(__('Author')),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('view_count')
                    ->label(__('Views'))
                    ->numeric(),
                TextColumn::make('published_at')
                    ->label(__('Published Date'))
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
