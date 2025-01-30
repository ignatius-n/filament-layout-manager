<?php

// config for Asosick/ReorderWidgets
use Asosick\ReorderWidgets\Http\Livewire\ReorderComponent;
use Asosick\ReorderWidgets\Pages\ReorderPage;

return [
    'ReorderComponent' => ReorderComponent::class,
    'RorderPage' => ReorderPage::class,
    'header' => 'Test Page',
    'default_settings' => [
        'components' => [],
        'selectOptions' => [],
        'gridColumns' => 2,
        'showEditButton' => true,
    ],
];
