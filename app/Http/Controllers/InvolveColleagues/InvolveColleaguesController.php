<?php
    
    namespace App\Http\Controllers\InvolveColleagues;
    
    use App\Http\Controllers\AbstractController;
    use App\Services\InvolveColleagues\InvolveColleaguesService;
    use App\Http\Requests\InvolveColleagues\InvolveColleaguesRequest;
    use Exception;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Illuminate\Http\Response;
    
    class InvolveColleaguesController extends AbstractController
    {
        public function __construct(InvolveColleaguesService $service)
        {
            $this->service = $service;
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(InvolveColleaguesRequest $request)
        {
            try {
                $this->logRequest();
                $involveColleagues = $this->service->store($request->validated());
                return response()->json($involveColleagues, Response::HTTP_CREATED);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(InvolveColleaguesRequest $request, $id)
        {
            try {
                $this->logRequest();
                $involveColleagues = $this->service->update($request->validated(), $id);
                return response()->json($involveColleagues, Response::HTTP_OK);
            } catch (ModelNotFoundException $e) {
                $this->logRequest($e);
                return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->logRequest($e);
                return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }