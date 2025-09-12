<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;

class DashboardController extends Controller
{
    protected ?string $logType = 'dashboard';
    protected ?string $nameEntity = "Dashboard";

    public function __construct(private DashboardService $service) {}

    public function dashboard(Request $request)
    {
        try {
            $this->logRequest();
            $data = $this->service->totalGeralData();
            $this->logToDatabase(
                type: $this->logType,
                level: 'info',
                customMessage: "Acessou o dashboard geral.",
            );
            return response()->json($data, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
