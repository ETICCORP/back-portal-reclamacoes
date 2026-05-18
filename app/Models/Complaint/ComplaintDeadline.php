<?php

namespace App\Models\Complaint;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $appends = [
        'is_extended'
    ];

    protected $dates = ['start_date', 'end_date', 'notified_at'];

    public function logs(): HasMany
    {
        return $this->hasMany(ComplaintDeadlineLog::class, 'complaint_deadline_id');
    }

    /**
     * Accessor para 'is_extended'
     * Retorna true se o prazo já tiver sido renovado/prolongado.
     */
    public function getIsExtendedAttribute(): bool
    {
        // O item inicial de criação conta como 1. Se houver mais de 1, já foi renovado.
        return $this->logs()->count() > 1;
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
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
