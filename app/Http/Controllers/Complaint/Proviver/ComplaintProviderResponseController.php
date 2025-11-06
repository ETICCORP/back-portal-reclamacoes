<?php
    
    namespace App\Http\Controllers\Complaint\Proviver;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Complaint\Proviver\ComplaintProviderResponseService;
    use App\Http\Requests\Complaint\Proviver\ComplaintProviderResponseRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ComplaintProviderResponseController extends AbstractController
    {
        public function __construct(ComplaintProviderResponseService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ComplaintProviderResponseRequest $request)
        {
            try {
                $this->logRequest();
                  $complaint = $this->service->storeData($request->validated());
                return response()->json($complaint, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(ComplaintProviderResponseRequest $request, $id)
        {
            try {
                $this->logRequest();
                $complaintProviderResponse = $this->service->update($request->validated(), $id);
                return response()->json($complaintProviderResponse, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

      
    }