<?php


interface TaskQueue {
    public function createQueueAndBind($queueName, $exchangeKeyBindings, $autoDelete = false, $expires = false);
}
