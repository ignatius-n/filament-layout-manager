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

    private bool $showLockButton;

    protected int $layoutCount;

    public function __construct()
    {
        $this->layoutCount = config('filament-layout-manager.settings.layout_count');
        $this->gridColumns = config('filament-layout-manager.settings.grid_columns');
        $this->showLockButton = config('filament-layout-manager.settings.show_lock_button');
    }

    private function unwrapWidgetConfiguration(array $components): array
    {
        $unwrappedComponents = [];
        foreach ($components as $component) {
            if ($component instanceof WidgetConfiguration) {
                $unwrappedComponents[] = [
                    'widget_class' => $component->widget,
                    'data' => [...$component->widget::getDefaultProperties(), ...$component->getProperties()],
                ];
            } else {
                $unwrappedComponents[] =
                    ['widget_class' => $component, 'data' => []];
            }
        }

        return $unwrappedComponents;
    }

    /**
     * To override, provide an array of drop down names in the order you specified in getComponents())
     */
    protected function getComponentSelectOptions(): array
    {
        return collect($this->getComponents())
            ->map(function ($component) {
                $component_name = $component instanceof WidgetConfiguration ? $component->widget : $component;

                return substr(strrchr($component_name, '\\'), 1);
            })
            ->toArray();
    }

    /**
     * @return array<Component>
     */
    protected function getComponents(): array
    {
        return $this->components;
    }

    protected function getGridColumns(): int
    {
        return $this->gridColumns;
    }

    protected function showLockButton(): bool
    {
        return $this->showLockButton;
    }

    protected function getLayoutCount(): int
    {
        return $this->layoutCount;
    }

    protected function getViewData(): array
    {
        return [
            'settings' => [
                'components' => $this->unwrapWidgetConfiguration($this->getComponents()),
                'select_options' => $this->getComponentSelectOptions(),
                'grid_columns' => $this->getGridColumns(),
                'show_lock_button' => $this->showLockButton(),
                'layout_count' => $this->getLayoutCount(),
            ],
        ];
    }
}
