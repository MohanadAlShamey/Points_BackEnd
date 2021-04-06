<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function GuzzleHttp\json_encode;

class SendNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    public $tokenDevice;
    public $title;
    public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($token,$title,$body)
    {
        $this->body=$body;
        $this->title=$title;
        $this->tokenDevice=$token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$token= \App\User::whereNotNull('device')->pluck('device')->all();
        //dd($token);
        // echo $token.'<br>';
        $headers=[
            'Authorization: key='.'AAAAaRWNlG8:APA91bHjKf3yH4nxlGjzuvGy9Q8itry1d-AADELKZF7sfcjW4xJIaggEFp2Pa2PmvzMY4mrQhgbnhnFt3LObA8NLlba-Izg4cPfR3Bc0oOt1zi1LUUgpeqK1gg2ghPtqNxtj4sGvlatM',
            'Content-Type: application/json'
        ];
        $data=[
            "registration_ids"=>$this->tokenDevice,
            "notification"=>['title'=>$this->title,'body'=>$this->body,'sound'=>'default'],
        ];
        $json=json_encode($data);
        $url='https://fcm.googleapis.com/fcm/send';
        $ch = curl_init();



        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);



        $response = curl_exec($ch);

    }
}
