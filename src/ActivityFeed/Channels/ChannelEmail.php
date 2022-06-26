<?php

namespace East\LaravelActivityfeed\ActivityFeed\Channels;

use East\LaravelActivityfeed\ActivityFeed\Channels\ChannelBase;
use East\LaravelActivityfeed\Facades\AfRender;
use East\LaravelActivityfeed\Interfaces\ChannelInterface;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChannelEmail extends ChannelBase implements ChannelInterface {

    public $type = 'cron';

    public function run(AfNotification $notification)
    {
        $message = AfRender::getMessage($notification);
        $email['to_email'] = $notification->recipient->email ?? null;
        $email['to_name'] = $notification->recipient->name ?? null;

        $email['from_email'] = $notification->creator->email ?? env('MAIL_FROM_ADDRESS');
        $email['from_name'] = $notification->creator->name ?? env('MAIL_FROM_NAME');
        $email['subject'] = $notification->AfEvent->AfRule->AfTemplate->email_subject ?? env('MAIL_FROM_NAME');

        if(!$email['to_email'] OR !$email['from_email']){ return false; }

        try {
            Mail::html($message, function ($message) use ($email) {
                /* @var Message $message */
                $message
                    ->to($email['to_email'],$email['to_name'])
                    ->from($email['from_email'],$email['from_name'])
                    ->subject($email['subject']);
            });

            echo('SENT!');
            print_r($email);

        } catch (\Throwable $exception){
            print_r($email);
            Log::error($exception->getMessage() .json_encode($email));
            print_r($exception->getMessage());
        }

    }


}