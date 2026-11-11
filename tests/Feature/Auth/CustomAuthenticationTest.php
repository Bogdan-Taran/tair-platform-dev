<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class CustomAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['/login', 'Войти'])]
    #[TestWith(['/register', 'Зарегистрироваться'])]
    #[TestWith(['/forgot-password', 'Отправить ссылку'])]
    #[TestWith(['/reset-password/token?email=test@example.com', 'Сохранить пароль'])]
    public function test_auth_routes_use_the_custom_forms(string $path, string $button): void
    {
        $this->get($path)->assertOk()->assertSee('data-auth-form', false)->assertSee($button)->assertSee('auth-forms.css')->assertDontSee('build/assets');
    }

    public function test_json_registration_initializes_a_missing_role_and_opens_dashboard(): void
    {
        $this->postJson('/register', $this->registrationData() + ['role' => 'admin'])->assertCreated()->assertJsonPath('redirect', '/dashboard');
        $user = User::where('email', 'anna@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue(Hash::check('Secret-pass-123', $user->password));
        $this->get('/dashboard')->assertOk()->assertSee('Анна');
    }

    public function test_duplicate_phone_is_a_validation_error_instead_of_500(): void
    {
        User::factory()->create(['phone' => '+79130000001']);
        $this->postJson('/register', $this->registrationData())->assertUnprocessable()->assertJsonValidationErrors('phone');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    #[TestWith(['email', 'bad-email'])]
    #[TestWith(['birthdate', '2999-01-01'])]
    #[TestWith(['agreement', '0'])]
    #[TestWith(['password_confirmation', 'different-password'])]
    public function test_registration_rejects_invalid_fields(string $field, string $value): void
    {
        $data = $this->registrationData();
        $data[$field] = $value;
        $this->postJson('/register', $data)->assertUnprocessable()->assertJsonValidationErrors($field === 'password_confirmation' ? 'password' : $field);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_normal_registration_preserves_fields_and_errors(): void
    {
        $data = $this->registrationData();
        $data['password_confirmation'] = 'wrong';
        $this->followingRedirects()->from('/register')->post('/register', $data)->assertOk()->assertSee('Пароли не совпадают.')->assertSee('Анна')->assertDontSee('Secret-pass-123');
    }

    public function test_modal_login_uses_json_and_keeps_the_intended_destination(): void
    {
        $user = User::factory()->create(['email' => 'anna@example.com']);
        $this->withSession(['url.intended' => '/documents'])->postJson('/login', ['email' => ' ANNA@EXAMPLE.COM ', 'password' => 'password'])
            ->assertOk()->assertJsonPath('redirect', route('documents'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_remains_a_validation_error_and_is_rate_limited(): void
    {
        User::factory()->create(['email' => 'anna@example.com']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/login', ['email' => 'anna@example.com', 'password' => 'wrong'])->assertUnprocessable()->assertJsonPath('errors.email.0', 'Неверная почта или пароль.');
        }
        $this->postJson('/login', ['email' => 'anna@example.com', 'password' => 'password'])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_password_reset_link_is_sent_and_token_can_be_used_only_once(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'anna@example.com']);
        $this->postJson('/forgot-password', ['email' => $user->email])->assertOk()->assertJsonPath('status', 'success');
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $data = ['token' => $notification->token, 'email' => $user->email, 'password' => 'New-secret-123', 'password_confirmation' => 'New-secret-123'];
            $this->get('/reset-password/'.$notification->token.'?email='.$user->email)->assertOk()->assertSee($user->email);
            $this->postJson('/reset-password', $data)->assertOk()->assertJsonPath('redirect', '/login');
            $this->assertTrue(Hash::check('New-secret-123', $user->fresh()->password));
            $this->postJson('/reset-password', $data)->assertUnprocessable()->assertJsonValidationErrors('email');
            $this->postJson('/login', ['email' => $user->email, 'password' => 'New-secret-123'])->assertOk();
            $this->assertAuthenticatedAs($user);

            return true;
        });
    }

    public function test_expired_reset_tokens_are_rejected(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $this->travel(61)->minutes();
        $this->postJson('/reset-password', ['token' => $token, 'email' => $user->email, 'password' => 'New-secret-123', 'password_confirmation' => 'New-secret-123'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_unknown_email_gets_a_generic_reset_response(): void
    {
        Notification::fake();
        $this->postJson('/forgot-password', ['email' => 'missing@example.com'])->assertOk()->assertJsonPath('status', 'success');
        Notification::assertNothingSent();
    }

    public function test_mail_outage_is_reported_without_a_500(): void
    {
        Password::shouldReceive('sendResetLink')->once()->andThrow(new \RuntimeException('Mail unavailable'));
        $this->postJson('/forgot-password', ['email' => 'anna@example.com'])->assertStatus(503)->assertJsonPath('message', 'Не удалось отправить письмо. Попробуйте позже.');
    }

    private function registrationData(): array
    {
        return ['firstname' => 'Анна', 'lastname' => 'Иванова', 'patronymic' => 'Петровна', 'email' => 'anna@example.com', 'phone' => '+79130000001', 'birthdate' => '1990-01-01', 'password' => 'Secret-pass-123', 'password_confirmation' => 'Secret-pass-123', 'agreement' => '1'];
    }
}
