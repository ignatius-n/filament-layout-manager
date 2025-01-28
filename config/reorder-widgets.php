<?php

// config for Asosick/ReorderWidgets
use Asosick\ReorderWidgets\Http\Livewire\ReorderComponent;
use Asosick\ReorderWidgets\Pages\ReorderPage;

return [
    'ReorderComponent' => ReorderComponent::class,
    'RorderPage' => ReorderPage::class,

    'settings' => [
        'grid_columns' => 2,
        'grid_rows' => 2,
    ]
];
