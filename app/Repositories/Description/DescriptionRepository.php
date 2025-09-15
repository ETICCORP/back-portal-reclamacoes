<?php
namespace App\Repositories\Description;

use App\Models\Description\Description;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use App\Models\Complaint\Complaint;

class DescriptionRepository extends AbstractRepository
{
    protected $complaintattachment;

    public function __construct(Description $model, ComplaintattachmentRepository $complaintattachment)
    {
        $this->complaintattachment = $complaintattachment;
        parent::__construct($model);
    }

    private function generateRandomImageName(string $extension): string
    {
        $date = date('Ymd_His');
        $randomString = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 10);

        return $date . '_' . $randomString . '.' . $extension;
    }

    public function saveDescription(Complaint $complaint, array $data): void
    {
        if (!isset($data['type'])) {
            return;
        }

        if ($data['type'] !== 'text' && isset($data['body']) && $data['body'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['body'];
            $extension = $file->getClientOriginalExtension();
            $randomName = $this->generateRandomImageName($extension);

            $path = $file->storeAs("complaintattachments/{$complaint->id}", $randomName, 'public');

            $complaintattachment = $this->complaintattachment->createComplaintAttachment(
                $complaint->id,
                $path,
                $randomName
            );

            $this->model->create([
                'type' => $data['type'],
                'body' => $complaintattachment->id,
                'fk_complaint' => $complaint->id,
            ]);
        } else {
            $this->model->create([
                'type' => $data['type'],
                'body' => $data['body'],
                'fk_complaint' => $complaint->id,
            ]);
        }
    }
}
