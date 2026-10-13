<?php

namespace Tests\Unit\User;

use App\Mail\UserCreatedMail;
use App\Mail\UserCredentialsChangedMail;
use App\Models\User\User;
use App\Repositories\User\UserRepository;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    public function test_new_user_email_links_to_the_frontend_homepage(): void
    {
        config(['app.frontend_url' => 'https://portal.example/']);
        config(['view.compiled' => sys_get_temp_dir()]);
        $this->app['view']->addLocation(dirname(__DIR__, 3).'/resources/views');

        $user = new User(['email' => 'user@example.com', 'first_name' => 'Utilizador']);
        $html = (new UserCreatedMail($user, 'TemporaryPassword123!'))->render();

        $this->assertStringContainsString('href="https://portal.example"', $html);
        $this->assertStringContainsString('Aceder à página inicial do Portal', $html);
    }

    public function test_updated_credentials_are_queued_for_email_delivery(): void
    {
        Mail::fake();

        $data = ['new_password' => 'UpdatedPassword123!'];
        $user = new User(['email' => 'user@example.com']);

        $repository = Mockery::mock(UserRepository::class);
        $repository->shouldReceive('changePasswordUser')
            ->once()
            ->with($data, 42)
            ->andReturn($user);

        $service = new UserService($repository);

        $service->changePasswordUser($data, 42);

        Mail::assertQueued(UserCredentialsChangedMail::class, fn (UserCredentialsChangedMail $mail) => $mail->user->email === $user->email
        );
    }

    public function test_credentials_changed_email_informs_the_user_without_disclosing_the_password(): void
    {
        config(['view.compiled' => sys_get_temp_dir()]);
        $this->app['view']->addLocation(dirname(__DIR__, 3).'/resources/views');

        $user = new User(['email' => 'user@example.com', 'first_name' => 'Utilizador']);
        $mail = new UserCredentialsChangedMail($user);
        $html = $mail->render();

        $this->assertSame('As credenciais da sua conta foram alteradas', $mail->subject);
        $this->assertStringContainsString('As credenciais de acesso à sua conta foram alteradas', $html);
        $this->assertStringNotContainsString('Bem-vindo(a)', $html);
        $this->assertStringNotContainsString('Senha actual', $html);
    }
}
