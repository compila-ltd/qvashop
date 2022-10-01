<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class SendNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    // Send to Telegram Channel
    public function toTelegram($notifiable)
    {
        $message = "Nueva Oferta en QvaShop.\n\n";
        $message .= "$" . $this->p2p->amount . " SQP x " . $this->p2p->receive + 0 . " en #" . $this->p2p->coin . "\n\n";
        $message .= "Ratio: *" . number_format($this->p2p->receive / $this->p2p->amount, 2) . "*\n\n";
        $message .= route('p2p.show', ['p2p' => $this->p2p]);

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.token'))
            ->content($message);
    }
}
