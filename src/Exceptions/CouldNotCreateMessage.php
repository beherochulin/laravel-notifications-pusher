<?php
namespace NotificationChannels\PusherPushNotifications\Exceptions;

use Exception;

class CouldNotCreateMessage extends Exception {
    public static function invalidPlatformGiven($platform) { // 不合法的平台
        return new static("Platform `{$platform}` is invalid. It should be either `Android`, `iOS`, `JavaScript` or `ReactNative`.");
    }
    public static function platformConflict($platform) { // 平台冲突
        return new static("You are trying to send an extra message to `{$platform}` while the original message is to `{$platform}`.");
    }
}