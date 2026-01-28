<?php
    
    namespace App\Http\Controllers\Complaintattachment;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Complaintattachment\ComplaintattachmentService;
    use App\Http\Requests\Complaintattachment\ComplaintattachmentRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ComplaintattachmentController extends AbstractController
    {
        public function __construct(ComplaintattachmentService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ComplaintattachmentRequest $request)
        {
            try {
                $this->logRequest();
                $complaintattachment = $this->service->store($request->validated());
                return response()->json($complaintattachment, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(ComplaintattachmentRequest $request, $id)
        {
            try {
                $this->logRequest();
                $complaintattachment = $this->service->update($request->validated(), $id);
                return response()->json($complaintattachment, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }



        public function files($alertID)
        {
            try {
                $this->logRequest();
                $alertAttachment = $this->service->files($alertID);
                return response()->json($alertAttachment, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        public function showFile($id)
        {
            try {
                $filePath = $this->service->showFile($id); // Retorna caminho absoluto
        
                if (!file_exists($filePath)) {
                    return response()->json(['error' => 'Arquivo não encontrado.'], 404);
                }
        
                $mimeType = \Illuminate\Support\Facades\File::mimeType($filePath);
                $fileName = basename($filePath);
        
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    "message" => "Falha ao abrir o arquivo.",
                    "error" => $th->getMessage()
                ], 400);
            }
        }
    }