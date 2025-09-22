<?php
namespace App\Repositories\User;

use App\Models\User\User;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository extends AbstractRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }



    public function changePassword( $data)
    {
     // Atualiza a senha
    $user = Auth::user();
    $user->password = Hash::make($data['new_password']);

    if ($user->save()) {
        return response()->json(["message" => "Senha atualizada com sucesso!"]);
    }

    return response()->json(["message" => "Falha ao atualizar a senha."], 500);
    }

   
}