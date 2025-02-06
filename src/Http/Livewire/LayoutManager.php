<?php

namespace Asosick\FilamentLayoutManager\Http\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class LayoutManager extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithHeaderActions;

    protected $listeners = ['updateLayout'];

    #[Url(as: 'l')]
    public int $currentLayout = 0;

    public array $container = [];

    public bool $editMode = false;

    public array $settings = [];

    public $selectedComponent;

    public int $layoutCount;

    public int $columns;

    public string $heading;

    public bool $showLockButton;

    public function mount(?array $settings = []): void
    {
        $this->heading = $settings['heading'] ?? config('filament-layout-manager.heading');
        $this->settings = $settings ?? config('filament-layout-manager.settings');
        $this->layoutCount = $this->settings['layout_count'] ?? config('filament-layout-manager.settings.layout_count');
        $this->columns = $this->settings['grid_columns'] ?? config('filament-layout-manager.settings.grid_columns');
        $this->showLockButton = $this->settings['show_lock_button'] ?? config('filament-layout-manager.settings.show_lock_button');
        $this->selectedComponent = 0;
        $this->load();
        $this->refocusToLayoutInUse();
        $this->enforceRowCounts();
    }

    /**
     * Ensures that if the grid row count changes, the size of components is bounded
     * @return void
     */
    protected function enforceRowCounts(): void
    {
        foreach($this->container as &$layout){
            foreach($layout as $_ => &$component){
                if(! $component){
                    continue;
                }
                $component['cols'] = min($this->columns, $component['cols']);
            }
        }
    }

    /**
     * Toggles between editing mode
     * Will refocus to a layout which has components on change.
     * @return void
     */
    public function toggleEditMode(): void
    {
        $this->editMode = ! $this->editMode;
        $this->refocusToLayoutInUse();
    }

    /**
     * If the current layout is empty, will move active layout index to a layout with components.
     * @return void
     */
    private function refocusToLayoutInUse(): void
    {
        if ($this->container[$this->currentLayout] ?? []) {
            return;
        }
        $i = 0;
        while ($i < count($this->container)) {
            if (count($this->container[$i] ?? []) != 0) {
                $this->currentLayout = $i;

                return;
            }
            $i = $i + 1;
        }
        $this->currentLayout = 0;
    }

    /**
     * Toggles the size of a component to the max or min columns allowed.
     * @param $id
     * @return void
     */
    public function toggleSize($id): void
    {
        if (! $this->editMode) {
            return;
        }
        $cols = $this->container[$this->currentLayout][$id]['cols'];
        $this->container[$this->currentLayout][$id]['cols'] = $cols === $this->columns ? 1 : $this->columns;
    }

    /**
     * Increased the size of a component by one.
     * @param $id
     * @return void
     */
    public function increaseSize($id): void
    {
        if (! $this->editMode) {
            return;
        }
        $cols = $this->container[$this->currentLayout][$id]['cols'];
        $this->container[$this->currentLayout][$id]['cols'] = min($this->columns, $cols + 1);
    }

    /**
     * Decreased the size of a component by one.
     * @param string $id
     * @return void
     */
    public function decreaseSize(string $id): void
    {
        if (! $this->editMode) {
            return;
        }
        $cols = $this->container[$this->currentLayout][$id]['cols'];
        $this->container[$this->currentLayout][$id]['cols'] = max(1, $cols - 1);
    }

    /**
     * Creates a new component on the active layout.
     * @return void
     */
    public function addComponent(): void
    {
        if (! $this->editMode) {
            return;
        }
        $this->container[$this->currentLayout][uniqid()] = [
            'cols' => 1,
            'type' => $this->settings['components'][$this->selectedComponent],
            'store' => [],
        ];
    }

    /**
     * Removes the selected component from the active layout
     * @param string $componentId
     * @return void
     */
    public function removeComponent(string $componentId): void
    {
        if (! $this->editMode) {
            return;
        }
        unset($this->container[$this->currentLayout][$componentId]);
    }

    /**
     * Updates the order of the components on a specific layout. This happens after a user changes the order via dragging.
     * @param array<string> $orderedIds
     * @return void
     */
    public function updateLayout(array $orderedIds): void
    {
        if (! $this->editMode || ! isset($orderedIds)) {
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

    /**
     * Returns the editAction (a.k.a the 'lock'/'unlock' button).
     * @return Action
     */
    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(__('filament-layout-manager::filament-layout-manager.edit'))
            ->outlined()
            ->icon(fn () => ! $this->editMode ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open')
            ->label(fn () => ! $this->editMode ? __('filament-layout-manager::filament-layout-manager.unlock') : __('filament-layout-manager::filament-layout-manager.lock'))
            ->action(fn () => $this->toggleEditMode());
    }

    /**
     * Returns the add action.
     * @return Action
     */
    public function addAction(): Action
    {
        return Action::make('add')
            ->label(__('filament-layout-manager::filament-layout-manager.add'))
            ->outlined()
            ->color('success')
            ->icon('heroicon-m-plus')
            ->action(fn () => $this->addComponent());
    }

    /**
     * Returns the save action.
     * @return Action
     */
    public function saveAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-layout-manager::filament-layout-manager.save'))
            ->outlined()
            ->color('danger')
            ->icon('heroicon-m-bookmark-square')
            ->action(fn () => $this->saveLayout());
    }

    /**
     * Returns an array of user defined header actions.
     * @return array<Action>
     */
    public function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Returns one button of a list of buttons used to toggle between different numbered views.
     * @return Action
     */
    public function selectLayoutAction(): Action
    {
        return Action::make('selectLayout')
            ->label(fn (array $arguments) => $arguments['id'] + 1)
            ->outlined()
            ->keyBindings(function (array $arguments) {
                return ['command+' . ($arguments['id'] + 1), 'ctrl+' . ($arguments['id'] + 1)];
            })
            ->color(fn (array $arguments) => $arguments['id'] === $this->currentLayout ? 'primary' : 'secondary')
            ->action(fn ($arguments) => $this->currentLayout = $arguments['id']);
    }

    /**
     * Wrapper method of the save() function used to check the user is in edit mode, and issue a notification when saved.
     * @return void
     */
    public function saveLayout(): void
    {
        if (! $this->editMode) {
            return;
        }
        $this->save();
        $this->saveNotification();
    }

    /**
     *  Method to save the `container` (a.k.a all the data across all views).
     *  @return void
     */
    protected function save(): void
    {
        session(['layout_manager' => $this->container]);
    }

    /**
     * A method executed when a child component issues the `component-store-update` event.
     * Updates a component's store (a place to store component specific data).
     * @param string $id
     * @param array $store
     * @return void
     */
    #[On('component-store-update')]
    public function componentStoreUpdate(string $id, array $store): void
    {
        $this->container[$this->currentLayout][$id]['store'] = $store;
        if (! $this->editMode) {
            $this->save();
        }
    }

    /**
     * Loads all information from a storage location.
     * @return void
     */
    protected function load(): void
    {
        $this->container = session('layout_manager', []);
    }

    /**
     * Issues a notification to indicate all layouts have been saved.
     * @return void
     */
    protected function saveNotification(): void
    {
        Notification::make()
            ->title(__('filament-layout-manager::filament-layout-manager.saved-notification'))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('filament-layout-manager::layout-manager');
    }
}
