<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestQuestionsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Latest Public Questions'))
            ->query(fn (): Builder => Question::query()->with('topic')->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(45),
                TextColumn::make('topic.name')
                    ->label(__('Topic'))
                    ->badge(),
                TextColumn::make('name')
                    ->label(__('Asker')),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('Submitted At'))
                    ->since(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label(__('View / Answer'))
                    ->url(fn (Question $record): string => QuestionResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
