<?php
    
    namespace App\Http\Controllers\Soluction;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Soluction\SoluctionService;
    use App\Http\Requests\Soluction\SoluctionRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class SoluctionController extends AbstractController
    {
        public function __construct(SoluctionService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(SoluctionRequest $request)
        {
            try {
                $this->logRequest();
                $soluction = $this->service->store($request->validated());
                return response()->json($soluction, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(SoluctionRequest $request, $id)
        {
            try {
                $this->logRequest();
                $soluction = $this->service->update($request->validated(), $id);
                return response()->json($soluction, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }