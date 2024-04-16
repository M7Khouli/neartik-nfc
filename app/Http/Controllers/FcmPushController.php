<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmPushController extends Controller
{
    //
    public function notify($title,$body,$target){

        $messaging = app('firebase.messaging');

        $notification = Notification::create($title,$body);

        $message = CloudMessage::new()
        ->withNotification($notification);

        $report = $messaging->sendMultiCast($message,$target);

        echo 'Successful sends: '.$report->successes()->count().PHP_EOL;
        echo 'Failed sends: '.$report->failures()->count().PHP_EOL;

        if ($report->hasFailures()) {
        foreach ($report->failures()->getItems() as $failure) {
        echo $failure->error()->getMessage().PHP_EOL;
            }
        }
        else {

        }
    }

}
