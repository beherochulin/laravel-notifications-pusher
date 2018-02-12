<?php
namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\Arr; // 数组
use NotificationChannels\PusherPushNotifications\Exceptions\CouldNotCreateMessage;

class PusherMessage {
    protected $platform = 'iOS'; // iOS/Android/JavaScript/ReactNative
    protected $title; // 标题
    protected $body; // 主体
    protected $sound = 'default'; // 声音
    protected $icon; // 图标 [Android]
    protected $badge; // 徽章 [iOS]
    protected $options = []; // 选项
    protected $extraMessage; // 额外信息

    public static function create($body='') {
        return new static($body);
    }
    public function __construct($body='') {
        $this->body = $body;
    }

    // ### 附加消息
    public function withAndroid(PusherMessage $message) { // 附加 Android 方法
        $this->withExtra($message->android());
        return $this;
    }
    public function withiOS(PusherMessage $message) { // 附加 iOS 方法
        $this->withExtra($message->iOS());
        return $this;
    }
    public function withJavaScript(PusherMessage $message) { // 附加 JavaScript 方法
        $this->withExtra($message->javaScript());
        return $this;
    }
    public function withReactNative(PusherMessage $message) { // 附加 ReactNative 方法
        $this->withExtra($message->reactNative());
        return $this;
    }
    private function withExtra(PusherMessage $message) { // 附加其它平台消息 基础方法
        if ( $message->getPlatform() == $this->platform ) throw CouldNotCreateMessage::platformConflict($this->platform);

        $this->extraMessage = $message;
    }
    // ### 设置
    public function title($value) { // title 标题
        $this->title = $value;
        return $this;
    }
    public function body($value) { // body 主体
        $this->body = $value;
        return $this;
    }
    public function sound($value) { // sound 声音
        $this->sound = $value;
        return $this;
    }
    public function icon($value) { // icon 图标 Android
        $this->icon = $value;
        return $this;
    }
    public function badge($value) { // badge 徽章 iOS int
        $this->badge = (int) $value;
        return $this;
    }
    public function android() { // 设置平台
        $this->platform = 'Android';
        return $this;
    }
    public function iOS() { // 设置平台
        $this->platform = 'iOS';
        return $this;
    }
    public function javaScript() { // 设置平台
        $this->platform = 'JavaScript';
        return $this;
    }
    public function reactNative() { // 设置平台
        $this->platform = 'ReactNative';
        return $this;
    }
    public function platform($platform) {
        if ( !in_array($platform, ['iOS', 'Android', 'JavaScript', 'ReactNative']) ) throw CouldNotCreateMessage::invalidPlatformGiven($platform);

        $this->platform = $platform;
        return $this;
    }
    public function setOption($key, $value) { // 设置选项
        $this->options[$key] = $value;
        return $this;
    }
    // ### 获取
    public function getPlatform() { // 获取平台
        return $this->platform;
    }
    // ### 格式化
    public function toArray() {
        switch ( $this->platform ) {
            case 'iOS':
                $data = $this->toiOS();
            break;
            case 'Android':
                $data = $this->toAndroid();
            break;
            case 'JavaScript':
                $data = $this->toJavaScript();
            break;
            case 'ReactNative':
                $data = $this->toReactNative();
            break;
            default:

        }
        return $data;
    }
    public function toAndroid() {
        $message = [
            'gcm' => [
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'sound' => $this->sound,
                    'icon' => $this->icon,
                ],
            ],
        ];

        $this->formatMessage($message);
        return $message;
    }
    public function toiOS() {
        $message = [
            'apns' => [
                'aps' => [
                    'alert' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'sound' => $this->sound,
                    'badge' => $this->badge,
                ],
            ],
        ];

        $this->formatMessage($message);
        return $message;
    }
    public function toJavaScript() {
        $message = [
            'apns' => [
                'aps' => [
                    'alert' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'sound' => $this->sound,
                    'badge' => $this->badge,
                ],
            ],
        ];

        $this->formatMessage($message);
        return $message;
    }
    public function toReactNative() {
        $message = [
            'apns' => [
                'aps' => [
                    'alert' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'sound' => $this->sound,
                    'badge' => $this->badge,
                ],
            ],
        ];

        $this->formatMessage($message);
        return $message;
    }
    private function formatMessage(&$message) { // Payload
        if ( $this->extraMessage ) $message = array_merge($message, $this->extraMessage->toArray());

        foreach ( $this->options as $option => $value ) {
            Arr::set($message, $option, $value);
        }
    }
}