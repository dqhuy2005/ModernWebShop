<?php

namespace App\Services;

use App\Models\Order;

interface INotificationService
{
    public function sendOrderNotification(Order $order, $notificationTypeCode = 'order_status_changed');

    public function getStatistics($dateFrom = null, $dateTo = null);
}
