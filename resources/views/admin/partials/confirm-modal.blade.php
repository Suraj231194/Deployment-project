{{-- Admin Confirmation Modal --}}
<div id="admin-confirm-modal" class="fixed inset-0 z-[9998] hidden">
    <div id="confirm-backdrop" class="absolute inset-0 bg-slate-950/50 opacity-0 backdrop-blur-[2px] transition-opacity duration-300"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div id="confirm-dialog" class="pointer-events-auto relative w-full max-w-[400px] translate-y-2 scale-95 rounded-2xl bg-white p-6 opacity-0 shadow-2xl transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]">
            <div class="flex items-start gap-4">
                <div id="confirm-icon" class="h-10 w-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 id="confirm-title" class="text-[15px] font-bold text-slate-900">Are you sure?</h3>
                    <p id="confirm-message" class="text-[13px] text-slate-500 font-medium mt-1 leading-relaxed">This action cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button id="confirm-cancel-btn" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">Cancel</button>
                <button id="confirm-action-btn" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 transition shadow-sm cursor-pointer">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
window.AdminConfirm = {
    modal: null, backdrop: null, dialog: null, _resolve: null, isOpen: false,

    init() {
        this.modal = document.getElementById('admin-confirm-modal');
        this.backdrop = document.getElementById('confirm-backdrop');
        this.dialog = document.getElementById('confirm-dialog');
        document.body.appendChild(this.modal);

        document.getElementById('confirm-cancel-btn').addEventListener('click', () => this.close(false));
        document.getElementById('confirm-action-btn').addEventListener('click', () => this.close(true));
        this.backdrop.addEventListener('click', () => this.close(false));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && this.isOpen) this.close(false); });
    },

    show(opts = {}) {
        if (!this.modal) this.init();
        document.getElementById('confirm-title').textContent = opts.title || 'Are you sure?';
        document.getElementById('confirm-message').textContent = opts.message || 'This action cannot be undone.';
        const actionBtn = document.getElementById('confirm-action-btn');
        actionBtn.textContent = opts.confirmText || 'Confirm';
        actionBtn.className = `px-5 py-2.5 rounded-xl text-sm font-bold text-white transition shadow-sm ${opts.danger !== false ? 'bg-rose-500 hover:bg-rose-600' : 'bg-primary-600 hover:bg-primary-700'}`;

        this.modal.classList.remove('hidden');
        this.isOpen = true;
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            this.backdrop.classList.replace('opacity-0', 'opacity-100');
            this.dialog.classList.remove('opacity-0', 'scale-95', 'translate-y-2');
            this.dialog.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        });

        return new Promise((resolve) => { this._resolve = resolve; });
    },

    close(result) {
        this.isOpen = false;
        this.backdrop.classList.replace('opacity-100', 'opacity-0');
        this.dialog.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        this.dialog.classList.add('opacity-0', 'scale-95', 'translate-y-2');
        document.body.classList.remove('overflow-hidden');
        setTimeout(() => { this.modal.classList.add('hidden'); }, 300);
        if (this._resolve) { this._resolve(result); this._resolve = null; }
    }
};
document.addEventListener('DOMContentLoaded', () => AdminConfirm.init());
</script>
