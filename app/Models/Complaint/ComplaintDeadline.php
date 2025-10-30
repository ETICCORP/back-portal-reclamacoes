<?php

namespace App\Models\Complaint;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintDeadline extends Model
{
    use HasFactory;
    protected $table = 'complaint_deadlines';
    protected $primaryKey = 'id';
protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'notified_at' => 'datetime',
];

    protected $fillable = [
        'complaint_id',
        'days',
        'start_date',
        'end_date',
        'status',
        'notified_at'
    ];

    protected $dates = ['start_date', 'end_date', 'notified_at'];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->end_date);
    }

    public function remainingDays(): int
    {
          return now()->diffInDays($this->end_date, false);
    }


 


}