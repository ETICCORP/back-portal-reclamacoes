<?php

namespace App\Repositories\Complaint\Proviver;

use App\Mail\ComplaintForwardedMail;
use App\Models\Complaint\Proviver\ComplaintProvider;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Mail;

class ComplaintProviderRepository extends AbstractRepository
{
    public function __construct(ComplaintProvider $model)
    {
        parent::__construct($model);
    }
    public function forwardComplaint(array $data)
    {

        $complaintProvider = $this->model->create([
            'complaint_id' => $data['complaint_id'],
            'provider_id' => $data['provider_id'],
            'summary' => $data['summary'],
            'notes' => $data['notes'],
            'sent_at' => $data['complaint_id'],
            'deadline' => $data['deadline'],
            'status' => 'sent'

        ]);
        // 📎 Anexos
        //  $this->uploadSignature($data['signature_path'] ?? null, $complaint->id);

        $complaintProvider->load([
            "complaint",
            "provider"

        ]);

        // Envio de e-mail ao Provedor
        Mail::to($complaintProvider->provider->email)
            ->send(new ComplaintForwardedMail($complaintProvider));
        return $complaintProvider;
    }
}
