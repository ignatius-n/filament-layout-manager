{{-- resources/views/livewire/dynamic-grid.blade.php --}}

<div x-data="{ sortable: null }" class="p-4">
    {{-- Edit Mode Toggle --}}
    <div class="mb-4">
        <div class="flex gap-1.5 justify-end">
            {{-- Add/Save Buttons (only in edit mode) --}}
            @if($editMode)
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="selectedComponent">
                        @foreach($allowedComponents as $_ => $component)
                            <option value="{{$component['view']}}">{{$component['title']}}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
                <x-filament::button
                    outlined
                    color="success"
                    icon="heroicon-m-plus"
                    wire:click="addComponent"
                    class="px-4 py-2 bg-blue-500 text-black rounded hover:bg-blue-600">
                    Add Table
                </x-filament::button>
                <x-filament::button
                    outlined
                    color="danger"
                    icon="heroicon-m-bookmark-square"
                    wire:click="saveLayout"
                    class="px-4 py-2 bg-green-500 text-black rounded hover:bg-green-600">
                    Save Layout
                </x-filament::button>
            @endif

            <x-filament::button
                outlined
                :icon="!$editMode ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open'"
                wire:click="toggleEditMode"
                class="px-4 py-2 bg-gray-500 text-black rounded hover:bg-gray-600"
            >
                @if($editMode)
                    Lock Layout
                @else
                    Edit Layout
                @endif
            </x-filament::button>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-{{$columns}} gap-4" x-ref="grid">
        @foreach($components as $id => $component)
            <div
                wire:key="{{ $id }}"
                data-id="{{ $id }}"
                class="relative group bg-white shadow-sm transition-all"
                style="grid-column: span {{ $component['cols'] }} / span {{ $component['cols'] }}"
                :class="{
                    'rounded-lg border-2 border-blue-200': @entangle('editMode')
                }">

                {{-- Edit Mode Controls --}}
                @if($editMode)
                    <div class="opacity-75 hover:opacity-100 transition-opacity flex gap-1 p-2">
                        <button
                            wire:click="removeComponent('{{ $id }}')"
                            class="text-red-500 hover:text-red-700 bg-black rounded-full p-1 shadow">
                            ✕
                        </button>
                        <button
                            wire:click="toggleSize('{{ $id }}')"
                            class="text-blue-500 hover:text-blue-700 bg-black  rounded-full p-1 shadow">
                            ↔
                        </button>
                        <button
                            wire:click="increaseSize('{{ $id }}')"
                            class="text-blue-500 hover:text-blue-700 bg-black  rounded-full p-1 shadow">
                            +
                        </button>
                        <button
                            wire:click="decreaseSize('{{ $id }}')"
                            class="text-blue-500 hover:text-blue-700 bg-black  rounded-full p-1 shadow">
                            -
                        </button>
                        <div class="handle cursor-move text-gray-500 bg-black rounded-full p-1 shadow">
                            ⤵
                        </div>
                    </div>
                @endif

                {{-- Component Content --}}
                @livewire($component['type'], ['id' => $component['event_id']], key($id))


            </div>
        @endforeach
    </div>
</div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let sortableInstance;

        function initializeSortable() {
            const grid = document.querySelector('[x-ref="grid"]');
            if (grid) {
                sortableInstance = new Sortable(grid, {
                    animation: 150,
                    handle: '.handle',
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const orderedIds = Array.from(grid.children).map(el => el.dataset.id);
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


