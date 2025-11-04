<?php

namespace App\Models\Complaint\Proviver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintProviderResponse extends Model
{
    use HasFactory;
    protected $table = 'complaint_provider_response';
    protected $primaryKey = 'id';
    protected $fillable = ['complaint_id', 'provider_id', 'status', 'response'];
}