<?php

namespace App\Http\Controllers\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\User\UserService;
use App\Http\Requests\User\AuthRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Controllers\AbstractController;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\changePasswordUser;
use App\Http\Requests\User\EnabledRequest;
use App\Http\Requests\User\Verify2faRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends AbstractController
{
    protected ?string $logType = 'user';
    protected ?string $nameEntity = "Usuário";
    protected ?string $fieldName = "first_name";

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    protected function logDefinitions(): array
    {
        return [
            'index' => 'visualizou a lista de usuários',
            'show' => 'visualizou os detalhes do usuário (nome: #:name)',
            'store' => 'registrou um novo usuário (nome: #:name)',
            'update' => 'atualizou os dados do usuário (nome: #:name)',
            'enabled' => 'atualizou o status do usuário (nome: #:name)',
            'changePasswordUser' => 'alterou a sua senha (nome: #:name)',

            'verify2fa' => 'iniciou sessão na aplicação',
        ];
    }

    /**
     * @unauthenticated
     */
    public function login(AuthRequest $request)
    {
        try {
            $this->logRequest();
            $token = $this->service->login($request);
            $this->logAction();
            return response()->json(['api_token' => $token], Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            $this->logToDatabase('error', 'Erro ao iniciar sessão do usuário.');
            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
    public function logout(Request $request)
    {
        try {
            $this->logRequest();
            $this->logAction();
            $this->service->logout($request);
            return response()->json(["message" => "Sessão terminada!"], Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Erro ao terminar sessão do usuário.',
            );
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function store(UserRequest $request)
    {
        try {
            $this->logRequest();
            $user = $this->service->store($request->validated());
            $this->logAction(params: $user);
            return response()->json($user, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Erro ao criar usuário.',
            );
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $this->logRequest();
            $user = $this->service->update($request->validated(), $id);
            $this->logAction(params: $user);
            return response()->json($user, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Usuário não encontrado.',
            );
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Erro ao atualizar usuário.',
            );
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function enabled(EnabledRequest $request, $id)
    {
        try {
            $this->logRequest();
            $user = $this->service->update($request->validated(), $id);
            $this->logToDatabase(
                type: 'user',
                level: 'info',
                customMessage: "Usuário {$user?->first_name} atualizado com sucesso.",
            );
            return response()->json($user, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Usuário não encontrado.',
            );
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Erro ao atualizar usuário.',
            );
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $this->logRequest();

            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return response()->json([
                    "message" => "A senha antiga está incorreta."
                ], 400);
            }

            $userPassword = $this->service->changePassword($request->validated());
            return response()->json($userPassword, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function me()
    {
        try {
            $this->logRequest();
            $user = $this->service->me();
            return response()->json($user, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @unauthenticated
     */
    public function verify2fa(Verify2faRequest $request)
    {
        try {
            $user = $this->service->verify2fa($request->validated());
            $this->logAction(params: $user);
            $this->logToDatabase(
                type: 'user',
                level: 'info',
                customMessage: 'Iniciou sessão na aplicação.',
            );
            return response()->json($user, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function changePasswordUser(changePasswordUser $request, $id)
    {
        try {
            $this->logRequest();
            $user = $this->service->changePasswordUser($request->validated(), $id);
            $this->logAction(params: $user);
            return response()->json($user, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Usuário não encontrado.',
            );
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            $this->logToDatabase(
                type: 'user',
                level: 'error',
                customMessage: 'Erro ao atualizar usuário.',
            );
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
