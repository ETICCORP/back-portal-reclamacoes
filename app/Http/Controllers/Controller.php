<?php

namespace App\Http\Controllers;

use App\Services\Log\LogService;
use App\Traits\DatabaseLogger;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests, DatabaseLogger;

    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    protected function logRequest($e = null)
    {
        $request = request()->all();
        $requestFiltered = $this->filterSensitiveData($request);
        if (app()->environment() !== 'testing') {
            if ($e) {
                Log::error($e->getMessage(), ['Payload: ' . json_encode($requestFiltered), 'Trace: ' . $e->getTraceAsString()]);
            } else {
                Log::info('URL: ' . request()->getRequestUri() . PHP_EOL . 'Método: ' . request()->method(), ['Payload: ' . json_encode($requestFiltered)]);
            }
        }
    }

    private function filterSensitiveData(array $requestData): array
    {
        $sensitiveData = ['password', 'senha', 'senhaAtual'];

        foreach ($sensitiveData as $key) {
            if (isset($requestData[$key])) {
                unset($requestData[$key]);
            }
        }

        return $requestData;
    }
    public function arrayChangeKeyCaseRecursive($array, $case = CASE_LOWER)
    {
        $array = array_change_key_case($array, $case);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->arrayChangeKeyCaseRecursive($value, $case);
            }
        }
        return $array;
    }

    public function resolvePath($object, $path)
    {
        $parts = explode('->', $path);
        foreach ($parts as $part) {
            if (!is_object($object) || !isset($object->{$part})) {
                return null;
            }
            $object = $object->{$part};
        }
        return $object;
    }
}
