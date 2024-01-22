<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Classes\NotificationObject;
use Illuminate\Broadcasting\PrivateChannel;
use App\View\Components\NotificationNavbar;

class NotificationCustom extends Notification implements ShouldQueue
{
    use Queueable;

    public $notificationObj;
    public $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(NotificationObject $notificationObj, array $via)
    {
        $this->notificationObj = $notificationObj;
        $this->via = $via;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        //return ['database', 'broadcast', 'mail'];
        return $this->via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $notiData = $this->notificationObj->toArray();
        return (new MailMessage)
            ->subject('แจ้งเตือน : ' . $notiData['title'])
            ->markdown('emails.notification', [
                'url' => $notiData['url'],
                'title' => $notiData['title'],
                'description' => $notiData['description'],
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->notificationObj->toArray();
    }

    /**
     * Get the data that should be sent with the broadcasted event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $notiData = $this->notificationObj->toArray();
        $notiNavbarComp = new NotificationNavbar($this->id, $notiData['title'], $notiData['description'], $notiData['url'], $notiData['type'], date('Y-m-d H:i:s'), null);
        $html = $notiNavbarComp->render()->render();
        $notiData = array_merge($notiData, [
            'html' => $html,
            'icon' => $notiNavbarComp->getIcon(),
        ]);
        return $notiData;
    }
}
