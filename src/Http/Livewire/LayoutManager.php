<?php

namespace Asosick\FilamentLayoutManager\Http\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Livewire\Attributes\Url;
use Livewire\Component;

class LayoutManager extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected $listeners = ['updateLayout'];

    #[Url(as: 'l')]
    public ?int $currentLayout = 0;

    public array $container = [];

    public bool $editMode = false;

    public array $settings = [];

    public $selectedComponent;

    public int $layoutCount;

    public int $columns;

    public bool $showLockButton;

    public function mount(?array $settings = []): void
    {
        $this->settings = $settings ?? config('filament-layout-manager.settings');
        $this->layoutCount = $this->settings['layout_count'] ?? config('filament-layout-manager.settings.layout_count');
        $this->columns = $this->settings['grid_columns'] ?? config('filament-layout-manager.settings.grid_columns');
        $this->showLockButton = $this->settings['show_lock_button'] ?? config('filament-layout-manager.settings.show_lock_button');
        $this->selectedComponent = Arr::get($settings, 'components.0', null);
        $this->load();
        $this->refocusToLayoutInUse();
    }


    public function toggleEditMode(): void
    {
        $this->editMode = ! $this->editMode;
        $this->refocusToLayoutInUse();
    }

    private function refocusToLayoutInUse(): void {
        $i = $this->currentLayout;
        while($i>=0){
            if(count($this->container[$i] ?? []) != 0) {
                $this->currentLayout = $i;
                return;
            }
            $i = $i-1;
        }
        $this->currentLayout = 0;
    }

    public function toggleSize($id): void
    {
        if(! $this->editMode) {
            return;
        }
        $cols = $this->container[$this->currentLayout][$id]['cols'];
        $this->container[$this->currentLayout][$id]['cols'] = $cols === $this->columns ? 1 : $this->columns;
    }

    public function increaseSize($id): void
    {
        if(! $this->editMode) {
            return;
        }
        $cols = $this->container[$this->currentLayout][$id]['cols'];
        $this->container[$this->currentLayout][$id]['cols'] = min($this->columns, $cols + 1);
    }

    public function decreaseSize($id): void
    {
        if(! $this->editMode) {
            return;
        }
        $cols = $this->container[$this->currentLayout][$id]['cols'];
        $this->container[$this->currentLayout][$id]['cols'] = max(1, $cols - 1);
    }

    public function addComponent(): void
    {
        if(! $this->editMode) {
            return;
        }
        $this->container[$this->currentLayout][uniqid()] = [
            'cols' => 1,
            'type' => $this->selectedComponent,
            'event_id' => count($this->container) + 1,
        ];
    }

    public function removeComponent($componentId): void
    {
        if(! $this->editMode) {
            return;
        }
        unset($this->container[$this->currentLayout][$componentId]);
    }

    public function selectLayout($index): void
    {
        $this->currentLayout = $index;
    }


    public function updateLayout($orderedIds): void
    {
        if (! $this->editMode || ! isset($orderedIds) || ! is_array($orderedIds)) {
            return;
        }
        $sortedData = [];
        foreach ($orderedIds as $key) {
            if (isset($this->container[$this->currentLayout][$key])) {
                $sortedData[$key] = $this->container[$this->currentLayout][$key];
            }
        }
        if (count($sortedData) === count($this->container[$this->currentLayout])) {
            $this->container[$this->currentLayout] = $sortedData;
        }
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(__('filament-layout-manager::filament-layout-manager.edit'))
            ->outlined()
            ->icon(fn () => ! $this->editMode ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open')
            ->label(fn () => ! $this->editMode ? __('filament-layout-manager::filament-layout-manager.unlock') : __('filament-layout-manager::filament-layout-manager.lock'))
            ->action(fn () => $this->toggleEditMode());
    }

    public function addAction(): Action
    {
        return Action::make('add')
            ->label(__('filament-layout-manager::filament-layout-manager.add'))
            ->outlined()
            ->color('success')
            ->icon('heroicon-m-plus')
            ->action(fn () => $this->addComponent());
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-layout-manager::filament-layout-manager.save'))
            ->outlined()
            ->color('danger')
            ->icon('heroicon-m-bookmark-square')
            ->action(fn () => $this->saveLayout());
    }

    /*
     * You may ask why there is no ->action() here to trigger a layout change...
     * For the life of me, I can't get the action to trigger using the recommended method by Filament as shown here..
     * https://filamentphp.com/docs/3.x/actions/adding-an-action-to-a-livewire-component
     * I've used an alternative method of passing the $id to the function below and that didn't work either.
     * It works in my other Actions in this class...
     *
     * Unless I've missed something, this a bug? Regardless, the selectLayoutAction is wrapped in a div with a wire click
     * method to call the selectLayout method and change $this->currentLayout.
     * Not ideal. Apologies if you're trying to override the action here and it's unclear.
     */
    public function selectLayoutAction($id): Action
    {
        return Action::make('select_layout')
            ->label(fn (array $arguments) => $id + 1)
            ->outlined()
            ->keyBindings(function (array $arguments) use ($id) {
                return ['command+' . ($id + 1), 'ctrl+' . ($id + 1)];
            })
            ->color(fn (array $arguments) => $id === $this->currentLayout ? 'primary' : 'secondary');
    }


    public function saveLayout(): void
    {
        if(! $this->editMode) {
            return;
        }
        $this->save();
        $this->saveNotification();
    }

    protected function save(): void
    {
        session(['layout_manager' => $this->container]);
    }

    protected function load(): void
    {
        $this->container = session('layout_manager', []);
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
        return view('filament-layout-manager::layout-manager');
    }
}
