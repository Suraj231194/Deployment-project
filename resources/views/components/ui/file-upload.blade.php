@props([
    'id',
    'name',
    'label' => null,
    'hint' => null,
    'multiple' => false,
    'accept' => null,
    'errorKey' => null,
    'disabled' => false,
])

@php
    use Illuminate\Support\Str;

    $fieldKey = $errorKey ?: Str::of($name)->before('[')->value();
    $messages = array_merge($errors->get($fieldKey), $errors->get($fieldKey.'.*'));
    $selectedFileListId = $id.'SelectedFiles';
@endphp

<div {{ $attributes->class(['space-y-2']) }}>
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-semibold text-slate-700">{{ $label }}</label>
    @endif

    <div class="{{ $messages ? 'border-rose-300 bg-rose-50/40' : 'border-slate-300 bg-slate-50' }} rounded-2xl border border-dashed p-4">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="file"
            @if ($multiple) multiple @endif
            @if ($accept) accept="{{ $accept }}" @endif
            @disabled($disabled)
            data-file-upload-input
            data-file-upload-target="{{ $selectedFileListId }}"
            class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-white file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60"
        >

        @if ($hint)
            <p class="mt-2 text-xs leading-5 text-slate-500">{{ $hint }}</p>
        @endif

        {{-- Business step: show the currently selected file names so the user can confirm attachments before submitting the form. --}}
        <div id="{{ $selectedFileListId }}" class="mt-3 hidden rounded-xl border border-slate-200 bg-white px-3 py-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Selected Files</p>
            <div class="mt-2 flex flex-wrap gap-2" data-file-upload-list></div>
        </div>
    </div>

    @if ($messages)
        <p class="text-sm text-rose-600">{{ $messages[0] }}</p>
    @endif
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Business step: update the selected-file preview whenever the user chooses attachments.
                document.querySelectorAll('[data-file-upload-input]').forEach(function (input) {
                    if (input.dataset.fileUploadBound === 'true') {
                        return;
                    }

                    input.dataset.fileUploadBound = 'true';

                    input.addEventListener('change', function () {
                        var targetId = input.getAttribute('data-file-upload-target');
                        var container = targetId ? document.getElementById(targetId) : null;
                        var list = container ? container.querySelector('[data-file-upload-list]') : null;

                        if (!container || !list) {
                            return;
                        }

                        list.innerHTML = '';

                        var files = Array.from(input.files || []);

                        if (!files.length) {
                            container.classList.add('hidden');
                            return;
                        }

                        files.forEach(function (file) {
                            var item = document.createElement('span');
                            item.className = 'inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700';
                            item.textContent = file.name;
                            list.appendChild(item);
                        });

                        container.classList.remove('hidden');
                    });
                });
            });
        </script>
    @endpush
@endonce
