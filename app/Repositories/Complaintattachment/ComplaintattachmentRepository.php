<?php

namespace App\Repositories\Complaintattachment;

use App\Models\Complaintattachment\Complaintattachment;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ComplaintattachmentRepository extends AbstractRepository
{
    public function __construct(Complaintattachment $model)
    {
        parent::__construct($model);
    }

 public function createComplaintAttachment(array $attachments, int $complaintId)
{
    $attachmentsCreated = [];

    \Log::debug("📎 Iniciando upload de anexos para denúncia #{$complaintId}", [
        'attachments' => $attachments
    ]);

    foreach ($attachments as $index => $base64File) {
        try {
            \Log::debug("🔍 Processando anexo {$index}");

            if (!is_string($base64File)) {
                \Log::warning("⚠️ Anexo {$index} não é uma string base64", ['value' => $base64File]);
                continue;
            }

            if (preg_match('/^data:(.*?);base64,(.*)$/', $base64File, $matches)) {
                $mimeType = $matches[1];
                $fileData = base64_decode($matches[2]);

                \Log::debug("✅ Base64 decodificado", [
                    'mimeType' => $mimeType,
                    'size'     => strlen($fileData)
                ]);

                if ($fileData === false) {
                    throw new \Exception("Falha ao decodificar Base64");
                }

                $extension = explode('/', $mimeType)[1] ?? 'bin';
                $randomName = $this->model::generateCustomRandomCode(12) . '.' . $extension;
                $path = "complaintattachments/{$complaintId}/{$randomName}";

                Storage::disk('public')->put($path, $fileData);

                \Log::debug("📂 Arquivo salvo no storage", ['path' => $path]);

                $created = $this->model->create([
                    'fk_complaint' => $complaintId,
                    'file'         => $path,
                    'name'         => "dn_{$randomName}",
                ]);

                Log::info("💾 Anexo cadastrado no banco", ['id' => $created->id]);

                $attachmentsCreated[] = $created;
            } else {
                Log::warning("❌ String não corresponde ao padrão Base64 esperado", [
                    'content_start' => substr($base64File, 0, 30)
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("🔥 Erro ao salvar anexo da denúncia {$complaintId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
