<?php declare(strict_types=1);
namespace Budgetcontrol\Workspace\ValueObjects;

use Budgetcontrol\Library\Entity\Wallet as EntityWallet;

final class Wallet {

    private string $name;
    private float $balance;
    private EntityWallet $walletType;
    private string $color;
    private int $currencyId;
    private bool $excludeFromStats;
    private string $uuid;

    public function __construct(string $name, float $balance, EntityWallet $walletType, string $color, int $currencyId, bool $excludeFromStats) {
        $this->name = $name;
        $this->balance = $balance;
        $this->walletType = $walletType;
        $this->color = $color;
        $this->currencyId = $currencyId;
        $this->excludeFromStats = $excludeFromStats;
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function getWalletType(): string {
        return $this->walletType->value;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getCurrencyId(): int {
        return $this->currencyId;
    }

    public function getExcludeFromStats(): bool {
        return $this->excludeFromStats;
    }

    public function getUuid(): string {
        return $this->uuid;
    }
}