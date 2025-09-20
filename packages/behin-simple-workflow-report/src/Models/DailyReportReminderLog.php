<?php

namespace Behin\SimpleWorkflowReport\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportReminderLog extends Model
{
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $table = 'daily_report_reminder_logs';

    protected $fillable = [
        'report_date',
        'user_id',
        'mobile',
        'status',
        'error_message',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];
}
