<?php

namespace Asosick\ReorderWidgets\Http\Livewire;

use Filament\Notifications\Notification;
use Livewire\Component;

class ReorderComponent extends Component
{
    public string $selectedComponent = 'list-events';

    public int $columns = 3;

    protected $listeners = ['updateLayout'];

    public array $allowedComponents = [

    ];

    public ?string $activeTab = null;

    public $components = [];

    public $editMode = false;

    public function mount()
    {
        // Load initial state from session/database
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
            'cols' => 1, // 1 = half width, 2 = full width
            'order' => count($this->components),
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
        $this->components = collect($orderedIds)
            ->mapWithKeys(fn ($id, $index) => [
                $id => [
                    ...$this->components[$id],
                    'order' => $index,
                ],
            ])
            ->sortBy(fn ($item) => $item['order'])
            ->values()
            ->toArray();
    }

    public function saveLayout()
    {
        session(['grid_layout' => $this->components]);
        Notification::make()
            ->title('Saved your layout')
            ->success()
            ->send();
        // Save to database here if needed
    }

    public function render()
    {
        return view('reorder-widgets::reorder-component');
    }
}
