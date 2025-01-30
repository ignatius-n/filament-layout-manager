{{-- resources/views/livewire/dynamic-grid.blade.php --}}

<div x-data="{ sortable: null }" class="p-4">
    {{-- Edit Mode Toggle --}}
    <div class="mb-4 flex justify-between w-full gap-y-8 py-8">
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            {{config('reorder-widgets.header')}}
        </h1>
        <div class="flex justify-end">
            {{-- Add/Save Buttons (only in edit mode) --}}
            @if($editMode)
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="selectedComponent">
                        @foreach(Arr::get($settings, 'selectOptions', []) as $key => $value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
                {{ $this->addAction }}

                {{ $this->saveAction }}
                <x-filament-actions::modals />

            @endif

            {{$this->editAction}}
        </div>
    </div>


    <div class="grid md:grid-cols-{{$columns}} gap-4 !important" x-ref="grid">
        @foreach($components as $id => $component)
            <div wire:key="grid-item-{{ $id }}"
                 data-id="{{ $id }}"
                 class="col-span-{{ $component['cols'] }}"
{{--                 class="relative group transition-all h-full"--}}
                 style="grid-column: span {{ $component['cols'] }} / span {{ $component['cols'] }}">

                {{-- Edit Mode Controls --}}
                @if($editMode)
                    <div class="opacity-75 hover:opacity-100 transition-opacity flex gap-1 p-2">
                        <button wire:click="removeComponent('{{ $id }}')">
                            ✕
                        </button>
                        @if($components > 1)
                            <button
                                wire:click="toggleSize('{{ $id }}')"
                                class="p-1 text-4sm">
                                ↔
                            </button>
                            <button
                                wire:click="increaseSize('{{ $id }}')"
                                class="p-1 text-4lg">
                                +
                            </button>
                            <button
                                wire:click="decreaseSize('{{ $id }}')"
                                class="p-1 text-4sm">
                                -
                            </button>
                        @endif
                        <div class="handle cursor-move  bg-black rounded-full p-1 shadow text-4xl">
                            ⤯
                        </div>
                    </div>
                @endif

                @if(is_subclass_of($component, \Filament\Widgets\Widget::class))
                    <x-filament-widgets::widgets
                        :data="[
                        ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                        ...$this->getWidgetData(),
                    ]"
                        :widgets="$this->getVisibleWidgets()"
                        wire:key="widget-{{ $id }}"
                    />
                @else
                    <livewire:dynamic-component :is="$component['type']" :key="$id" />
                @endif

            </div>
        @endforeach
    </div>
</div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let sortableInstance;

        /*
            Credit to this library for showing me how to get an end node to prevent wire:key issues with my nested components when I move them to the end.
            https://github.com/wotzebra/livewire-sortablejs/blob/master/src/index.js#L6
         */
        const moveEndMorphMarker = (el) => {
            const endMorphMarker = Array.from(el.childNodes).filter((childNode) => {
                return childNode.nodeType === 8 && ['[if ENDBLOCK]><![endif]', '__ENDBLOCK__'].includes(childNode.nodeValue?.trim());
            })[0];

            if (endMorphMarker) {
                el.appendChild(endMorphMarker);
            }
        }


        function initializeSortable() {
            const grid = document.querySelector('[x-ref="grid"]');
            if (grid) {
                sortableInstance = new Sortable(grid, {
                    animation: 150,
                    handle: '.handle',
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const orderedIds = Array.from(grid.children).map(el => el.dataset.id);
                        moveEndMorphMarker(grid);
                        Livewire.dispatch('updateLayout', { orderedIds: orderedIds });
                    }
                });
            }
        }

        // Initialize on load
        initializeSortable();

        // Reinitialize whenever Livewire re-renders
        document.addEventListener('livewire:update', function() {
            if (Livewire.getByName('editMode')) {
                initializeSortable();
            } else {
                sortableInstance?.destroy();
            }
        });
    </script>

@endpush


