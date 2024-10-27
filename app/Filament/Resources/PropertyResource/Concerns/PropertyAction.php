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
                ->fillForm(fn (Model $record): array => [
                    'agents' => $record->agents()->pluck('agent_id'),
                ])
                ->form([
                    Forms\Components\Select::make('agents')
                        ->relationship('agents', 'name')
                        ->multiple()
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->icon('gmdi-post-add-o')
                ->action(function (Model $record, Tables\Actions\Action $action): void {
                    $record->update([
                        'status' => PropertyStatus::Posted,
                    ]);
                    $action->success();
                }),
            Tables\Actions\Action::make('sold_out')
                ->iconButton()
                ->icon('gravityui-tag-dollar')
                ->color('success')
                ->successNotification(Notification::make()->title(__('Sold out successfully'))->success())
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('updateSoldOut', $record))
                ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
                ->form([
                    Forms\Components\Placeholder::make('sale_price')
                        ->label(__('Price'))
                        ->content(function (Model $record) {
                            return $record->sale_price;
                        }),
                    Forms\Components\Placeholder::make('commission')
                        ->label(__('Commission'))
                        ->content(function (Model $record) {
                            return $record->sale_commission_description;
                        }),
                    Forms\Components\TextInput::make('sold_price')
                        ->numeric()
                        ->required()
                        ->default(fn (Model $record) => $record->sale_price_type == PropertyPriceType::Fix ? $record->sale_price_from : 0)
                        ->rules([
                            'required',
                            'numeric',
                            'integer',
                            'min:1',
                        ]),
                    Forms\Components\TextInput::make('sold_commission')
                        ->numeric()
                        ->required()
                        ->default(fn (Model $record) => $record->sale_price_type == PropertyPriceType::Fix ? ($record->sale_price_from * $record->seller_commission / 100) : 0)
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
                        'status' => $record->is_rentable ? PropertyStatus::SoldOut : PropertyStatus::Completed,
                    ]);
                    $action->success();
                }),
            Tables\Actions\Action::make('rented')
                ->iconButton()
                ->icon('bi-house-check')
                ->color('success')
                ->successNotification(Notification::make()->title(__('Rented successfully'))->success())
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('updateRented', $record))
                ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
                ->form([
                    Forms\Components\Placeholder::make('rent_price')
                        ->label(__('Price'))
                        ->content(function (Model $record) {
                            return $record->rent_price;
                        }),
                    Forms\Components\Placeholder::make('commission')
                        ->label(__('Commission'))
                        ->content(function (Model $record) {
                            return $record->rent_commission_description;
                        }),
                    Forms\Components\TextInput::make('rented_price')
                        ->numeric()
                        ->required()
                        ->default(fn (Model $record) => $record->rent_price_type == PropertyPriceType::Fix ? $record->rent_price_from : 0)
                        ->rules([
                            'required',
                            'numeric',
                            'integer',
                            'min:1',
                        ]),
                    Forms\Components\TextInput::make('rented_commission')
                        ->numeric()
                        ->required()
                        ->default(fn (Model $record) => $record->rent_price_type == PropertyPriceType::Fix ? ($record->rent_price_from * $record->landlord_commission / 100) + ($record->rent_price_from * $record->renter_commission / 100) : 0)
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
                        'status' => $record->is_saleable ? PropertyStatus::Rented : PropertyStatus::Completed,
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
                    $record->agents()->detach();

                    $action->success();
                })
                ->visible(fn (Model $record): bool => Filament::auth()->user()->can('restore', $record)),
        ];
    }
}
