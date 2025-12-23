<?php

namespace App\DTOs;

class TwoFactorResultDTO
{
    public function __construct(
        public string $secret,
        public string $qrCodeUrl,
    ) {}
}
