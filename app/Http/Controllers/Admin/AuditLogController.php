<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('admin')
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.audit-logs.index', compact('logs'));
    }
}
