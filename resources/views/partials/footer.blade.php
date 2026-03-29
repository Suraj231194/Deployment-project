<footer id="siteFooter" class="relative mt-10 overflow-hidden border-t border-primary-700/40 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white transition-[padding] duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-secondary-600/45 to-transparent"></div>
    <div class="pointer-events-none absolute -left-24 top-8 h-56 w-56 rounded-full bg-primary-500/20 blur-3xl"></div>
    <div class="pointer-events-none absolute right-0 top-0 h-72 w-72 rounded-full bg-secondary-600/10 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-none px-4 py-8 sm:px-6 lg:px-8 xl:px-10">
        <div class="grid gap-4 lg:grid-cols-12">
            <section class="rounded-[30px] border border-white/10 bg-white/10 p-5 shadow-[0_24px_60px_rgba(5,16,9,0.3)] backdrop-blur sm:p-6 lg:col-span-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/15 p-2 shadow-lg shadow-primary-950/25">
                        <img src="{{ asset('upload/icons/logo.jpg') }}" alt="Biogenix Logo" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-secondary-600/85">Biogenix Healthcare</p>
                        <h3 class="mt-1 text-xl font-semibold tracking-tight text-white">Trusted support for every order flow</h3>
                    </div>
                </div>

                <p class="mt-4 max-w-2xl text-sm leading-6 text-primary-100/75">
                    Precision diagnostics, medical instruments, and responsive post-order support for laboratories, hospitals, and procurement teams.
                </p>

             

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-primary-950/35 p-3.5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-secondary-600/85">Email</p>
                        <a href="mailto:support@biogenix.com" class="mt-1.5 block text-sm font-semibold text-white no-underline transition hover:text-secondary-600">
                            support@biogenix.com
                        </a>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-primary-950/35 p-3.5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-secondary-600/85">Phone</p>
                        <a href="tel:+919876543210" class="mt-1.5 block text-sm font-semibold text-white no-underline transition hover:text-secondary-600">
                            +91 98765 43210
                        </a>
                    </div>
                </div>

                <div class="mt-3 rounded-2xl border border-white/10 bg-primary-950/35 p-3.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-secondary-600/85">Address</p>
                    <p class="mt-1.5 text-sm leading-6 text-primary-100/75">123 Medical Park Drive, Lucknow, UP, IN</p>
                </div>
            </section>

            <section class="rounded-[30px] border border-white/10 bg-white/10 p-5 shadow-[0_24px_60px_rgba(5,16,9,0.3)] backdrop-blur lg:col-span-2">
                <h4 class="text-base font-semibold text-secondary-600/85">Quick Links</h4>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('home') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Products</a></li>
                    <li><a href="{{ route('quotation.create') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Generate Quote</a></li>
                    <li><a href="{{ route('pi-quotation.generate') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">PI</a></li>
                    <li><a href="{{ route('about') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Contact Us</a></li>
                </ul>
            </section>

            <section class="rounded-[30px] border border-white/10 bg-white/10 p-5 shadow-[0_24px_60px_rgba(5,16,9,0.3)] backdrop-blur lg:col-span-2">
                <h4 class="text-base font-semibold text-secondary-600/85">Legal</h4>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('privacy') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Terms of Use</a></li>
                    <li><a href="{{ route('refund-policy') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">Refund & Cancellation</a></li>
                    <li><a href="{{ route('faq') }}" class="text-sm font-medium text-primary-100/70 no-underline transition hover:text-secondary-600">FAQ</a></li>
                </ul>
            </section>

            <section class="rounded-[30px] border border-white/10 bg-white/10 p-5 shadow-[0_24px_60px_rgba(5,16,9,0.3)] backdrop-blur lg:col-span-3">
                <h4 class="text-base font-semibold text-secondary-600/85">Quick Connect</h4>
                <p class="mt-2 text-sm leading-6 text-primary-100/70">
                    Use the fastest channel for order follow-up, account help, and support desk coordination.
                </p>

                <div class="mt-3 flex flex-wrap gap-2.5">
                    <a href="mailto:support@biogenix.com" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-primary-100/80 transition hover:-translate-y-0.5 hover:border-secondary-600/35 hover:bg-secondary-600/10 hover:text-secondary-600" aria-label="Email support">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                    </a>
                    <a href="tel:+919876543210" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-primary-100/80 transition hover:-translate-y-0.5 hover:border-secondary-600/35 hover:bg-secondary-600/10 hover:text-secondary-600" aria-label="Call support">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.78.68 2.61a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.47-1.25a2 2 0 0 1 2.11-.45c.83.33 1.71.56 2.61.68A2 2 0 0 1 22 16.92z"></path></svg>
                    </a>
                    <a href="https://wa.me/919876543210" target="_blank" rel="noopener noreferrer" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-primary-100/80 transition hover:-translate-y-0.5 hover:border-secondary-600/35 hover:bg-secondary-600/10 hover:text-secondary-600" aria-label="WhatsApp support">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                </div>

                <div class="mt-3 space-y-2">
                    <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-secondary-600/85">Support Desk</p>
                        <p class="mt-1 text-sm font-medium leading-6 text-primary-100/75">Email, phone, and WhatsApp shortcuts for faster response handling during business hours.</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-5 flex flex-col gap-3 border-t border-white/10 pt-4 sm:flex-row sm:items-center sm:justify-between">
          

            <p class="text-sm text-secondary-600/85">&copy; 2026 Biogenix Healthcare Solutions - All Rights Reserved</p>
        </div>
    </div>
</footer>
