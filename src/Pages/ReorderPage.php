<?php

namespace Asosick\ReorderWidgets\Pages;

use Filament\Pages\Page;
use Livewire\Component;

abstract class ReorderPage extends Page
{
    protected static string $view = 'reorder-widgets::reorder-page';

    protected array $settings = [];

    protected array $components = [];

    protected int $gridColumns;

    protected int $gridRows;

    protected bool $allowMultipleComponents = true;

    private bool $showEditButton = true;

    public function __construct()
    {
        $this->gridColumns = config('reorder-widgets.settings.grid_columns');
        $this->gridRows = config('reorder-widgets.settings.grid_rows');
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

    protected function allowMultipleComponents(): bool
    {
        return $this->allowMultipleComponents;
    }

    protected function getGridColumns(): int
    {
        return $this->gridColumns;
    }

    protected function getGridRows(): int
    {
        return $this->gridRows;
    }

    protected function showEditButton(): bool
    {
        return $this->showEditButton;
    }

    //    protected function getHeaderActions(): array
    //    {
    //        return [
    //
    //        ];
    //    }

    protected function getViewData(): array
    {
        return [
            'settings' => [
                'components' => $this->getComponents(),
                'selectOptions' => $this->getComponentSelectOptions(),
                'gridColumns' => $this->getGridColumns(),
                'gridRows' => $this->getGridRows(),
                'showEditButton' => $this->showEditButton(),
            ],
        ];
    }
}
