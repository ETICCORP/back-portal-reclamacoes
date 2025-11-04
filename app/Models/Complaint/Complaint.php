<?php

namespace App\Models\Complaint;

use App\Models\Complaint\ComplaintInteraction\ComplaintInteraction;
use App\Models\Complaint\Proviver\ComplaintProvider;
use App\Models\Complaint\Proviver\ComplaintProviderResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Soluction\Soluction;
use App\Models\ComplaintAttachment\ComplaintAttachment; // cuidado: Service não é Model!
use App\Models\ComplaintTriages\ComplaintTriages;

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

     public function triages()
    {
        return $this->hasMany(ComplaintTriages::class, 'complaint_id');
    }

      public function opinions()
    {
        return $this->hasMany(ComplaintOpinions::class, 'complaint_id');
    }
       public function interaction()
    {
        return $this->hasMany(ComplaintInteraction::class, 'complaint_id');
    }
    public function deadlines()
    {
        return $this->hasMany(ComplaintDeadline::class, 'complaint_id');
    }

    

     public function proverResponse()
    {
        return $this->hasMany(ComplaintProviderResponse::class, 'complaint_id');
    }

    public function forwardProvider()
    {
        return $this->hasMany(ComplaintProvider::class, 'complaint_id');
    }



     public function entitiyResponse()
    {
        return $this->hasMany(ComplaintResponses::class, 'complaint_id');
    }
    
}
