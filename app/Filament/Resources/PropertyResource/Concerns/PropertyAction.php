<?php

namespace App\Filament\Resources\PropertyResource\Concerns;

use App\Enums\PropertyStatus;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait PropertyAction
{
    public static function getTableActions()
    {
        return [
            Tables\Actions\EditAction::make()
                ->iconButton()
                ->color('warning'),
            Tables\Actions\ViewAction::make()
                ->iconButton(),
            Tables\Actions\Action::make('post')
                ->iconButton()
                ->color('primary')
                ->requiresConfirmation()
                ->successNotification(Notification::make()->title(__('Posted successfully'))->success())
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('updatePosted', $record))
                ->icon('gmdi-post-add-o')
                ->action(function (Model $record, Tables\Actions\Action $action): void {
                    $record->update([
                        'status' => PropertyStatus::Posted,
                    ]);
                    $action->success();
                }),
            Tables\Actions\Action::make('unpost')
                ->iconButton()
                ->color('primary')
                ->requiresConfirmation()
                ->successNotification(Notification::make()->title(__('Unposted successfully'))->success())
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('updateUnposted', $record))
                ->icon('gmdi-playlist-remove-o')
                ->action(function (Model $record, Tables\Actions\Action $action): void {
                    $record->update([
                        'status' => PropertyStatus::Draft,
                    ]);
                    $action->success();
                }),
            Tables\Actions\Action::make('trash')
                ->iconButton()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->successNotification(Notification::make()->title(__('Trashed successfully'))->success())
                ->action(function (Model $record, Tables\Actions\Action $action) {
                    $record->delete();

                    $action->success();
                })
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('delete', $record)),
            Tables\Actions\Action::make('restore')
                ->icon('gmdi-refresh-o')
                ->color('gray')
                ->successNotification(Notification::make()->title(__('Restored successfully'))->success())
                ->action(function (Model $record, Tables\Actions\Action $action) {
                    $record->restore();
                    $record->update([
                        'status' => PropertyStatus::Draft,
                    ]);

                    $action->success();
                })
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('restore', $record)),
        ];
    }
}
