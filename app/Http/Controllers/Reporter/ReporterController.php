<?php
    
    namespace App\Http\Controllers\Reporter;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Reporter\ReporterService;
    use App\Http\Requests\Reporter\ReporterRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ReporterController extends AbstractController
    {
        public function __construct(ReporterService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ReporterRequest $request)
        {
            try {
                $this->logRequest();
                $reporter = $this->service->store($request->validated());
                return response()->json($reporter, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(ReporterRequest $request, $id)
        {
            try {
                $this->logRequest();
                $reporter = $this->service->update($request->validated(), $id);
                return response()->json($reporter, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }