<?php
    
    namespace App\Http\Controllers\Proviver\grupProveder;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\Proviver\grupProveder\grupProvederService;
    use App\Http\Requests\Proviver\grupProveder\grupProvederRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class grupProvederController extends AbstractController
    {
        public function __construct(grupProvederService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
       


         public function store(grupProvederRequest $request)
        {
            try {
                $this->logRequest();
                $userGrupoAlert = $this->service->storeMany($request->validated());
                return response()->json($userGrupoAlert, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(grupProvederRequest $request, $id)
        {
            try {
                $this->logRequest();
                $grupProveder = $this->service->update($request->validated(), $id);
                return response()->json($grupProveder, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }