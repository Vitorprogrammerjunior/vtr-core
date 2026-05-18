// VTR CORE — UI runtime
// Provides: confirm-modal (data-confirm-modal on forms), form-modal (data-modal-target),
// and ephemeral toasts (window.vtrToast or [data-flash]).

(() => {
    function openModal(id) {
        const el = typeof id === 'string' ? document.getElementById(id) : id;
        if (!el) return;
        el.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        const focusable = el.querySelector('input, textarea, select, button[type="submit"]');
        if (focusable) setTimeout(() => focusable.focus(), 60);
    }

    function closeModal(el) {
        if (typeof el === 'string') el = document.getElementById(el);
        if (!el) return;
        el.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // === Confirm modal (singleton) ===
    function ensureConfirmModal() {
        let el = document.getElementById('vtr-confirm-modal');
        if (el) return el;
        el = document.createElement('div');
        el.id = 'vtr-confirm-modal';
        el.className = 'vtr-modal-backdrop';
        el.innerHTML = `
            <div class="vtr-modal" role="dialog" aria-modal="true" aria-labelledby="vtr-confirm-title">
                <span class="vtr-modal-glow"></span>
                <button type="button" class="vtr-modal-close" data-modal-close aria-label="Fechar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/>
                    </svg>
                </button>
                <div class="vtr-modal-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v5M12 17h.01" stroke-linecap="round"/>
                        <path d="M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 id="vtr-confirm-title" class="vtr-modal-title" data-confirm-title>Confirmar ação</h3>
                <p class="vtr-modal-msg" data-confirm-message>Tem certeza?</p>
                <div class="vtr-modal-actions">
                    <button type="button" class="vtr-btn vtr-btn-ghost" data-modal-close>Cancelar</button>
                    <button type="button" class="vtr-btn" data-confirm-ok>Confirmar</button>
                </div>
            </div>`;
        document.body.appendChild(el);
        return el;
    }

    function showConfirm({ title, message, okLabel, onOk }) {
        const el = ensureConfirmModal();
        el.querySelector('[data-confirm-title]').textContent = title || 'Confirmar ação';
        el.querySelector('[data-confirm-message]').textContent = message || 'Tem certeza?';
        const ok = el.querySelector('[data-confirm-ok]');
        ok.textContent = okLabel || 'Confirmar';
        const fresh = ok.cloneNode(true);
        ok.replaceWith(fresh);
        fresh.addEventListener('click', () => {
            closeModal(el);
            if (typeof onOk === 'function') onOk();
        });
        openModal(el);
    }
    window.vtrConfirm = showConfirm;

    // === Toast ===
    function showToast(message, type = 'info', duration = 3200) {
        if (!message) return;
        let wrap = document.querySelector('.vtr-toast-wrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.className = 'vtr-toast-wrap';
            document.body.appendChild(wrap);
        }
        const t = document.createElement('div');
        t.className = 'vtr-toast';
        if (type === 'error') t.style.borderLeftColor = '#ff3b3b';
        t.textContent = message;
        wrap.appendChild(t);
        setTimeout(() => {
            t.classList.add('is-leaving');
            setTimeout(() => t.remove(), 260);
        }, duration);
    }
    window.vtrToast = showToast;

    // === Wire DOM on load ===
    document.addEventListener('DOMContentLoaded', () => {
        // Click on backdrop or [data-modal-close] closes the modal
        document.body.addEventListener('click', (e) => {
            const closeBtn = e.target.closest('[data-modal-close]');
            if (closeBtn) {
                const m = closeBtn.closest('.vtr-modal-backdrop');
                if (m) closeModal(m);
                return;
            }
            if (e.target.classList && e.target.classList.contains('vtr-modal-backdrop')) {
                closeModal(e.target);
            }
        });

        // ESC closes any open modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.vtr-modal-backdrop.is-open').forEach(closeModal);
            }
        });

        // Forms with data-confirm-modal intercept submit
        document.body.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form.matches || !form.matches('[data-confirm-modal]')) return;
            if (form.dataset.vtrConfirmed === '1') return;
            e.preventDefault();
            showConfirm({
                title:   form.dataset.confirmTitle   || 'Confirmar ação',
                message: form.dataset.confirmMessage || 'Tem certeza?',
                okLabel: form.dataset.confirmOk      || 'Confirmar',
                onOk: () => {
                    form.dataset.vtrConfirmed = '1';
                    form.submit();
                },
            });
        }, true);

        // Buttons / links with data-modal-target open the referenced modal
        document.body.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-modal-target]');
            if (!trigger) return;
            e.preventDefault();
            openModal(trigger.dataset.modalTarget);
        });

        // Auto-show toasts from server flash
        document.querySelectorAll('[data-flash]').forEach(el => {
            const msg = el.getAttribute('data-flash');
            const type = el.getAttribute('data-flash-type') || 'info';
            if (msg) showToast(msg, type);
            el.remove();
        });
    });
})();
