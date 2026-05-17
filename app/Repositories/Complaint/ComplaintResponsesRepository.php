<?php

namespace App\Repositories\Complaint;

use App\Enum\ClaimStatus;
use App\Mail\ComplaintResponseMail;
use App\Models\Complaint\ComplaintResponses;
use App\Models\Complaint\ModelEmail\ModelEmail;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintResponsesRepository extends AbstractRepository
{
    public $complaintRepository;
    protected ComplaintAttachmentRepository $attachments;

    public function __construct(ComplaintResponses $model, ComplaintAttachmentRepository $attachments, ComplaintRepository $complaintRepository)
    {
        parent::__construct($model);
        $this->attachments = $attachments;
        $this->complaintRepository = $complaintRepository;
    }

    public function complaintResponse(array $data)
    {
        $modelEmail = ModelEmail::find($data['model_id']);

        DB::beginTransaction();

        $complaint = $this->model->create([
            'user_id'        => $data['user_id'],
            'complaint_id'   => $data['complaint_id'],
            'subject'        => $data['subject'],
            'body'           => $data['body'],
            'signature_path' => $modelEmail->signature_path,
        ]);

        $complaint->load([
            "complaint",
            "user"
        ]);

        // 1. Centralizando o status com o Enum
        $status = ClaimStatus::RESPONDIDA_RECLAMANTE;

        $data['status']  = $status->value; // 'Respondida ao Reclamante'
        $data['comment'] = $data['body'];

        // 2. Atualiza o repositório passando a string extraída do Enum
        $this->complaintRepository->updateStatus($data, $data['complaint_id']);

        DB::commit();

        try {
            Mail::to($complaint->complaint->email)->queue(new ComplaintResponseMail($complaint));
        } catch (\Throwable $th) {
            logs()->error("Erro ao enviar email de resposta para Reclamação #{$complaint->complaint->code}", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
        }

        return $complaint;
    }


    /**
     * Faz o upload da assinatura digital em Base64 e vincula ao registro.
     *
     * @param string $base64Image
     * @param int $responseId
     * @return string|null O caminho do arquivo salvo ou null em caso de falha
     */
    public function uploadSignature(string $base64Image, int $responseId): ?string
    {
        Log::debug("🖋️ Iniciando upload da assinatura digital para resposta #{$responseId}");

        // 1. Busca logo o registro para evitar processar imagem se o ID não existir
        $response = $this->model::find($responseId);

        if (!$response) {
            Log::warning("⚠️ Resposta de reclamação não encontrada. Upload abortado.", ['response_id' => $responseId]);
            return null;
        }

        // 2. Validação da estrutura do Base64
        if (!preg_match('/^data:(.*?);base64,(.*)$/', $base64Image, $matches)) {
            Log::warning("❌ String não corresponde ao padrão Data-URI Base64 esperado", [
                'preview' => Str::limit($base64Image, 50)
            ]);
            return null;
        }

        $mimeType = $matches[1] ?? 'image/png';
        $base64Data = $matches[2];

        // 3. Validação estrita do MimeType (Evita arquivos maliciosos ou extensões estranhas)
        $allowedMimes = [
            'image/png'  => 'png',
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg'
        ];

        if (!array_key_exists($mimeType, $allowedMimes)) {
            Log::warning("⚠️ Formato de imagem não permitido para assinatura", ['mime_type' => $mimeType]);
            return null;
        }

        $extension = $allowedMimes[$mimeType];

        // 4. Decodificação dos dados binários
        $fileData = base64_decode($base64Data, true);
        if ($fileData === false) {
            Log::error("❌ Falha crítica ao decodificar os dados binários do Base64", ['response_id' => $responseId]);
            return null;
        }

        // 5. Geração de nomes e caminhos únicos
        $filename = 'signature_' . Str::random(20) . '.' . $extension;
        $path = "complaint_signatures/{$responseId}/{$filename}";

        // 6. Operação Atómica (Garante consistência total entre Storage e Banco)
        try {
            return DB::transaction(function () use ($path, $fileData, $response) {

                // Primeiro guarda o ficheiro no disco
                Storage::disk('public')->put($path, $fileData);

                // Se o arquivo antigo existir, remove-o para evitar lixo no servidor
                if ($response->signature_path) {
                    Storage::disk('public')->delete($response->signature_path);
                }

                // Atualiza o registro na base de dados
                $response->update(['signature_path' => $path]);

                Log::info("💾 Assinatura gravada e registro atualizado com sucesso", [
                    'response_id' => $response->id,
                    'path'        => $path
                ]);

                return $path;
            });
        } catch (\Throwable $e) {
            // Caso falhe o banco, removemos o arquivo recém-criado do Storage (Rollback manual do arquivo)
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error("🔥 Erro transacional ao salvar assinatura digital para resposta {$responseId}", [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    public function sendEmailResponse($id)
    {

        $complaint = $this->model::with('complaint', 'user')->find($id);

        try {
            Mail::to($complaint->complaint->email)->queue(new ComplaintResponseMail($complaint));
            return $complaint;
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar o email.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
