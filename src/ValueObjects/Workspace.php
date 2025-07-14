<?php
declare(strict_types=1);

namespace Budgetcontrol\Workspace\ValueObjects;

final class Workspace {

    private string $name;
    private int $currencyId;
    private int $paymentTypeId;
    private ?string $description;
    private ?string $uuid;

    public function __construct(string $name, int $currencyId, int $paymentTypeId) {
        $this->name = $name;
        $this->currencyId = $currencyId;
        $this->paymentTypeId = $paymentTypeId;
        $this->description = '';
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCurrencyId(): int {
        return $this->currencyId;
    }

    public function getPaymentTypeId(): int {
        return $this->paymentTypeId;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getUuid(): ?string {
        return $this->uuid;
    }
}