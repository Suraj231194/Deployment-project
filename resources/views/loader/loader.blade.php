<div id="globalPageLoader" aria-hidden="true" class="fixed inset-0 z-[9999] opacity-100 visible transition-opacity duration-200">
    <div class="h-full w-full overflow-hidden relative bg-slate-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.18),transparent_35%),radial-gradient(circle_at_bottom,rgba(34,197,94,0.12),transparent_30%)]"></div>

        <div class="relative flex h-full w-full items-center justify-center px-4">
            <div class="relative flex flex-col items-center gap-0">
                <div class="relative">
                    <img
                        src="{{ $loaderLogoSrc }}"
                        alt="Biogenix"
                        class="w-[260px] max-w-[72vw] drop-shadow-sm"
                        decoding="sync"
                    >
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-primary-600/95"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-cyan-400/95"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-amber-300/95"></span>
                    </div>

                    <div class="text-center">
                        <p class="text-white text-xl md:text-2xl font-semibold tracking-[0.24em] uppercase">
                            Loading your store
                        </p>
                        <p class="mt-2 text-slate-300 text-sm md:text-base">
                            Preparing a faster, smarter shopping experience...
                        </p>
                    </div>
                </div>

                <div class="w-72 max-w-[80vw]">
                    <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
                        <div class="loader-bar h-full w-1/2 rounded-full bg-gradient-to-r from-emerald-400 via-cyan-400 to-amber-300"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
