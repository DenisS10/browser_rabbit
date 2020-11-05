<?php

require_once 'TaskQueue.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;



class RabbitMQ Implements TaskQueue {
    
    protected $rabbitConnection;
    protected $rabbitChannel;
    protected $rabbitMQCluster;

    public function __construct()
    {
        $this->rabbitMQCluster = [
            [
                'host' => '192.168.1.123',
                'port' => '5672',
                'username' => 'user',
                'password' => '32dg6fg2v1e65gr',
            ],
        ];

    }
    protected function openChannel()
    {
        $connectionSuccess = false;
        $maxRetry = 200;
        $retryCount = 0;
        while (!$connectionSuccess && $retryCount < $maxRetry) {
            try {
                $index = rand(0, count($this->rabbitMQCluster) - 1);
                $rabbitMQConnData = $this->rabbitMQCluster[$index];
                $this->rabbitConnection = new AMQPStreamConnection(
                    $rabbitMQConnData['host'],
                    $rabbitMQConnData['port'],
                    $rabbitMQConnData['username'],
                    $rabbitMQConnData['password']
                );  
                $this->rabbitChannel = $this->rabbitConnection->channel();
                if ($this->rabbitChannel) {
                    $connectionSuccess = true;
                } else {
                    $retryCount++;
                }
            } catch (\Exception $e) {
                $retryCount++;
            }
        }
        return $connectionSuccess;
    }
    protected function closeChannel() 
    {
        $this->rabbitChannel->close();
        $this->rabbitConnection->close();
    }

    public function createQueueAndBind($queueName, $exchangeKeyBindings, $autoDelete = false, $expires = false)
    {
        $this->openChannel();
        if (!$this->rabbitChannel) {
            //throw new Exception('RabbitMQ not connected');
            return false;
        }
        $arguments = $expires ? ['x-expires' => ['I', $expires * 1000]] : [];
        //                                      $passive, $durable, $exclusive, $auto_delete, $nowait, $arguments
        $this->rabbitChannel->queue_declare($queueName, false, true, false, $autoDelete, false, $arguments);
        foreach ($exchangeKeyBindings as $exchangeName => $routingKey) {
            $this->rabbitChannel->queue_bind($queueName, $exchangeName, $routingKey);
        }
        $this->closeChannel();
        return true;
    }
}
