<?php

namespace App\Models\Complaint;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintDeadlineLog extends Model
{
    use HasFactory;

    protected $table = 'complaint_deadlines_logs';

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    protected $fillable = [
        'complaint_deadline_id',
        'days',
        'start_date',
        'end_date',
        'status',
        'reason',
        'user_id', // Guarda quem fez a alteração para auditoria estrita
    ];

    public function deadline(): BelongsTo
    {
        return $this->belongsTo(ComplaintDeadline::class, 'complaint_deadline_id');
    }
}