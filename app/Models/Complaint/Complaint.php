<?php

namespace App\Models\Complaint;

use App\Enum\ClaimStatus;
use App\Models\Complaint\ComplaintInteraction\ComplaintInteraction;
use App\Models\Complaint\Proviver\ComplaintProvider;
use App\Models\Complaint\Proviver\ComplaintProviderResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Soluction\Soluction;
use App\Models\Complaintattachment\Complaintattachment;
use App\Models\ComplaintTriages\ComplaintTriages;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaint';
    protected $primaryKey = 'id';
    protected $casts = [
        'isAnonymous' => 'boolean', // Define o cast para o atributo isAnonymous
        'enabled' => 'boolean',
        'status' => ClaimStatus::class,
    ];

    protected $fillable = [
        'policy_number',
        'user_id',
        'source',
        'received_at',
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
        "type",
        'representative'
    ];

    /**
     * Força o Laravel a encontrar a Factory no caminho correto.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ComplaintFactory::new();
    }

    /**
     * gera um código aleatório customizado para a reclamação
     * @param mixed $length
     * @return string
     */
    public static function generateCustomRandomCode(int $length = 6): string
    {
        // Prefixo: Ano Atual (2 dígitos) + Mês Atual (2 dígitos) -> Ex: 2605 (Maio de 2026)
        $prefix = date('ym');

        // Parte aleatória
        $characters = '23456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $maxIndex = strlen($characters) - 1;
        $randomPart = '';

        for ($i = 0; $i < $length; $i++) {
            $randomPart .= $characters[random_int(0, $maxIndex)];
        }

        // Retorna no formato: 2605-XXXXXX
        return "{$prefix}-{$randomPart}";
    }

    /**
     * Relacionamentos
     */
    public function user()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    public function typeReport()
    {
        return $this->belongsTo(TypeComplaints::class, 'type');
    }

    public function attachments()
    {
        return $this->hasMany(Complaintattachment::class, 'fk_complaint');
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

    /**
     * Verifica se o prazo (deadline) mais recente da reclamação ainda está ativo/válido.
     *
     * @return bool
     */
    public function getIsDeadlineActiveAttribute(): bool
    {
        // Obtém apenas a última deadline registada para esta reclamação
        $latestDeadline = $this->deadlines()->latest()->first();

        // 📝 Ajustado de expire_at para end_date conforme a estrutura da tua Model
        if (!$latestDeadline || empty($latestDeadline->end_date)) {
            logs()->info('Debug Deadline [Não Ativo - Sem Registro ou Sem Data de Fim]:', [
                'complaint_id'        => $this->id,
                'has_deadline_record' => !is_null($latestDeadline),
                'end_date_value'      => $latestDeadline->end_date ?? 'null'
            ]);

            return false;
        }

        // Como o 'end_date' está no $casts da model ComplaintDeadline, ele já é Carbon!
        $now = now();

        // Se NÃO está expirado, significa que está ativo
        $isActive = !$latestDeadline->is_expired;

        // LOG DE DEBUG: Grava as datas exatas e o resultado da comparação
        logs()->info('Debug Deadline [Processado]:', [
            'complaint_id'         => $this->id,
            'deadline_record_id'   => $latestDeadline->id,
            'data_fim_db'          => $latestDeadline->end_date->toDateTimeString(),
            'hora_atual_servidor'  => $now->toDateTimeString(),
            'esta_ativo_resultado' => $isActive
        ]);

        return $isActive;
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
