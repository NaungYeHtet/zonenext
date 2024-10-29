<?php

namespace App\Filament\Resources\PropertyResource\Concerns;

use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
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
                ->successNotification(Notification::make()->title(__('Posted successfully'))->success())
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('updatePosted', $record))
                ->icon('gmdi-post-add-o')
                ->action(function (Model $record, Tables\Actions\Action $action): void {
                    $record->update([
                        'status' => PropertyStatus::Posted,
                    ]);
                    $action->success();
                }),
            Tables\Actions\Action::make('purchased')
                ->iconButton()
                ->icon('bi-house-check')
                ->color('success')
                ->successNotification(Notification::make()->title(__('Updated property status to purchased.'))->success())
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('purchased', $record))
                ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
                ->form([
                    Forms\Components\Placeholder::make('price')
                        ->label(__('Price'))
                        ->content(function (Model $record) {
                            return $record->price;
                        }),
                    Forms\Components\Placeholder::make('commission')
                        ->label(__('Commission'))
                        ->content(function (Model $record) {
                            return $record->commission_description;
                        }),
                    Forms\Components\TextInput::make('purchased_price')
                        ->numeric()
                        ->required()
                        ->default(fn (Model $record) => $record->price_type == PropertyPriceType::Fix ? $record->price_from : 0)
                        ->rules([
                            'required',
                            'numeric',
                            'integer',
                            'min:1',
                        ]),
                    Forms\Components\TextInput::make('purchased_commission')
                        ->numeric()
                        ->required()
                        ->default(fn (Model $record) => $record->price_type == PropertyPriceType::Fix ? ($record->price_from * $record->owner_commission / 100) + ($record->price_from * $record->customer_commission / 100) : 0)
                        ->rules([
                            'required',
                            'numeric',
                            'integer',
                            'min:1',
                        ]),
                ])
                ->modalWidth(MaxWidth::Medium)
                ->action(function (Model $record, Tables\Actions\Action $action): void {
                    $record->update([
                        'status' => PropertyStatus::Purchased,
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
