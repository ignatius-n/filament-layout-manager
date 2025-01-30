<?php

namespace Asosick\ReorderWidgets\Http\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Livewire\Component;

class ReorderComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?string $selectedComponent = null;

    public int $layoutCount = 3;
    public int $currentLayout = 1;

    public int $columns = 4;

    protected $listeners = ['updateLayout'];

    public $components = [];

    public bool $editMode = false;

    public array $settings = [];

    public function mount(?array $settings = []): void
    {
        $this->settings = $settings ?? config('reorder-widgets.default_settings');
        $this->selectedComponent = Arr::get($settings, 'components.0', null);
        $this->layoutCount = config('reorder-widgets.layoutCount');
        $this->load();
//        $this->ensureMaxColumnsRespected();
    }

    private function ensureMaxColumnsRespected(): void
    {
        if (! $this->components) {
            return;
        }
        foreach ($this->components as $key => $component) {
            if ($component['cols'] > $this->columns) {
                $this->components[$key]['cols'] = $this->columns;
            }
        }
    }

    protected function load(): void
    {
        $this->components = session('grid_layout', []);
    }

    public function toggleEditMode(): void
    {
        $this->editMode = ! $this->editMode;
    }

    public function toggleSize($id): void
    {
        if ($this->editMode) {
            $cols = $this->components[$this->currentLayout][$id]['cols'];
            $this->components[$this->currentLayout][$id]['cols'] = $cols === $this->columns ? 1 : $this->columns;
        }
    }

    public function increaseSize($id): void
    {
        if ($this->editMode) {
            $cols = $this->components[$this->currentLayout][$id]['cols'];
            $this->components[$this->currentLayout][$id]['cols'] = min($this->columns, $cols + 1);
        }
    }

    public function decreaseSize($id): void
    {
        if ($this->editMode) {
            $cols = $this->components[$this->currentLayout][$id]['cols'];
            $this->components[$this->currentLayout][$id]['cols'] = max(1, $cols - 1);
        }
    }

    public function addComponent(): void
    {
        $this->components[$this->currentLayout][uniqid()] = [
            'cols' => 1,
            'type' => $this->selectedComponent,
            'event_id' => count($this->components) + 1,
        ];
    }

    public function removeComponent($componentId): void
    {
        unset($this->components[$componentId]);
    }

    public function updateLayout($orderedIds): void
    {
        if (! isset($orderedIds) || ! is_array($orderedIds)) {
            return;
        }

        $sortedData = [];
        foreach ($orderedIds as $key) {
            if (isset($this->components[$this->currentLayout][$key])) {
                $sortedData[$this->currentLayout][$key] = $this->components[$this->currentLayout][$key];
            }
        }
        if (count($sortedData) === count($this->components)) {
            $this->components = array_merge($sortedData, $this->components);
        }
    }

    public function saveLayout(): void
    {
        $this->save();
        $this->saveNotification();
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->outlined()
            ->icon(fn () => ! $this->editMode ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open')
            ->label(fn () => ! $this->editMode ? 'Unlock Layout' : 'Lock Layout')
            ->action(fn () => $this->toggleEditMode());
    }

    public function addAction(): Action
    {
        return Action::make('add')
            ->outlined()
            ->color('success')
            ->icon('heroicon-m-plus')
            ->action(fn () => $this->addComponent());
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->outlined()
            ->color('danger')
            ->icon('heroicon-m-bookmark-square')
            ->action(fn () => $this->saveLayout());
    }


    public function selectLayout(int $layoutNumber): void
    {
        $this->currentLayout = $layoutNumber;
    }

    protected function save(): void
    {
        session(['grid_layout' => $this->components]);
    }

    protected function saveNotification(): void
    {
        Notification::make()
            ->title('Saved your layout')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('reorder-widgets::reorder-component');
    }
}
