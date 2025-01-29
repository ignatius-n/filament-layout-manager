<?php

namespace Asosick\ReorderWidgets\Http\Livewire;

use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Livewire;

class ReorderComponent extends Component
{
    public ?string $selectedComponent = null;

    public int $columns = 4;

    protected $listeners = ['updateLayout'];

    public array $allowedComponents = [

    ];

    public ?string $activeTab = null;

    public $components = [];

    public $editMode = false;

    public $settings = [];

    public function mount($settings)
    {
        $this->settings = $settings;
        $this->selectedComponent = $settings['components'][0];

        // Load initial state from session/database
        $this->components = session('grid_layout', []);
        //        foreach($settings['selectOptions'] as $key => $value){
        //            Livewire::component($value, $key);
        //        }
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
