<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePageHitCounterTable extends AbstractMigration
{

    public function change(): void
    {
        $table = $this->table('page_hit_counter');
        $table->addColumn('ip', 'string')
            ->addColumn('city', 'string')
            ->addColumn('device', 'string')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
