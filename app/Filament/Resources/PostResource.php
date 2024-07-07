<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Post Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->placeholder('Enter the title of the post'),
                        Forms\Components\TextInput::make('slug')
                            ->required(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->placeholder('Enter the content of the post'),
                        Forms\Components\Textarea::make('short_content')
                            ->label('Short Content')
                            ->required()
                            ->placeholder('Enter the short content of the post'),
                        Forms\Components\Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple(),

                    ]),

                Forms\Components\Section::make('Meta Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('meta.time')
                            ->label('Time')
                            ->placeholder('Enter the time of the post'),
                        Forms\Components\TextInput::make('meta.source_name')
                            ->label('Source Name')
                            ->placeholder('Enter the source name of the post'),
                        Forms\Components\TextInput::make('meta.source_url')
                            ->label('Source URL')
                            ->placeholder('Enter the source URL of the post'),
                        Forms\Components\TextInput::make('meta.main_image')
                            ->label('Source Image'),
                    ]),
                Forms\Components\Section::make('Images')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('images.xs')
                            ->label('Extra Small Image'),
                        Forms\Components\TextInput::make('images.s')
                            ->label('Small Image'),
                        Forms\Components\TextInput::make('images.sqr')
                            ->label('Square Image'),
                        Forms\Components\TextInput::make('images.webp.xs')
                            ->label('Extra Small WebP Image'),
                        Forms\Components\TextInput::make('images.webp.s')
                            ->label('Small WebP Image'),

                        Forms\Components\TextInput::make('images.webp.sqr')
                            ->label('Square WebP Image'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Created at filter
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
