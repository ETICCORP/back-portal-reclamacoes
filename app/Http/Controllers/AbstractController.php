<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AbstractService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    protected int $auditTtl = 900; // 15 minutos em segundos

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
            }

            $this->logAction();

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

            $service = $this->service->show($id);

            $this->logAction(params: $service);

            return response()->json($service);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Recurso não encontrado.'], Response::HTTP_NOT_FOUND);
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
    protected function logAction(string $level = 'info', mixed $params = [])
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
     * Executa o processo de log pragmático:
     * - Extrai os placeholders da definição
     * - Navega nos dados para encontrar os valores correspondentes
     * - Substitui os placeholders pela mensagem final
     * - Salva o log no banco de dados
     */
    private function executePragmaticLog(string $definition, string $level, mixed $dataSource)
    {
        $user = optional(auth()->user())->first_name ?? 'Sistema';
        if ($user === 'Sistema') return;

        // 1. Encontrar todos os placeholders que começam com ':' (ex: :nome, :categoria.titulo)
        preg_match_all('/:([a-zA-Z0-9_.]+)/', $definition, $matches);
        $placeholders = $matches[1] ?? [];

        $replacements = [];

        foreach ($placeholders as $path) {
            // 2. Extrair o valor do objeto de forma segura
            $value = $this->getValueFromData($dataSource, $path);

            // Formata o valor (se for null, vira vazio, se for objeto, vira string)
            $replacements[":$path"] = is_scalar($value) ? $value : (string)$value;
        }

        // 3. Faz o replace final
        $message = strtr($definition, $replacements);
        $finalMessage = "O usuário {$user} {$message}";

        $this->logToDatabase(
            type: $this->logType,
            level: $level,
            customMessage: $finalMessage
        );
    }

    /**
     * Navega de forma segura em um array ou objeto usando um caminho (ex: 'cliente.nome' ou 'categoria.titulo').
     * Retorna null se o caminho não existir ou se encontrar um tipo inesperado no meio do caminho.
     * Suporta tanto objetos quanto arrays, e pode ser usado para extrair valores para os logs pragmáticos.
     */
    private function getValueFromData($data, string $path)
    {
        // Transforma o caminho em array para percorrer (ex: ['cliente', 'nome'])
        foreach (explode('.', $path) as $segment) {
            if (is_object($data)) {
                $data = $data->{$segment} ?? null;
            } elseif (is_array($data)) {
                $data = $data[$segment] ?? null;
            } else {
                return null;
            }
        }
        return $data;
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

            // Busca o registro antes de deletar para ter os dados para o log
            $service = $this->service->show($id);
            $this->logAction(params: $service);

            // Realiza a deleção
            $this->service->destroy($id);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
            }

            return response()->json(['error' => 'Recurso não encontrado.'], Response::HTTP_NOT_FOUND);
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
            return response()->json(['error' => 'Recurso não encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
