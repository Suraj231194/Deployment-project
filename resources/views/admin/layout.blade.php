@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-4 lg:py-8">
    <div class="mx-auto flex w-full max-w-[96rem] gap-0 lg:gap-8 px-4 sm:px-6 lg:px-8 xl:px-12 2xl:px-16">
        
        {{-- Mobile sidebar toggle --}}
        <button id="mobile-sidebar-toggle" class="fixed bottom-6 left-6 z-[999] lg:hidden h-12 w-12 rounded-full bg-primary-600 text-white shadow-lg flex items-center justify-center hover:bg-primary-700 transition cursor-pointer">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        {{-- Mobile sidebar overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-[997] bg-slate-900/50 backdrop-blur-sm hidden lg:hidden transition-opacity opacity-0"></div>

        <!-- Sidebar Navigation -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main id="admin-main-content" class="flex-1 min-w-0 space-y-6 pb-12 transition-opacity duration-200">
            @yield('admin_content')
        </main>
    </div>
</div>

{{-- Global partials --}}
@include('admin.partials.toast')
@include('admin.partials.confirm-modal')

{{-- Loading skeleton overlay --}}
<div id="admin-loading-skeleton" class="hidden absolute inset-0 z-10">
    <div class="space-y-6 animate-pulse p-1">
        <div class="h-8 bg-slate-200/60 rounded-xl w-2/5"></div>
        <div class="h-5 bg-slate-200/40 rounded-lg w-3/5"></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
            <div class="h-[130px] bg-slate-200/50 rounded-2xl"></div>
            <div class="h-[130px] bg-slate-200/50 rounded-2xl"></div>
            <div class="h-[130px] bg-slate-200/50 rounded-2xl hidden sm:block"></div>
            <div class="h-[130px] bg-slate-200/50 rounded-2xl hidden sm:block"></div>
        </div>
        <div class="h-[300px] bg-slate-200/40 rounded-2xl mt-4"></div>
    </div>
</div>

{{-- Scroll-to-top button --}}
<button id="admin-scroll-top" class="fixed bottom-6 right-6 z-[990] h-10 w-10 rounded-full bg-primary-600 text-white shadow-lg flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 hover:bg-slate-700 translate-y-4 cursor-pointer">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<style>
    /* Button loading state utility */
    .btn-loading { position: relative; color: transparent !important; pointer-events: none; }
    .btn-loading::after {
        content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 18px; height: 18px; border: 2.5px solid rgba(255,255,255,0.3); border-top-color: white;
        border-radius: 50%; animation: btnSpin 0.6s linear infinite;
    }
    .btn-loading-dark::after { border-color: rgba(15,23,42,0.2); border-top-color: #0f172a; }
    @keyframes btnSpin { to { transform: translate(-50%, -50%) rotate(360deg); } }

    /* Sidebar mobile transitions */
    @media (max-width: 1023px) {
        aside#admin-sidebar {
            position: fixed !important; top: 0; left: 0; bottom: 0; z-index: 998;
            transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            width: 260px; overflow-y: auto; height: 100vh;
            background: white; border-radius: 0; margin: 0; padding: 0;
        }
        aside#admin-sidebar > nav {
            border-radius: 0; border: none; box-shadow: none;
            height: 100%; overflow-y: auto;
        }
        aside#admin-sidebar.sidebar-open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.12); }
    }

    /* Skeleton shimmer */
    @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    .skeleton-shimmer {
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mainContent = document.getElementById('admin-main-content');
        const loadingSkeleton = document.getElementById('admin-loading-skeleton');
        const scrollTopBtn = document.getElementById('admin-scroll-top');
        
        // ─── Scroll-to-top button visibility ───
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                scrollTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollTopBtn.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            } else {
                scrollTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollTopBtn.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            }
        });
        scrollTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // ─── Mobile sidebar toggle ───
        const sidebar = document.getElementById('admin-sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mobileToggle = document.getElementById('mobile-sidebar-toggle');

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            sidebarOverlay.classList.remove('hidden');
            requestAnimationFrame(() => sidebarOverlay.classList.replace('opacity-0', 'opacity-100'));
        }
        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            sidebarOverlay.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
        }

        mobileToggle.addEventListener('click', () => {
            sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
        });
        sidebarOverlay.addEventListener('click', closeSidebar);

        // ─── Reusable AJAX page loader ───
        const loadPage = async (url) => {
            // Show skeleton
            mainContent.style.position = 'relative';
            mainContent.classList.add('opacity-40');
            loadingSkeleton.classList.remove('hidden');
            mainContent.prepend(loadingSkeleton);
            
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newContent = doc.getElementById('admin-main-content');
                if (newContent) {
                    mainContent.innerHTML = newContent.innerHTML;
                    
                    // Force execution of inline scripts
                    const scripts = mainContent.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });

                    window.history.pushState({}, '', url);
                    document.title = doc.title;
                    
                    // Scroll to top after content swap
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } catch (error) {
                console.error('Failed to load page:', error);
                window.location.href = url;
            } finally {
                loadingSkeleton.classList.add('hidden');
                mainContent.classList.remove('opacity-40');
                mainContent.style.position = '';
            }
        };

        // ─── Click interceptor for sidebar links AND .ajax-link elements ───
        document.body.addEventListener('click', async (e) => {
            const link = e.target.closest('#admin-sidebar a, .ajax-link');
            if (!link || !link.href || link.getAttribute('href') === '#') return;
            
            const url = new URL(link.href);
            if (url.origin !== window.location.origin) return;

            e.preventDefault();
            
            const isSidebarLink = !!e.target.closest('#admin-sidebar a');

            if (isSidebarLink) {
                // Close mobile sidebar
                closeSidebar();

                // Update sidebar active state
                document.querySelectorAll('#admin-sidebar a').forEach(aEl => {
                    aEl.classList.remove('bg-primary-600', 'text-white', 'font-bold');
                    aEl.classList.add('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-800');
                    const indicator = aEl.querySelector('div.bg-white.rounded-r-md');
                    if (indicator) indicator.remove();
                    const svg = aEl.querySelector('svg');
                    if (svg) { svg.classList.remove('text-indigo-200'); svg.classList.add('text-slate-400'); }
                });

                link.classList.remove('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-800');
                link.classList.add('bg-primary-600', 'text-white', 'font-bold');
                
                const indicatorDiv = document.createElement('div');
                indicatorDiv.className = 'absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-md';
                link.prepend(indicatorDiv);
                
                const linkSvg = link.querySelector('svg');
                if (linkSvg) { linkSvg.classList.remove('text-slate-400'); linkSvg.classList.add('text-indigo-200'); }
            }

            await loadPage(link.href);
        });

        // ─── Handle back/forward with AJAX ───
        window.addEventListener('popstate', async () => {
            await loadPage(window.location.href);
        });

        // ─── Button loading state helpers ───
        window.AdminBtnLoading = {
            start(btn) { btn.classList.add('btn-loading'); btn.disabled = true; },
            stop(btn) { btn.classList.remove('btn-loading'); btn.disabled = false; }
        };

        // ─── Click-outside to close dropdown menus ───
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.relative.inline-block')) {
                document.querySelectorAll('.relative.inline-block > div.absolute').forEach(d => d.classList.add('hidden'));
            }
        });
    });
</script>
@stack('scripts')
@endsection
