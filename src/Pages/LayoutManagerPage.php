<?php

namespace Asosick\FilamentLayoutManager\Pages;

use Filament\Pages\Page;
use Filament\Widgets\WidgetConfiguration;
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

    private function unWrapWidgetConfiguration(array $components): array{
        $unwrappedComponents = [];
        foreach ($components as $component) {
            if($component instanceof WidgetConfiguration) {
                $unwrappedComponents[] = [
                    'widget_class' => $component->widget,
                    'data' => [...$component->widget::getDefaultProperties(), ...$component->getProperties()]
                ];
            }
            else{
                $unwrappedComponents[] =
                    ['widget_class' => $component, 'data'=>[]];
            }
        }
        return $unwrappedComponents;
    }

    /**
     * To override, provide an associative array of key->values for the drop down
     * return [classpath (selection key) => select_option_text, ... ]
     */
    protected function getComponentSelectOptions(): array
    {
        return collect($this->getComponents())
            ->mapWithKeys(function ($component) {
                $component_name = $component instanceof WidgetConfiguration ? $component->widget : $component;
                return [
                    $component_name => substr(strrchr($component_name, '\\'), 1)
                ];
            })
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
                'components' => $this->unWrapWidgetConfiguration($this->getComponents()),
                'selectOptions' => $this->getComponentSelectOptions(),
                'gridColumns' => $this->getGridColumns(),
                'showEditButton' => $this->showEditButton(),
            ],
        ];
    }
}
