<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderNotification extends Notification
{
    use Queueable;
    
    protected $order_notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order_notification)
    {
        $this->order_notification = $order_notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
//        return ['mail', 'database'];
        return ['database'];
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
            'order_id'      => $this->order_notification['order_id'],
            'order_code'    => $this->order_notification['order_code'],
            'user_id'       => $this->order_notification['user_id'],
            'seller_id'     => $this->order_notification['seller_id'],
            'status'        => $this->order_notification['status']
        ];
    }
}
