<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Permission\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission\Permission;
use App\Models\User\User;

class PermissionSeed extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLES (Perfil de Acesso)
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::updateOrCreate(
            ['name' => 'Administrador'],
            [
                'description' => 'Acesso total às configurações, gestão de usuários e relatórios estratégicos.',
                'is_active' => true
            ]
        );

        $providerRole = Role::updateOrCreate(
            ['name' => 'Provedor'],
            [
                'description' => 'Acesso operacional para gestão de reclamações e dados da instituição.',
                'is_active' => true
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | MÓDULOS E PERMISSÕES (Configuração UX)
        |--------------------------------------------------------------------------
        | O array agora contém a definição amigável de cada módulo.
        */
        $modules = [
            'Usuário'              => 'Gerenciar contas e níveis de acesso.',
            'Estatística'          => 'Analisar indicadores e gráficos de desempenho.',
            'Regra'                => 'Configurar normas e diretrizes do portal.',
            'Reclamações'          => 'Monitorar o fluxo geral de reclamações.',
            'Perfil'               => 'Gerir informações e preferências da conta.',
            'Alertas'              => 'Configurar notificações e avisos do sistema.',
            'Histórico'            => 'Consultar logs e rastreabilidade de ações.',
            'Provedor'             => 'Administrar dados cadastrais da instituição.',
            'Provedor Reclamações' => 'Responder e tratar reclamações direcionadas.',
        ];

        $friendlyOperations = [
            'show'   => 'Visualizar',
            'create' => 'Cadastrar',
            'edit'   => 'Editar',
            'delete' => 'Remover',
        ];

        $adminPermissions = [];
        $providerPermissions = [];

        foreach ($modules as $moduleName => $moduleContext) {
            foreach ($friendlyOperations as $operation => $label) {

                $permissionName = Helper::formatarString($moduleName) . "-$operation";

                // Descrição amigável para o Frontend
                // Ex: "Visualizar Usuário: Gerenciar contas e níveis de acesso."
                $description = "{$label} {$moduleName}: {$moduleContext}";

                $permission = Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'description' => $description,
                        'is_active'   => true
                    ]
                );

                // Lógica de Separação de Perfis
                if (in_array($moduleName, ['Provedor', 'Provedor Reclamações'])) {
                    $providerPermissions[] = $permission->id;
                } else {
                    $adminPermissions[] = $permission->id;
                }
            }
        }

        // Sincroniza permissões aos perfis
        $adminRole->permissions()->sync($adminPermissions);
        $providerRole->permissions()->sync($providerPermissions);

        /*
        |--------------------------------------------------------------------------
        | CRIAÇÃO DE USUÁRIOS
        |--------------------------------------------------------------------------
        */
        // Verifica se já existem usuários para evitar duplicação
        if (User::count() === 0) {
            $this->seedUsers($adminRole->id, $providerRole->id);
        }
    }

    private function seedUsers(int $adminRoleId, int $providerRoleId): void
    {
        $users = [
            [
                'fname' => 'Evangelina',
                'lname' => 'Jaime',
                'email' => 'evangelina.jaime@keepcomply.co.ao',
                'pass'  => 'Evangelina123@@',
                'role'  => $adminRoleId
            ],
            [
                'fname' => 'Vicente',
                'lname' => 'Eduardo',
                'email' => 'vicente.eduardo@etic.co.ao',
                'pass'  => 'Vicente123@@',
                'role'  => $adminRoleId
            ],
            [
                'fname' => 'Evangelina Jaime',
                'lname' => 'Provedor',
                'email' => 'keepcomply838@gmail.com',
                'pass'  => 'Evangelina12Provedor@@',
                'role'  => $providerRoleId
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'first_name' => $u['fname'],
                    'last_name'  => $u['lname'],
                    'phone'      => '922222222',
                    'password'   => bcrypt($u['pass']),
                    'role_id'    => $u['role'],
                    'is_active'  => true
                ]
            );
        }
    }
}
