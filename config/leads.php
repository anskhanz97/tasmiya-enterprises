<?php

return [
    'timezone' => 'Asia/Karachi',
    'reminders' => [
        'enabled' => env('LEAD_REMINDERS_ENABLED', true),
        'time' => env('LEAD_REMINDER_TIME', '09:00'),
        'upcoming_days' => (int) env('LEAD_UPCOMING_DAYS', 7),
    ],
];
