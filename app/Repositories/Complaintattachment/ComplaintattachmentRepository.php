<?php

namespace App\Repositories\Complaintattachment;

use App\Models\Complaintattachment\Complaintattachment;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    foreach ($attachments as $index => $base64File) {
        try {
            Log::debug("🔍 Processando anexo {$index}");

            // valida se é string
            if (!is_string($base64File)) {
                Log::warning("⚠️ Anexo {$index} não é uma string base64", [
                    'value' => $base64File
                ]);
                continue;
            }

            // garantir que está no formato "data:xxx;base64,yyyy"
            if (!preg_match('/^data:(.*?);base64,(.*)$/', $base64File, $matches)) {
                Log::warning("❌ String não corresponde ao padrão Base64 esperado", [
                    'index' => $index,
                    'preview' => substr($base64File, 0, 50)
                ]);
                continue;
            }

            $mimeType = $matches[1] ?? 'application/octet-stream';
            $fileData = base64_decode($matches[2], true);

            if ($fileData === false) {
                throw new \Exception("Falha ao decodificar Base64");
            }

            Log::debug("✅ Base64 decodificado com sucesso", [
                'mimeType' => $mimeType,
                'size'     => strlen($fileData)
            ]);

            // descobrir extensão
            $extension = explode('/', $mimeType)[1] ?? 'bin';
            $randomName = $this->model::generateCustomRandomCode(12) . '.' . $extension;
            $path = "complaintattachments/{$complaintId}/{$randomName}";

            // salvar no disco
            Storage::disk('public')->put($path, $fileData);

            Log::debug("📂 Arquivo salvo no storage", ['path' => $path]);

            // registrar no banco
            $created = $this->model->create([
                'fk_complaint' => $complaintId,
                'file'         => $path,
                'name'         => "dn_{$randomName}",
            ]);

            Log::info("💾 Anexo cadastrado no banco", ['id' => $created->id]);

            $attachmentsCreated[] = $created;
        } catch (Throwable $e) {
            Log::error("🔥 Erro ao salvar anexo da denúncia {$complaintId}", [
                'index' => $index,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    return $attachmentsCreated;
}




public function showFile($id)
{
    try {
        $file = $this->model::findOrFail($id);
        $path = $file->file;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return response('Arquivo não encontrado', 404)
                ->header('Content-Type', 'text/plain');
        }

        $absolutePath = Storage::disk('public')->path($path);
        $mimeType = \Illuminate\Support\Facades\File::mimeType($absolutePath) ?? 'application/octet-stream';

        // envia o binário puro
        return response()->make(file_get_contents($absolutePath), 200, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($absolutePath).'"',
            'Cache-Control'       => 'no-cache, must-revalidate',
        ]);
    } catch (\Throwable $th) {
        Log::error("Erro em showFile", [
            'id'    => $id,
            'error' => $th->getMessage(),
        ]);

        return response('Falha ao abrir o arquivo', 400)
            ->header('Content-Type', 'text/plain');
    }
}



}
