<?php

// config for Asosick/FilamentLayoutManager
use Asosick\ReorderWidgets\Http\Livewire\LayoutManager;
use Asosick\ReorderWidgets\Pages\LayoutManagerPage;

return [
    'LayoutManager' => LayoutManager::class,
    'RorderPage' => LayoutManagerPage::class,
    'layoutCount' => 3,
    'header' => 'Test Page',
    'default_settings' => [
        'components' => [],
        'selectOptions' => [],
        'gridColumns' => 2,
        'showEditButton' => true,
    ],
];
