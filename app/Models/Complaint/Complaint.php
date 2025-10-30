<?php

namespace App\Models\Complaint;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Soluction\Soluction;
use App\Models\ComplaintAttachment\ComplaintAttachment; // cuidado: Service não é Model!

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaint';
    protected $primaryKey = 'id';
    protected $casts = [
        'isAnonymous' => 'boolean', // Define o cast para o atributo isAnonymous
        'enabled' => 'boolean',
    ];
    protected $fillable = [
        'full_name',
        'complainant_role',
        'contact',
        'email',
        'policy_number',
        'entity',
        'description',
        'code',
        'incidentDateTime',
        'location',
        'status',
        "type"
    ];



    public static function generateCustomRandomCode($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }

    /**
     * Relacionamentos
     */

    public function typeReport()
    {
        return $this->belongsTo(TypeComplaints::class, 'type');
    }



    public function attachments()
    {
        return $this->hasMany(ComplaintAttachment::class, 'fk_complaint');
    }

    public function soluctions()
    {
        return $this->hasMany(Soluction::class, 'fk_complaint');
    }
}
