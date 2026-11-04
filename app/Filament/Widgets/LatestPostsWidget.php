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

    protected static ?string $heading = 'Artikel Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Post::query()->with(['category', 'author'])->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),
                TextColumn::make('author.name')
                    ->label('Penulis'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('view_count')
                    ->label('Dilihat')
                    ->numeric(),
                TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
