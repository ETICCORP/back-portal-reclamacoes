<?php

namespace App\Http\Controllers\Log;

use App\Services\Log\LogService;
use App\Http\Controllers\AbstractController;


class LogController extends AbstractController
{
    protected bool $logRequest = false;
    public function __construct(LogService $service)
    {
        $this->service = $service;
    }
}
