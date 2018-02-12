<?php
namespace NotificationChannels\PusherPushNotifications;

use Pusher; // 频道驱动
use Illuminate\Events\Dispatcher; // 事件调度
use Illuminate\Notifications\Notification; // 通知
use Illuminate\Notifications\Events\NotificationFailed; // 通知失败

class PusherChannel { // Pusher 频道
    protected $pusher;
    private $events;

    public function __construct(Pusher $pusher, Dispatcher $events) {
        $this->pusher = $pusher;
        $this->events = $events;
    }
    public function send($notifiable, Notification $notification) {
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: $this->interestName($notifiable);

        $response = $this->pusher->notify(
            $interest,
            $notification->toPushNotification($notifiable)->toArray(),
            true
        );

        if ( !in_array($response['status'], [200, 202]) ) { // 200 202 正常状态
            $this->events->fire(
                new NotificationFailed($notifiable, $notification, 'pusher-push-notifications', $response)
            );
        }
    }
    protected function interestName($notifiable) { // interestName
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}