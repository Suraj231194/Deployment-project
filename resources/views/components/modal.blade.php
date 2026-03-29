@props([
    'id' => 'ui-modal',
    'open' => false,
    'title' => null,
    'maxWidth' => 'max-w-2xl',
])

<div
    id="{{ $id }}"
    class="{{ $open ? 'flex' : 'hidden' }} fixed inset-0 z-[100] items-center justify-center p-4 opacity-0 transition-opacity duration-300 sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
>
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm modal-close" data-modal-close="{{ $id }}"></div>

    <div
        class="relative flex flex-col w-full {{ $maxWidth }} grow-0 overflow-hidden rounded-[2rem] bg-white shadow-2xl transition duration-300 scale-85 max-h-[calc(100vh-2rem)]"
        id="{{ $id }}-content"
    >
        <!-- Header -->
        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 bg-white px-5 py-3 sm:px-6 sm:py-4">
            @if($title)
                <h2 id="{{ $id }}-title" class="text-xl font-bold tracking-tight text-slate-900">{{ $title }}</h2>
            @endif
            <button
                type="button"
                class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 modal-close"
                data-modal-close="{{ $id }}"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-5 sm:p-6 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @if(isset($footer))
            <div class="flex shrink-0 items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-3 sm:px-6">
                {{ $footer }}
            </div>
        @endif
    </div>

    <script>
        (function() {
            const modalId = @json($id);
            const isOpen = @json($open);
            const modal = document.getElementById(modalId);
            const content = document.getElementById(modalId + '-content');
            
            if (isOpen && modal && content) {
                window.requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    content.classList.remove('scale-85');
                    content.classList.add('scale-90');
                });
            }

            // Global observer for toggleModal if needed, but we'll use a safer approach in the view
        })();
    </script>
</div>
