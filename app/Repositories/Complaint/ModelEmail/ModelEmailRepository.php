<?php

namespace App\Repositories\Complaint\ModelEmail;

use App\Models\Complaint\ModelEmail\ModelEmail;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModelEmailRepository extends AbstractRepository
{
    public function __construct(ModelEmail $model)
    {
        parent::__construct($model);
    }
    public function complaintResponse(array $data)
    {

        $complaint = $this->model->create([
            'subject' => $data['subject'],
            'name' => $data['name'],
            'body' => $data['body'],
            'signature_path' => $data['signature_path'],
          'user_id' => $data['user_id'],

        ]);
        // 📎 Anexos
        $this->uploadSignature($data['signature_path'] ?? null, $complaint->id);

        $complaint->load([
            "user"
        ]);

        return $complaint;
    }

    public function uploadSignature(string $base64Image, int $responseId): ?string
    {
        Log::debug("🖋️ Iniciando upload da assinatura digital para resposta #{$responseId}");
        try {
            // Valida se o conteúdo é uma string
            if (!is_string($base64Image)) {
                Log::warning("⚠️ A assinatura não é uma string base64", ['value' => $base64Image]);
                return null;
            }

            // Verifica se está no formato Base64 padrão
            if (!preg_match('/^data:(.*?);base64,(.*)$/', $base64Image, $matches)) {
                Log::warning("❌ String não corresponde ao padrão Base64 esperado", [
                    'preview' => substr($base64Image, 0, 50)
                ]);
                return null;
            }

            $mimeType = $matches[1] ?? 'image/png';
            $fileData = base64_decode($matches[2], true);

            if ($fileData === false) {
                throw new \Exception("Falha ao decodificar Base64 da assinatura.");
            }

            Log::debug("✅ Base64 da assinatura decodificado com sucesso", [
                'mimeType' => $mimeType,
                'size'     => strlen($fileData)
            ]);

            // Determina a extensão do ficheiro
            $extension = explode('/', $mimeType)[1] ?? 'png';
            $randomName = 'signature_' . now()->timestamp . '_' . uniqid() . '.' . $extension;
            $path = "complaint_signatures/{$responseId}/{$randomName}";

            // Salva no storage (public)
            Storage::disk('public')->put($path, $fileData);

            Log::info("📂 Assinatura salva com sucesso no storage", ['path' => $path]);

            // Atualiza o caminho no banco
            $response = $this->model::find($responseId);
            if ($response) {
                $response->update(['signature_path' => $path]);
                Log::info("💾 Caminho da assinatura atualizado no banco", ['response_id' => $response->id]);
            } else {
                Log::warning("⚠️ Resposta de reclamação não encontrada", ['response_id' => $responseId]);
            }

            return $path;
        } catch (\Throwable $e) {
            Log::error("🔥 Erro ao salvar assinatura digital para resposta {$responseId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
