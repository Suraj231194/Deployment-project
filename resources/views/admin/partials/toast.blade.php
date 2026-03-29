{{-- Admin Toast Notification System --}}
<div id="admin-toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="max-width: 380px;"></div>

<style>
    @keyframes toastSlideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes toastSlideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .admin-toast { animation: toastSlideIn 0.35s cubic-bezier(0.21,1.02,0.73,1) forwards; pointer-events: auto; }
    .admin-toast.removing { animation: toastSlideOut 0.3s ease-in forwards; }
</style>

<script>
window.AdminToast = {
    container: null,
    init() { this.container = document.getElementById('admin-toast-container'); },

    icons: {
        success: `<svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        error: `<svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        info: `<svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        warning: `<svg class="h-5 w-5 text-secondary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
    },

    bgColors: {
        success: 'border-primary-200 bg-primary-50',
        error: 'border-rose-200 bg-rose-50',
        info: 'border-blue-200 bg-primary-50',
        warning: 'border-amber-200 bg-secondary-50'
    },

    show(message, type = 'success', duration = 3500) {
        if (!this.container) this.init();
        const toast = document.createElement('div');
        toast.className = `admin-toast flex items-start gap-3 px-4 py-3.5 rounded-xl border shadow-lg backdrop-blur-sm ${this.bgColors[type] || this.bgColors.info}`;
        toast.innerHTML = `
            <div class="flex-shrink-0 mt-0.5">${this.icons[type] || this.icons.info}</div>
            <p class="text-[13px] font-semibold text-slate-800 leading-relaxed flex-1">${message}</p>
            <button class="flex-shrink-0 mt-0.5 text-slate-400 hover:text-slate-600 transition cursor-pointer" onclick="this.closest('.admin-toast').classList.add('removing');setTimeout(()=>this.closest('.admin-toast').remove(),300)">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;
        this.container.appendChild(toast);
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }
};
document.addEventListener('DOMContentLoaded', () => AdminToast.init());
</script>
