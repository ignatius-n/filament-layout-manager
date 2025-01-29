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

class ReorderComponent extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?string $selectedComponent = null;

    public int $columns = 4;

    protected $listeners = ['updateLayout'];

    public $components = [];

    public bool $editMode = false;

    public array $settings = [];

    public function mount(?array $settings)
    {
        $this->settings = $settings ?? config('reorder-widgets.default_settings');
        $this->selectedComponent = Arr::get($settings, 'components.0', null);
        $this->load();
        $this->ensureMaxColumnsRespected();
    }

    private function ensureMaxColumnsRespected()
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

    protected function load()
    {
        $this->components = session('grid_layout', []);
    }

    public function toggleEditMode()
    {
        $this->editMode = ! $this->editMode;
    }

    public function toggleSize($id)
    {
        if ($this->editMode) {
            $cols = $this->components[$id]['cols'];
            $this->components[$id]['cols'] = $cols === $this->columns ? 1 : $this->columns;
        }
    }

    public function increaseSize($id)
    {
        if ($this->editMode) {
            $cols = $this->components[$id]['cols'];
            $this->components[$id]['cols'] = min($this->columns, $cols + 1);
        }
    }

    public function decreaseSize($id)
    {
        if ($this->editMode) {
            $cols = $this->components[$id]['cols'];
            $this->components[$id]['cols'] = max(1, $cols - 1);
        }
    }

    public function addComponent()
    {
        $this->components[uniqid()] = [
            'cols' => 1,
            'type' => $this->selectedComponent,
            'event_id' => count($this->components) + 1,
        ];
    }

    public function removeComponent($componentId)
    {
        unset($this->components[$componentId]);
    }

    public function updateLayout($orderedIds)
    {
        if (! isset($orderedIds) || ! is_array($orderedIds)) {
            return;
        }

        $sortedData = [];
        foreach ($orderedIds as $key) {
            if (isset($this->components[$key])) {
                $sortedData[$key] = $this->components[$key];
            }
        }
        if (count($sortedData) === count($this->components)) {
            $this->components = $sortedData;
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
            ->icon(fn() => !$this->editMode ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open')
            ->label(fn() => !$this->editMode ? 'Unlock Layout' : 'Lock Layout')
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
            ->icon('heroicon-m-bookmark-square')
            ->action(fn () => $this->saveLayout());
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
