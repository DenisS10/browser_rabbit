<?php


interface TaskQueue {
    public function sendCommandToHardware($unit, $commandType, $commandText, $commandDatabaseId);
    public function sendExecuteRecalculateMaintenanceRequest($maintenanceId);
}
