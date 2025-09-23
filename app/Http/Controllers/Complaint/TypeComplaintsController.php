<?php
    
    namespace App\Http\Controllers\Complaint;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Complaint\TypeComplaintsService;
    use App\Http\Requests\Complaint\TypeComplaintsRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class TypeComplaintsController extends AbstractController
    {
        public function __construct(TypeComplaintsService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(TypeComplaintsRequest $request)
        {
            try {
                $this->logRequest();
                $typeComplaints = $this->service->store($request->validated());
                return response()->json($typeComplaints, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(TypeComplaintsRequest $request, $id)
        {
            try {
                $this->logRequest();
                $typeComplaints = $this->service->update($request->validated(), $id);
                return response()->json($typeComplaints, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }