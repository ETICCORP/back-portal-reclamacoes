<?php
    
    namespace App\Http\Controllers\Complaint\Proviver;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Complaint\Proviver\ComplaintProviderService;
    use App\Http\Requests\Complaint\Proviver\ComplaintProviderRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ComplaintProviderController extends AbstractController
    {
        public function __construct(ComplaintProviderService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ComplaintProviderRequest $request)
        {
            try {
                $this->logRequest();
                $complaintProvider = $this->service->store($request->validated());
                return response()->json($complaintProvider, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
     public function forwardComplaint(ComplaintProviderRequest $request)
        {
            try {
                $this->logRequest();
                $complaintProvider = $this->service->forwardComplaint($request->validated());
                return response()->json($complaintProvider, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        /**
         * Update the specified resource in storage.
         */
        public function update(ComplaintProviderRequest $request, $id)
        {
            try {
                $this->logRequest();
                $complaintProvider = $this->service->update($request->validated(), $id);
                return response()->json($complaintProvider, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }