<?php namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    protected function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}