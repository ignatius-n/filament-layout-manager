<div>
    @if($this->shouldWrapInFilamentPage())
        <x-filament-panels::page>
            <livewire:reorder-component :settings="$settings ?? []"/>
        </x-filament-panels::page>
    @else
        <livewire:reorder-component :settings="$settings ?? []"/>
    @endif
</div>
