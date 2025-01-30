<?php

namespace Asosick\FilamentLayoutManager\Pages;

use Filament\Pages\Page;
use Livewire\Component;

abstract class LayoutManagerPage extends Page
{
    protected static string $view = 'filament-layout-manager::layout-manager-page';

    protected array $settings = [];

    protected array $components = [];

    protected int $gridColumns;

    private bool $showEditButton;

    public function __construct()
    {
        $this->gridColumns = config('filament-layout-manager.default_settings.gridColumns');
        $this->showEditButton = config('filament-layout-manager.default_settings.showEditButton');
    }

    /**
     * @return array<Component>
     */
    protected function getComponents(): array
    {
        return $this->components;
    }

    /**
     * To override, provide an associative array of key->values for the drop down
     * return [classpath (selection key) => select_option_text, ... ]
     */
    protected function getComponentSelectOptions(): array
    {
        return collect($this->getComponents())
            ->mapWithKeys(fn ($component) => [$component => substr(strrchr($component, '\\'), 1)])
            ->toArray();
    }

    protected function getGridColumns(): int
    {
        return $this->gridColumns;
    }

    protected function showEditButton(): bool
    {
        return $this->showEditButton;
    }

    protected function getViewData(): array
    {
        return [
            'settings' => [
                'components' => $this->getComponents(),
                'selectOptions' => $this->getComponentSelectOptions(),
                'gridColumns' => $this->getGridColumns(),
                'showEditButton' => $this->showEditButton(),
            ],
        ];
    }
}
