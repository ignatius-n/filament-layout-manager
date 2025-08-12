<?php

namespace Asosick\FilamentLayoutManager\Pages;

use Filament\Pages\Page;
use Filament\Widgets\WidgetConfiguration;
use Livewire\Component;

abstract class LayoutManagerPage extends Page
{
    protected string $view = 'filament-layout-manager::layout-manager-page';

    protected array $settings = [];

    protected array $components = [];

    protected int $gridColumns;

    private bool $showLockButton;

    protected int $layoutCount;

    public bool $wrapInFilamentPage;

    public function __construct()
    {
        $this->layoutCount = config('filament-layout-manager.settings.layout_count');
        $this->gridColumns = config('filament-layout-manager.settings.grid_columns');
        $this->showLockButton = config('filament-layout-manager.settings.show_lock_button');
        $this->wrapInFilamentPage = config('filament-layout-manager.wrap_in_filament_page');
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
     * @return array<Component|WidgetConfiguration>
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

    public function shouldWrapInFilamentPage(): bool
    {
        return $this->wrapInFilamentPage;
    }

    protected function getViewData(): array
    {
        return [
            'settings' => [
                'heading' => $this->getHeading(),
                'components' => $this->unwrapWidgetConfiguration($this->getComponents()),
                'select_options' => $this->getComponentSelectOptions(),
                'grid_columns' => $this->getGridColumns(),
                'show_lock_button' => $this->showLockButton(),
                'layout_count' => $this->getLayoutCount(),
            ],
        ];
    }
}
