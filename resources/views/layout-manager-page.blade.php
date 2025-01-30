{{--<div>--}}
{{--    {{dd(view('filament-layout-manager::reorder-component'))}}--}}
{{--</div>--}}
{{--<x-filament-panels::page>--}}
{{--    <livewire:reorder-component :settings="$settings"/>--}}
{{--</x-filament-panels::page>--}}
<div>
    <livewire:reorder-component :settings="$settings ?? []"/>
</div>
