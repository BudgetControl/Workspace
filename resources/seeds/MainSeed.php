<?php

use Budgetcontrol\Seeds\Resources\Seed;
use Phinx\Seed\AbstractSeed;
use Budgetcontrol\Seeds\Resources\Seeds;

class MainSeed extends AbstractSeed
{

    public function run(): void
    {
        $seed = new Seed();
        $seed->runAllSeeds();
    }
}
