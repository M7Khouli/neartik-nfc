<?php


namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService {

        public static function notify($title,$body,$target,$image=null){

        $messaging = app('firebase.messaging');

        $notification = Notification::create($title,$body,$image);

        $message = CloudMessage::new()
        ->withNotification($notification)
        ->withData(['type'=>'new message']);

        $report = $messaging->sendMultiCast($message,$target);

        /*
        echo 'Successful sends: '.$report->successes()->count().PHP_EOL;
        echo 'Failed sends: '.$report->failures()->count().PHP_EOL;

        if ($report->hasFailures()) {
        foreach ($report->failures()->getItems() as $failure) {
        echo $failure->error()->getMessage().PHP_EOL;
            }
        }
        else {

        }
        */
    }

}



