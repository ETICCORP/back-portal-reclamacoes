<?php

namespace App\Http\Controllers\Log;

use App\Services\Log\LogService;
use App\Http\Controllers\AbstractController;
use Illuminate\Http\Request;
use Exception;

class LogController extends AbstractController
{

    public function __construct(LogService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            // Captura de filtros
            $filters = $request['filters'] ?? $request['filtersV2'];

            // Chamada ao serviço (LogService)
            $result = $this->service->index(
                $request->input('paginate', 15),
                $filters,
                $request->input('orderBy', ['id' => 'desc']),
                $request->input('relationships', []),
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
