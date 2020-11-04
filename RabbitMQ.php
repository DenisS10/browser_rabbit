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

    public function sendTachograpyCurrentDataReportRequest($driverId, $tabUID, $reportUID)
    {
        $time = time();
        $packetArray = [
            'cmd' => 'exec_tachograph_dashboard',
            'logic' => 'current_tacho_report',
            'time_from' => $time,
            'time_to' => $time,
            'lang' => substr(Yii::$app->language, 0, 2),
            'tz_offset' => Yii::$app->user->identity->time_zone,
            'id_user' => Yii::$app->user->identity->id,
            'id_session' => $tabUID,
            'driver_list' => [$driverId],
            'reportUID' => $reportUID,
        ];
        $packet = JSON::encode($packetArray);
        $this->openChannel();
        if (!$this->rabbitChannel) {
            //throw new Exception('RabbitMQ not connected');
            return false;
        }
        $exchange = 'gelios.report.request';
        $routingKey = 'gelios.report.request.current_tacho_report';
        $msg = new AMQPMessage($packet);
        $this->rabbitChannel->basic_publish($msg, $exchange, $routingKey);
        Yii::trace($packet . ', ' . Json::encode(['exchange' => $exchange, 'topic' => $routingKey]));
        $this->closeChannel();
        return true;
    }
    public function sendTachograpyIntervalDataReportRequest($driverId, $tabUID, $reportUID, $timeFrom, $timeTo)
    {
        $time = time();
        $packetArray = [
            'cmd' => 'exec_tachograph_dashboard',
            'logic' => 'interval_tacho_report',
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
            'lang' => substr(Yii::$app->language, 0, 2),
            'tz_offset' => Yii::$app->user->identity->time_zone,
            'id_user' => Yii::$app->user->identity->id,
            'id_session' => $tabUID,
            'driver_list' => [$driverId],
            'reportUID' => $reportUID,
        ];
        $packet = JSON::encode($packetArray);
        $this->openChannel();
        if (!$this->rabbitChannel) {
            //throw new Exception('RabbitMQ not connected');
            return false;
        }
        $exchange = 'gelios.report.request';
        $routingKey = 'gelios.report.request.interval_tacho_report';
        $msg = new AMQPMessage($packet);
        $this->rabbitChannel->basic_publish($msg, $exchange, $routingKey);
        Yii::trace($packet . ', ' . Json::encode(['exchange' => $exchange, 'topic' => $routingKey]));
        $this->closeChannel();
        return true;
    }
    public function sendOrdinaryReportRequest($packet)
    {
        $this->openChannel();
        if (!$this->rabbitChannel) {
            //throw new Exception('RabbitMQ not connected');
            return false;
        }
        $exchange = 'gelios.report.request';
        $routingKey = "gelios.report.request.ordinary";
        $msg = new AMQPMessage($packet);
        $this->rabbitChannel->basic_publish($msg, $exchange, $routingKey);
        Yii::trace($packet . ', ' . Json::encode(['exchange' => $exchange, 'topic' => $routingKey]));
        $this->closeChannel();
        return true;
    }
    public function sendCommandToHardware($unit, $commandType, $commandText, $commandDatabaseId)
    {
        $this->openChannel();
        if (!$this->rabbitChannel) {
            //throw new Exception('RabbitMQ not connected');
            return false;
        }
        $routingKey = "gelios.hw.cmd.send.{$unit->hw_type}.{$unit->id}";
        $time = time();
        $payload = json_encode([
            'unit_id' => $unit->id,
            'hw_type' => $unit->hw_type,
            'hw_id' => $unit->hw_id,
            'cmd_time' => $time,
            'cmd_type' => $commandType,
            'cmd_text' => $commandText,
            'cmd_id' => $commandDatabaseId,
        ]);
        $msg = new AMQPMessage($payload);
        $this->rabbitChannel->basic_publish($msg, 'gelios.hw.cmd.send', $routingKey);
        Yii::trace($payload . ', ' . Json::encode(['exchange' => 'gelios.hw.cmd.send', 'topic' => $routingKey]));
        $this->closeChannel();
        return true;
    }

    public function sendExecuteRecalculateMaintenanceRequest($maintenanceId)
    {
        $this->openChannel();
        if (!$this->rabbitChannel) {
            //throw new Exception('RabbitMQ not connected');
            return false;
        }
        $routingKey = "gelios.maintenance.service";
        $time = time();
        $payload = json_encode([
            'maintenances_id' => !is_array($maintenanceId) ? [$maintenanceId] : $maintenanceId,
            'action' => 'recalculate',
            'timestamp' => $time
        ]);
        $msg = new AMQPMessage($payload);
        $this->rabbitChannel->basic_publish($msg, 'gelios.maintenance.service', $routingKey);
        Yii::trace($payload . ', ' . Json::encode(['exchange' => 'gelios.maintenance.service', 'topic' => $routingKey]));
        $this->closeChannel();
        return true;
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
