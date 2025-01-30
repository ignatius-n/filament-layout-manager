<?php

// config for Asosick/FilamentLayoutManager
use Asosick\FilamentLayoutManager\Http\Livewire\LayoutManager;
use Asosick\FilamentLayoutManager\Pages\LayoutManagerPage;

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
