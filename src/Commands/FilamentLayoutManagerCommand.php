<?php

namespace Asosick\ReorderWidgets\Commands;

use Illuminate\Console\Command;

class FilamentLayoutManagerCommand extends Command
{
    public $signature = 'filament-layout-manager';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
