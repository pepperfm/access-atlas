<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create
        {--name= : Имя пользователя}
        {--email= : Email пользователя}
        {--password= : Пароль}
        {--role= : App-role}
        {--status= : Статус пользователя}
        {--force : Создать без подтверждения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать пользователя с app-role через Laravel Prompts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->resolveName();
        $email = $this->resolveEmail();
        $password = $this->resolvePassword();
        $role = $this->resolveRole();
        $status = $this->resolveStatus();

        if (! $this->option('force')) {
            $confirmed = confirm(
                label: "Создать пользователя {$email} с ролью {$role}?",
                default: true,
            );

            if (! $confirmed) {
                info('Создание пользователя отменено.');

                return self::SUCCESS;
            }
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'status' => UserStatus::from($status),
        ]);

        $user->syncRoles([$role]);

        info("Пользователь {$user->email} создан.");
        info("Роль: {$this->roleLabel($role)}. Статус: {$this->statusLabel($status)}.");

        return self::SUCCESS;
    }

    private function resolveName(): string
    {
        $name = (string) ($this->option('name') ?: text(
            label: 'Имя пользователя',
            placeholder: 'Например, Иван Петров',
            required: 'Имя обязательно.',
            validate: fn(string $value) => mb_strlen(trim($value)) >= 2
                ? null
                : 'Имя должно быть не короче 2 символов.',
        ));

        return trim($name);
    }

    private function resolveEmail(): string
    {
        $email = strtolower(trim((string) ($this->option('email') ?: text(
            label: 'Email пользователя',
            placeholder: 'user@example.com',
            required: 'Email обязателен.',
            validate: fn(string $value) => $this->validateEmail($value),
        ))));

        $error = $this->validateEmail($email);

        if ($error) {
            error($error);
            exit(self::FAILURE);
        }

        return $email;
    }

    private function resolvePassword(): string
    {
        $passwordOption = $this->option('password');

        $password = (string) ($passwordOption ?: password(
            label: 'Пароль',
            placeholder: 'Минимум 8 символов',
            required: 'Пароль обязателен.',
            validate: fn(string $value) => mb_strlen($value) >= 8
                ? null
                : 'Пароль должен быть не короче 8 символов.',
        ));

        if (mb_strlen($password) < 8) {
            error('Пароль должен быть не короче 8 символов.');
            exit(self::FAILURE);
        }

        return $password;
    }

    private function resolveRole(): string
    {
        $role = (string) ($this->option('role') ?: select(
            label: 'App-role пользователя',
            options: AppRole::options(),
            default: AppRole::Developer->value,
        ));

        if (! in_array($role, AppRole::values(), true)) {
            error('Выбрана неизвестная роль.');
            exit(self::FAILURE);
        }

        return $role;
    }

    private function resolveStatus(): string
    {
        $status = (string) ($this->option('status') ?: select(
            label: 'Статус пользователя',
            options: [
                UserStatus::Active->value => $this->statusLabel(UserStatus::Active->value),
                UserStatus::Inactive->value => $this->statusLabel(UserStatus::Inactive->value),
                UserStatus::Suspended->value => $this->statusLabel(UserStatus::Suspended->value),
            ],
            default: UserStatus::Active->value,
        ));

        if (! in_array($status, UserStatus::values(), true)) {
            error('Выбран неизвестный статус.');
            exit(self::FAILURE);
        }

        return $status;
    }

    private function validateEmail(string $email): ?string
    {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', Rule::unique(User::class, 'email')]],
        );

        return $validator->fails()
            ? $validator->errors()->first('email')
            : null;
    }

    private function roleLabel(string $role): string
    {
        return AppRole::from($role)->label();
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            UserStatus::Active->value => 'Активен',
            UserStatus::Inactive->value => 'Неактивен',
            UserStatus::Suspended->value => 'Заблокирован',
            default => $status,
        };
    }
}
