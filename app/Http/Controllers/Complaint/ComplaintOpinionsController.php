<?php
    
    namespace App\Http\Controllers\Complaint;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Complaint\ComplaintOpinionsService;
    use App\Http\Requests\Complaint\ComplaintOpinionsRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ComplaintOpinionsController extends AbstractController
    {
        public function __construct(ComplaintOpinionsService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ComplaintOpinionsRequest $request)
        {
            try {
                $this->logRequest();
                 $data = $request->validated();
            // Atribui automaticamente o ID do utilizador autenticado
            $data['user_id'] = auth()->id();
                $complaintOpinions = $this->service->store(   $data);
                return response()->json($complaintOpinions, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(ComplaintOpinionsRequest $request, $id)
        {
            try {
                $this->logRequest();
                $complaintOpinions = $this->service->update($request->validated(), $id);
                return response()->json($complaintOpinions, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }