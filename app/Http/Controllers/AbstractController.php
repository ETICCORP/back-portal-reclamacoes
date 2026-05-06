<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AbstractService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Ao usar o AbstractController é necessário criar manualmente os métodos store e update.
 */

abstract class AbstractController extends Controller
{
    protected AbstractService $service;
    protected ?string $nameEntity = "Entidade";
    protected ?string $fieldName = "name";
    protected ?string $logType = 'entity';
    protected bool $logRequest = true;
    protected string $resourceName = 'registro';

    /**
     * Define o tempo padrão da janela de silêncio (1 hora).
     * Pode ser sobrescrito nos controllers filhos.
     */
    protected int $auditTtl = 3600;

    public function __construct(AbstractService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($this->logRequest) {
                $this->logRequest();
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    customMessage: "O usuario " . Auth::user()->first_name . " Visualizou todos os registros no módulo {$this->nameEntity}",
                );
            }

            $filters = $request['filters'] ?? $request['filtersV2'];
            $service = $this->service->index($request['paginate'], $filters, $request['orderBy'], $request['relationships']);
            return response()->json($service);
        } catch (Exception $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
            }
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int|string $id)
    {
        try {
            if ($this->logRequest) {
                $this->logRequest();
            }

            $this->logAction();

            $service = $this->service->show($id);

            if ($this->logRequest) {
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    customMessage: "O usuário " . auth()->user()->first_name . " visualizou o registro com a descrição: {$this->resolvePath($service,$this->fieldName)} no módulo {$this->nameEntity}",
                );
            }

            return response()->json($service);
        } catch (ModelNotFoundException $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'error',
                    customMessage: "Erro ao visualizar o registro {$id} em {$this->nameEntity}."
                );
            }
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
            }
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

/**
     * Ponto de entrada único para logs.
     * Chamado tanto pelos métodos mágicos (show, index) quanto manualmente.
     */
    protected function logAction(string $level = 'info', array $params = [])
    {
        // 1. Identifica o método que chamou o logAction (show, index, update, etc)
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $action = $trace[1]['function'] ?? 'unknown';

        // 2. Verifica se o filho definiu log para este método
        $definitions = method_exists($this, 'logDefinitions') ? $this->logDefinitions() : [];
        $definition = $definitions[$action] ?? null;

        // Se não houver definição, encerramos aqui (ignora o log silenciosamente)
        if (!$definition) {
            return;
        }

        // 3. Aplica a Trava de Silêncio (Cache)
        $url = request()->url();
        $userId = auth()->id() ?? 'guest';
        $className = class_basename($this); 
        
        $cacheKey = "audit_lock:" . md5("{$className}:{$userId}:{$url}:{$action}");

        // Só executa se o cache permitir (primeiro acesso na janela de tempo)
        if (Cache::add($cacheKey, true, $this->auditTtl)) {
            $this->executePragmaticLog($definition, $level, $params);
        }
    }

    /**
     * Faz a montagem final e grava no banco
     */
    private function executePragmaticLog(string $definition, string $level, array $params)
    {
        $user = optional(auth()->user())->first_name ?? 'Sistema';

        // Substituição de placeholders (:id, :nome, etc)
        $message = str_replace(
            array_map(fn($k) => ":$k", array_keys($params)),
            array_values($params),
            $definition
        );

        $finalMessage = "O usuário {$user} {$message}";

        $this->logToDatabase(
            type: $this->logType,
            level: $level,
            customMessage: $finalMessage
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            if ($this->logRequest) {
                $this->logRequest();
            }

            $this->service->destroy($id);

            if ($this->logRequest) {
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    customMessage: "Registro {$id} removido com sucesso em {$this->nameEntity}."
                );
            }

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'error',
                    customMessage: "Erro ao remover o registro {$id} em {$this->nameEntity}."
                );
            }
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
            }
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Método que será sobrescrito nos controllers filhos
    protected function logDefinitions(): array
    {
        return [];
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $id)
    {
        try {
            $service = $this->service->restore($id);
            return response()->json($service, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
