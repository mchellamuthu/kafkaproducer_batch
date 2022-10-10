<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Junges\Kafka\Producers\MessageBatch;

class ProduceMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $limit;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($limit = 100)
    {
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users  = \App\Models\User::paginate($this->limit);
        $messageBatch = new MessageBatch();
        foreach ($users as $user) {
            $message = new Message(body: [
                'name' => $user->name,
                'email' => $user->email,
            ]);
            $messageBatch->push($message);
        }


        $producer = Kafka::publishOn('large_data')->withSasl(new \Junges\Kafka\Config\Sasl(
            password: config('kafka.secret'),
            username: config('kafka.key'),
            mechanisms: 'PLAIN',
            securityProtocol: 'SASL_SSL',
        ));
        $producer->sendBatch($messageBatch);
    }
}
