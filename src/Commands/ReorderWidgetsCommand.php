<?php

namespace Asosick\ReorderWidgets\Commands;

use Illuminate\Console\Command;

class ReorderWidgetsCommand extends Command
{
    public $signature = 'reorder-widgets';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
