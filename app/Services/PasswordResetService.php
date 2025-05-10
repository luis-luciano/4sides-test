<?php

namespace App\Services;

use App\Models\PasswordReset;
use Carbon\Carbon;

class PasswordResetService
{
    protected ?PasswordReset $passwordResetUser;

    public function __construct(protected string $email)
    {
        $this->passwordResetUser = PasswordReset::byEmail($email)->first();
    }

    public function getPasswordResetUser(): ?PasswordReset
    {
        return $this->passwordResetUser;
    }

    public function generateVerificationCode()
    {
        $this->passwordResetUser = PasswordReset::updateOrCreate(
            ['email' => $this->email],
            ['verification_code' => random_int(100000, 999999), 'created_at' => now()]
        );
    }

    public function invalidVerificationCode(string $verificationCode): bool
    {
        return $this->passwordResetUser?->verification_code !== $verificationCode || $this->expiredVerificationCode();
    }


    public function expiredVerificationCode(): bool
    {
        $brokerDefaultConfig = config('auth.defaults.passwords');
        $broker = config("auth.passwords.{$brokerDefaultConfig}");
        $expire = $broker['expire'] ?? 0;

        if ($expire <= 0) {
            return true;
        }

        return Carbon::parse($this->passwordResetUser?->created_at)->addSeconds(
            $expire
        )->isPast();
    }
}
