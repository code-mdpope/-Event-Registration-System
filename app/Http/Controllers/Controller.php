<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Log admin activity
     */
    protected function logActivity(string $action, ?string $description = null): void
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            ActivityLog::create([
                'admin_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
            ]);
        }
    }
}
