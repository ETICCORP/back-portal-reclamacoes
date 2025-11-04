<?php
    
    namespace App\Http\Controllers\Complaint\ModelEmail;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Complaint\ModelEmail\ModelEmailService;
    use App\Http\Requests\Complaint\ModelEmail\ModelEmailRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ModelEmailController extends AbstractController
    {
        public function __construct(ModelEmailService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ModelEmailRequest $request)
        {
            try {
                $this->logRequest();
                $modelEmail = $this->service->store($request->validated());
                return response()->json($modelEmail, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
     public function complaintResponse(ModelEmailRequest $request)
    {
        try {
      
            $this->logRequest();
            $data = $request->validated();
            // Atribui automaticamente o ID do utilizador autenticado
       return     $data['user_id'] = auth()->id();
            $complaintResponses = $this->service->complaintResponse(   $data );
            return response()->json($complaintResponses, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
         * Update the specified resource in storage.
         */
        public function update(ModelEmailRequest $request, $id)
        {
            try {
                $this->logRequest();
                $modelEmail = $this->service->update($request->validated(), $id);
                return response()->json($modelEmail, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }