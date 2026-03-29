@props([
    'colspan' => 1,
    'title' => 'No records found.',
    'description' => null,
])

<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-12 text-center">
        <div class="mx-auto max-w-md space-y-2">
            <p class="text-base font-semibold text-slate-900">{{ $title }}</p>
            @if ($description)
                <p class="text-sm leading-6 text-slate-500">{{ $description }}</p>
            @endif
        </div>
    </td>
</tr>
