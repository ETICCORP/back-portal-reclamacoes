<?php

namespace App\Repositories\Complaint\ModelEmail;

use App\Models\Complaint\ModelEmail\ModelEmail;
use App\Repositories\AbstractRepository;
use Illuminate\Http\UploadedFile; // ✅ Corrigido
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModelEmailRepository extends AbstractRepository
{
    public function __construct(ModelEmail $model)
    {
        parent::__construct($model);
    }

    public function complaintResponse(array $data)
    {
        // 1️⃣ Cria a reclamação SEM assinatura
        $complaint = $this->model->create([
            'subject' => $data['subject'],
            'name'    => $data['name'],
            'body'    => $data['body'],
            'user_id' => $data['user_id'],
        ]);

        // 2️⃣ Upload da assinatura (form-data)
        $signaturePath = $this->uploadSignature(
            $data['signature_path'] ?? null,
            $complaint->id
        );

        // 3️⃣ Guarda o caminho da imagem, se existir
        if ($signaturePath) {
            $complaint->update([
                'signature_path' => $signaturePath
            ]);
        }

        // 4️⃣ Carrega relações
        $complaint->load(['user']);

        return $complaint;
    }

    public function uploadSignature(?UploadedFile $signature, int $responseId): ?string
    {
        Log::debug("🖋️ Iniciando upload da assinatura (form-data) para resposta #{$responseId}");

        try {
            // Se não veio assinatura, simplesmente ignora
            if (!$signature) {
                Log::info("ℹ️ Nenhuma assinatura enviada");
                return null;
            }

            if (!$signature->isValid()) {
                Log::warning("⚠️ Ficheiro de assinatura inválido");
                return null;
            }

            if (!str_starts_with($signature->getMimeType(), 'image/')) {
                Log::warning("❌ Ficheiro enviado não é imagem", [
                    'mime' => $signature->getMimeType()
                ]);
                return null;
            }

            // Upload
            $path = $signature->store(
                "complaint_signatures/{$responseId}",
                'public'
            );

            Log::info("💾 Assinatura salva no storage", [
                'path' => $path,
                'name' => $signature->getClientOriginalName()
            ]);

            // Atualiza no banco
            $response = $this->model::find($responseId);

            if (!$response) {
                Log::warning("⚠️ Resposta não encontrada", [
                    'response_id' => $responseId
                ]);
                return null;
            }

            $response->update([
                'signature_path' => $path
            ]);

            Log::info("✅ Assinatura associada à resposta", [
                'response_id' => $response->id
            ]);

            return $path;
        } catch (\Throwable $e) {
            Log::error("🔥 Erro ao salvar assinatura da resposta {$responseId}", [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    public function files($modelID)
    {
        try {
            $response = $this->model::where('id', $modelID)->get();

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

        // Verifica se existe assinatura
        if (!$file->signature_path || !Storage::disk('public')->exists($file->signature_path)) {
            throw new \Exception('Arquivo não encontrado.');
        }

        // Retorna o caminho absoluto para o controller
        return Storage::disk('public')->path($file->signature_path);
    }



    public function update(array $data, $id)
    {
        $model = ModelEmail::findOrFail($id);

        // atualiza texto
        $model->update([
            'subject' => $data['subject'],
            'name'    => $data['name'],
            'body'    => $data['body'],
        ]);

        // troca imagem se vier nova
        if (isset($data['signature_path']) && $data['signature_path'] instanceof \Illuminate\Http\UploadedFile) {

            if (
                $model->signature_path &&
                Storage::disk('public')->exists($model->signature_path)
            ) {
                Storage::disk('public')->delete($model->signature_path);
            }

            $path = $data['signature_path']->store('signatures', 'public');

            $model->update([
                'signature_path' => $path
            ]);
        }

        return $model;
    }
}
