<?php

namespace App\Repositories\Complaint\Proviver;

use App\Mail\ComplaintForwardedMail;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\Proviver\ComplaintProvider;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaint\ComplaintRepository;
use App\Repositories\Reporter\ReporterRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ComplaintProviderRepository extends AbstractRepository
{
    public $complaintRepository;
    public function __construct(ComplaintProvider $model, ComplaintRepository $complaintRepository)
    {
        parent::__construct($model);
        $this->complaintRepository = $complaintRepository;
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
        $data['status'] = "Encaminhado ao Provedor";
        $data['comment'] = $data['notes'];

        $this->complaintRepository->updateStatus($data, $data['complaint_id']);
        $complaintProvider->load([
            "complaint",
            "provider"

        ]);

        // Envio de e-mail ao Provedor
        Mail::to($complaintProvider->provider->email)
            ->send(new ComplaintForwardedMail($complaintProvider));
        return $complaintProvider;
    }

    public function forward()
    {


        return   $complaint = $this->model::count();
    }

    public function providersManth()
    {
        $complaintsByMonth = $this->model::select(
            DB::raw("DATE_FORMAT(created_at, '%M') as month"), // nome do mês
            DB::raw('COUNT(*) as total')
        )

            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($complaintsByMonth);
    }
}
