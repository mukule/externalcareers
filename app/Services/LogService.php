<?php

namespace App\Services;

use App\Models\AdminLogModel;
use App\Models\UserLogModel;

class LogService
{
    /**
     * Log admin activity
     */
    public static function admin($userId, $action)
    {
        try {
            (new AdminLogModel())->insert([
                'uid'    => $userId,
                'action' => $action,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Admin Log Failed: ' . $e->getMessage());
        }
    }

    /**
     * Log user activity
     */
    public static function user($userId, $action)
    {
        try {
            (new UserLogModel())->insert([
                'uid'    => $userId,
                'action' => $action,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'User Log Failed: ' . $e->getMessage());
        }
    }
}