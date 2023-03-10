<?php
/**
 * custom values using in the project
 * Also have some custom env values
 */

return [

    'current_date_time' => getCurrentDateTime(),
    'stock_alert_value' => env('STOCK_ALERT_VALUE', 50),
    'out_of_stock_message' => ' is not enough stock to fulfill the order.',
    'email_message_content' => "Stock is below ".env('STOCK_ALERT_VALUE', 50)."% please upgrade a stock.",
    'email_subject' => "Low Stock",
    'low_stock_email_from' => env('MAIL_SEND_LOW_STOCK'),
    'email_from' => env('MAIL_FROM_ADDRESS'),
    'email_from_name' => env('MAIL_FROM_NAME'),
    'random_string_len' => 10,
];
