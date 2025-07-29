<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $request = $this->request;
        $user = $notifiable;

        return (new MailMessage())
            ->line(__('messages.welcome_to').app_name().'!')
            ->line(__('messages.account_is_created_use_this_credebtials'))
            ->line(__('messages.Username').': '.$user->username)
            ->line(__('messages.Email').': '.$user->email)
            ->line(__('messages.Password').': '.$request['password']);
    }
}
