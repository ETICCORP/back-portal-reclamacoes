<?php

namespace App\Repositories\Complaint\Proviver;

use App\Models\Complaint\Proviver\ComplaintProviderResponse;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;

class ComplaintProviderResponseRepository extends AbstractRepository
{
    protected ComplaintattachmentRepository $attachments;
    public function __construct(ComplaintProviderResponse $model,    ComplaintattachmentRepository $attachments,)
    {
        parent::__construct($model);
        $this->attachments        = $attachments;
    }

    public function storeData(array $data)
    {


        $complaint = $this->model->create([
            'complaint_id' => $data['complaint_id'] ?? null,
            'provider_id' => $data['provider_id'] ?? null,
            'status' => $data['status'] ?? null,


        ]);
        // 📎 Anexos
        $this->handleAttachments($data['attachments'] ?? null, $complaint->id);

        return $complaint;
    }

    private function handleAttachments($attachments, int $complaintId): void
    {
        if (empty($attachments)) {
            return;
        }

        if (is_string($attachments)) {
            $attachments = json_decode($attachments, true);
        }

        if (is_array($attachments)) {
            $this->attachments->createComplaintAttachment($attachments, $complaintId);
        }
    }



}
