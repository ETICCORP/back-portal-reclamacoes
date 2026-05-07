<?php

namespace App\Services\User;

use App\Mail\TwoFactorCodeMail as MailTwoFactorCodeMail;
use App\Models\Log\Log;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Services\AbstractService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserService extends AbstractService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    public function store(array $data)
    {
        // 1. Gerar uma senha aleatória caso não venha do front
        $password = Str::random(12); // gera senha aleatória de 12 caracteres
        $data['password'] = Hash::make($password);

        // 2. Salvar o usuário no repositório
        $user = $this->repository->store($data);

        try {
            // 3. Enviar a senha por email para o usuário
            Mail::to($data['email'])->send(new \App\Mail\UserCreatedMail($user, $password));
        } catch (\Throwable $th) {
            Log::error('Erro ao enviar email para o utilizador', [
                'email' => $data['email'],
                'error' => $th->getMessage(),
            ]);
        }

        return $user;
    }
    public function changePassword(array $data)
    {
        return $this->repository->changePassword($data);
    }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email ou senha incorretos'], 401);
        }
        if ($user->is_active == 0) {
            return response()->json(['message' => 'Conta bloqueada. Por favor, entre em contato com os administradores do sistema para obter assistência.'], 400);
        }

        // Gera código 2FA
        $user->generateTwoFactorCode();

        // Envia email
        Mail::to($user->email)->send(new MailTwoFactorCodeMail($user));
        $token = $user->createToken("fortaleza_SEGUROS")->plainTextToken;

        //return $token;
        return response()->json([
            'message' => 'Código 2FA enviado para seu email',
            'user_id' => $user->id // necessário para validar o código depois
        ]);
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return true;
    }

    public function me()
    {
        return Auth::user()->load('role', 'role.permissions');
    }

    public function forgotPassword(string $userEmail): void
    {
        $userEmail = mb_strtolower($userEmail);

        $user = User::query()
            ->where('email', $userEmail)
            ->first();

        if ($user->google_id !== null) return;

        Password::sendResetLink(['email' => $userEmail]);
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset($data, function (User $user, string $password) {
            $user->update(['password' => $password]);
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new Exception(trans($status));
        }
    }

    // Método para validar o código 2FA e autenticar o usuário
    public function verify2fa(array $request)
    {
        $code = $request['code'] ?? null;

        $user = User::where('two_factor_code', $code)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Código inválido.'], 401);
        }

        // 2. Verificação de expiração
        if ($user->two_factor_expires_at->isPast()) {
            return response()->json(['status' => 'error', 'message' => 'Código expirou.'], 401);
        }

        // 3. Autentica o usuário manualmente na instância do Auth para esta requisição
        Auth::setUser($user);

        // 4. Limpa o código 2FA
        $user->resetTwoFactorCode();

        // 5. Gera o token definitivo para o frontend usar nas próximas rotas
        $token = $user->createToken("NOSSA_SEGUROS")->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Autenticação 2FA validada.',
            'token' => $token,
            'user' => [
                'id' => Auth::id(), // Já funciona por causa do setUser
                'name' => Auth::user()->name
            ]
        ]);
    }

    public function changePasswordUser(array $data, $id)
    {
        return $this->repository->changePasswordUser($data, $id);
    }
}
