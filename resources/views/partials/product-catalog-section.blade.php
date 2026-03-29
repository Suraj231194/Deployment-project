<section class="py-10 md:py-12">
    <div class="mx-auto w-full max-w-none px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="mx-auto max-w-3xl rounded-[28px] border border-slate-200 bg-white px-6 py-10 text-center shadow-sm md:px-10">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-700">Legacy Partial</p>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 md:text-4xl">Catalog preview moved to the shared storefront system</h2>
            <p class="mx-auto mt-3 max-w-2xl text-base leading-8 text-slate-600">
                This older standalone catalog partial is no longer routed. Use the shared catalog and product detail
                pages so typography, color, spacing, and action patterns stay aligned across the project.
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">Open Catalog</a>
                <a href="{{ route('contact') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Contact Sales</a>
            </div>
        </div>
    </div>
</section>
