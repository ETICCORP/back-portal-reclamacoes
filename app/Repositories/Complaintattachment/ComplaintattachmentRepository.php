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

    public function createComplaintAttachment(int $complaintId, string $path, string $randomName)
    {
        return $this->model->create([
            'fk_complaint' => $complaintId,
            'file' => $path,
            'name' => 'dn' . $randomName,
        ]);
    }

    public function showFile($id){
   
        try {// Busca o arquivo pelo ID ou lança uma exceção se não for encontrado
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

