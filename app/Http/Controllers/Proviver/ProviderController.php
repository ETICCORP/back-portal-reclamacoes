<?php
    
    namespace App\Http\Controllers\Proviver;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Proviver\ProviderService;
    use App\Http\Requests\Proviver\ProviderRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class ProviderController extends AbstractController
    {
        public function __construct(ProviderService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(ProviderRequest $request)
        {
            try {
                $this->logRequest();
                $provider = $this->service->store($request->validated());
                return response()->json($provider, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(ProviderRequest $request, $id)
        {
            try {
                $this->logRequest();
                $provider = $this->service->update($request->validated(), $id);
                return response()->json($provider, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }