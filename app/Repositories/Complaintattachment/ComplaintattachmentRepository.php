<?php

namespace App\Repositories\Complaintattachment;

use App\Models\Complaintattachment\Complaintattachment;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ComplaintattachmentRepository extends AbstractRepository
{
    public function __construct(Complaintattachment $model)
    {
        parent::__construct($model);
    }

  public function createComplaintAttachment(array $attachments, int $complaintId): array
{
    $attachmentsCreated = [];

    foreach ($attachments as $base64File) {
        try {
            if (preg_match('/^data:(.*?);base64,(.*)$/', $base64File, $matches)) {
                $mimeType = $matches[1];
                $fileData = base64_decode($matches[2]);

                if ($fileData === false) {
                    throw new \Exception("Falha ao decodificar Base64");
                }

                // extensão a partir do mime
                $extension = explode('/', $mimeType)[1] ?? 'bin';

                // gera nome único
                $randomName = $this->model::generateCustomRandomCode(12) . '.' . $extension;

                // define caminho
                $path = "complaintattachments/{$complaintId}/{$randomName}";

                // grava no disco
                Storage::disk('public')->put($path, $fileData);

                // persiste no banco
                $attachmentsCreated[] = $this->model->create([
                    'fk_complaint' => $complaintId,
                    'file'         => $path,
                    'name'         => "dn_{$randomName}",
                ]);
            }
        } catch (\Throwable $e) {
           // \Log::error("Erro ao salvar anexo da denúncia {$complaintId}: " . $e->getMessage());
        }
    }

    return $attachmentsCreated;
}



    public function showFile($id)
    {

        try { // Busca o arquivo pelo ID ou lança uma exceção se não for encontrado
            $file = $this->model::findOrFail($id);

            $path = $file->file; // Acesso ao caminho relativo do arquivo]

            if (!Storage::disk('public')->exists($path)) {
                return response()->json(['error' => 'Arquivo não encontrado'], 404);
            }

            $fileContents = Storage::disk('public')->get($path);
            $mimeType = File::mimeType(storage_path("app/public/{$path}"));

            if (!$mimeType) {
                $mimeType = 'application/octet-stream';
            }
            return response($fileContents, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"'); // 'inline' para abrir no navegador

        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Falha ao abrir o arquivo, não encontrado "
            ], 400);
        }
    }
}
