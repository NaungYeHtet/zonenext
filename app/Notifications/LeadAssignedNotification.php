<?php

namespace App\Notifications;

use App\Models\Lead;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification
{
    use Queueable;

    private string $resource;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Lead $lead)
    {
        $this->resource = $this->lead->interest->getLeadType($lead->is_owner)->getResource();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'database',
            'broadcast',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        app()->setLocale($notifiable->language->value);

        return FilamentNotification::make()
            ->success()
            ->title(__('lead.notification.assigned.title', [
                'lead_name' => $this->lead->name,
            ]))
            ->body(__('lead.notification.assigned.body', [
                'lead_name' => $this->lead->name,
                'contact' => $this->lead->contact,
            ]))
            ->actions([
                Action::make('view_lead')
                    ->url($this->resource::getUrl('edit', ['record' => $this->lead]))
                    ->color('info')
                    ->button(),
            ])
            ->getBroadcastMessage();
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title(json_encode([
                'key' => 'lead.notification.assigned.title',
                'replace' => [
                    'lead_name' => $this->lead->name,
                ],
            ]))
            ->body(json_encode([
                'key' => 'lead.notification.assigned.body',
                'replace' => [
                    'lead_name' => $this->lead->name,
                    'contact' => $this->lead->contact,
                ],
            ]))
            ->actions([
                Action::make('view_lead')
                    ->url($this->resource::getUrl('edit', ['record' => $this->lead]))
                    ->color('info')
                    ->button(),
            ])
            ->getDatabaseMessage();
    }
}
