<?php

namespace App\Repositories\Complaintattachment;

use App\Models\Complaintattachment\Complaintattachment;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Str;

class ComplaintattachmentRepository extends AbstractRepository
{
    public function __construct(Complaintattachment $model)
    {
        parent::__construct($model);
    }



    public function createComplaintAttachment(array $attachments, int $complaintId): array
    {
        $attachmentsCreated = [];

        Log::debug("📎 Iniciando upload de anexos para denúncia #{$complaintId}", [
            'total' => count($attachments)
        ]);

        foreach ($attachments as $index => $attachment) {
            try {
                /**
                 * =========================================
                 * CASO 1 — Upload via form-data (UploadedFile)
                 * =========================================
                 */


                if ($attachment instanceof \Illuminate\Http\UploadedFile) {

                    $path = $attachment->store("complaintattachments/{$complaintId}", 'public');

                    $created = $this->model->create([
                        'fk_complaint' => $complaintId,
                        'file'         => $path,
                        'name'         => $attachment->getClientOriginalName(),
                    ]);

                    $attachmentsCreated[] = $created;

                    Log::info("💾 Anexo (form-data) salvo", [
                        'id' => $created->id,
                        'path' => $path
                    ]);

                    continue;
                }

                /**
                 * =========================================
                 * CASO 2 — Upload via Base64
                 * =========================================
                 */
                if (!is_string($attachment)) {
                    Log::warning("⚠️ Anexo {$index} inválido", [
                        'type' => gettype($attachment)
                    ]);
                    continue;
                }

                if (!preg_match('/^data:(.*?);base64,(.*)$/', $attachment, $matches)) {
                    Log::warning("❌ Base64 fora do padrão", [
                        'index' => $index
                    ]);
                    continue;
                }

                $mimeType = $matches[1];
                $fileData = base64_decode($matches[2], true);

                if ($fileData === false) {
                    throw new \Exception("Falha ao decodificar Base64");
                }

                $extension = explode('/', $mimeType)[1] ?? 'bin';
                $randomName = $this->model::generateCustomRandomCode(12) . '.' . $extension;
                $path = "complaintattachments/{$complaintId}/{$randomName}";

                Storage::disk('public')->put($path, $fileData);

                $created = $this->model->create([
                    'fk_complaint' => $complaintId,
                    'file'         => $path,
                    'name'         => "dn_{$randomName}",
                ]);

                $attachmentsCreated[] = $created;

                Log::info("💾 Anexo (base64) salvo", [
                    'id' => $created->id,
                    'path' => $path
                ]);
            } catch (\Throwable $e) {
                Log::error("🔥 Erro ao salvar anexo da denúncia {$complaintId}", [
                    'index' => $index,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $attachmentsCreated;
    }


    public function files($alert_id)
    {
        try {
            $response = $this->model::where('fk_complaint', $alert_id)->get();

            // $this->CraftHistory->log('info', 'Visualizou ficheiros da solicitação com o código ' .  $code, Auth::user()->fullName, Auth::user()->id, null, 'user', null);
            $data = [];
            foreach ($response as $attachment) {
                $filePath = storage_path("app/public/" . $attachment->path);
                if (file_exists($filePath)) {
                    $fileSize = filesize($filePath);
                    $fileType = mime_content_type($filePath);
                    $data[] = [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'size' => $fileSize,
                        'type' => $fileType,
                    ];
                } else {
                    $data[] = [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'size' => 0,
                        'type' => 'unknown',
                        'message' => 'Arquivo não encontrado.',
                    ];
                }
            }

            if (empty($data)) {
                return response()->json([
                    "message" => "Nenhum anexo encontrado."
                ], 404);
            }

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Erro ao listar arquivos",
                "error" => $th->getMessage()
            ], 400);
        }
    }
    public function showFile($id)
    {
        $file = $this->model::findOrFail($id);

        if (!$file->file || !Storage::disk('public')->exists($file->file)) {
            throw new \Exception('Arquivo não encontrado.');
        }

        // Retorna o caminho absoluto para o controller
        return Storage::disk('public')->path($file->file);
    }
}
