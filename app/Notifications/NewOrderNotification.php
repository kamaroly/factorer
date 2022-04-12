<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Order\Entities\Order;
use Illuminate\Bus\Queueable;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order_transaction_id;
    public $order_status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($orderID, $orderStatus)
    {
        $this->order_transaction_id = $orderID;
        $this->order_status = $orderStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toDatabase($notifiable)
    {

        $text = '<strong>' . $this->order_transaction_id. '</strong> created!';

        $url_backend = route('backend.order.receipt',  $this->order_transaction_id);

        return [
            'title'         => 'Order #:'.$this->order_transaction_id. ' '. $this->order_status,
            'module'        => 'Order',
            'type'          => 'created', // created, published, viewed,
            'icon'          => 'fas fa-shopping-cart',
            'text'          => $text,
            'url_backend'    => $url_backend,
        ];
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
}
