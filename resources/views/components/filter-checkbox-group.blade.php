@props([
    'label',
    'name',
    'items',
    'selected' => [],
])

<div x-data="{ expanded: false }">
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>

    <div class="space-y-2">
        @foreach($items as $index => $item)
            <label class="flex items-center gap-2 cursor-pointer" x-show="{{ $index < 3 ? 'true' : 'false' }} || expanded">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $item['id'] }}"
                    {{ in_array($item['id'], $selected) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                >
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
            </label>
        @endforeach
    </div>

    @if($items->count() > 3)
        <button type="button"
                @click="expanded = !expanded"
                class="mt-2 text-sm text-blue-600 hover:text-blue-800">
            <span x-text="expanded ? 'Voir moins' : 'Voir plus ({{ $items->count() - 3 }})'"></span>
        </button>
    @endif
</div>
