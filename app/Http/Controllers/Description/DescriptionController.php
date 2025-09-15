<?php
    
    namespace App\Http\Controllers\Description;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Description\DescriptionService;
    use App\Http\Requests\Description\DescriptionRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class DescriptionController extends AbstractController
    {
        public function __construct(DescriptionService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(DescriptionRequest $request)
        {
            try {
                $this->logRequest();
                $description = $this->service->store($request->validated());
                return response()->json($description, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(DescriptionRequest $request, $id)
        {
            try {
                $this->logRequest();
                $description = $this->service->update($request->validated(), $id);
                return response()->json($description, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }