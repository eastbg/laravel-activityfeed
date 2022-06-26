<?php

namespace East\LaravelActivityfeed\ActivityFeed\Channels;

use East\LaravelActivityfeed\ActivityFeed\Channels\ChannelBase;
use East\LaravelActivityfeed\Interfaces\ChannelInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;

class ChannelEmail extends ChannelBase implements ChannelInterface {

    public $type = 'cron';

    public function run(AfNotification $notification)
    {
        echo('Here we go!');
        // TODO: Implement send() method.
    }

    private function sendEmail(AfNotification $notification){

        \Mail::html($email->message_html, function ($message) use ($email) {
            /* @var Message $message */
            $message
                ->to($email->to)
                ->from($email->from['email'],$email->from['name'])
                ->cc($email->cc)
                ->subject($email->subject);
        });

    }


}