<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
//require_once 'Config.php';
require_once 'RabbitMQ.php';

spl_autoload('RabbitMQ');
actionPrepareQueues();
function actionPrepareQueues()
{
    if(!$_COOKIE['PHPSESSID']) {
        session_start();
    }
    $rabbitMQ = new RabbitMQ();
    $exchangeName = 'testing.hw.messages';
    $queueName = $exchangeName . '.' . $_COOKIE['PHPSESSID'];
    $result = $rabbitMQ->createQueueAndBind(
        $queueName,
        [$exchangeName => 'testing.hw.messages.#'],
        true,
        60
    );
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode([
            'queueName' => $queueName,
        ]);
    } else {
        echo json_encode([
            'error' => true,
        ]);
    }
}

