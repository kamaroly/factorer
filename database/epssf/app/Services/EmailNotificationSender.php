<?php 
namespace Ceb\Services;

use Fenos\Notifynder\Contracts\Sender;
use Fenos\Notifynder\Contracts\NotifynderSender;
use Illuminate\Mail\Mailer;

class EmailNotificationSender implements Sender
{

    protected $notifications;

    protected $mailer;

    public function __construct($notifications,Mailer $mailer) 
    {
        $this->notifications = $notifications;
        $this->mailer = $mailer;
    }

    public function send(NotifynderSender $sender)
    {
        // Do your extra logic here
        $notificationSent = $sender->send($this->notifications);     

        $this->sendEmails($notificationSent);
    }

    public function sendEmails($notificationsSent) {

            $view = 'emails.notification';
            $user = $notificationsSent->to;
            $this->mailer->queue($view, compact('user'), function($message) use ($user) {
                return $message->to($user->email)
                    ->subject('Ceb Notification');
            });
        
    }
}

 ?>