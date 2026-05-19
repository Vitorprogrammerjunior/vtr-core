(()=>{function e(e){let t=typeof e==`string`?document.getElementById(e):e;if(!t)return;t.classList.add(`is-open`),document.body.style.overflow=`hidden`;let n=t.querySelector(`input, textarea, select, button[type="submit"]`);n&&setTimeout(()=>n.focus(),60)}function t(e){typeof e==`string`&&(e=document.getElementById(e)),e&&(e.classList.remove(`is-open`),document.body.style.overflow=``)}function n(){let e=document.getElementById(`vtr-confirm-modal`);return e||(e=document.createElement(`div`),e.id=`vtr-confirm-modal`,e.className=`vtr-modal-backdrop`,e.innerHTML=`
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
            </div>`,document.body.appendChild(e),e)}function r({title:r,message:i,okLabel:a,onOk:o}){let s=n();s.querySelector(`[data-confirm-title]`).textContent=r||`Confirmar ação`,s.querySelector(`[data-confirm-message]`).textContent=i||`Tem certeza?`;let c=s.querySelector(`[data-confirm-ok]`);c.textContent=a||`Confirmar`;let l=c.cloneNode(!0);c.replaceWith(l),l.addEventListener(`click`,()=>{t(s),typeof o==`function`&&o()}),e(s)}window.vtrConfirm=r;function i(e,t=`info`,n=3200){if(!e)return;let r=document.querySelector(`.vtr-toast-wrap`);r||(r=document.createElement(`div`),r.className=`vtr-toast-wrap`,document.body.appendChild(r));let i=document.createElement(`div`);i.className=`vtr-toast`,t===`error`&&(i.style.borderLeftColor=`#ff3b3b`),i.textContent=e,r.appendChild(i),setTimeout(()=>{i.classList.add(`is-leaving`),setTimeout(()=>i.remove(),260)},n)}window.vtrToast=i,document.addEventListener(`DOMContentLoaded`,()=>{document.body.addEventListener(`click`,e=>{let n=e.target.closest(`[data-modal-close]`);if(n){let e=n.closest(`.vtr-modal-backdrop`);e&&t(e);return}e.target.classList&&e.target.classList.contains(`vtr-modal-backdrop`)&&t(e.target)}),document.addEventListener(`keydown`,e=>{e.key===`Escape`&&document.querySelectorAll(`.vtr-modal-backdrop.is-open`).forEach(t)}),document.body.addEventListener(`submit`,e=>{let t=e.target;!t.matches||!t.matches(`[data-confirm-modal]`)||t.dataset.vtrConfirmed!==`1`&&(e.preventDefault(),r({title:t.dataset.confirmTitle||`Confirmar ação`,message:t.dataset.confirmMessage||`Tem certeza?`,okLabel:t.dataset.confirmOk||`Confirmar`,onOk:()=>{t.dataset.vtrConfirmed=`1`,t.submit()}}))},!0),document.body.addEventListener(`click`,t=>{let n=t.target.closest(`[data-modal-target]`);n&&(t.preventDefault(),e(n.dataset.modalTarget))}),document.querySelectorAll(`[data-flash]`).forEach(e=>{let t=e.getAttribute(`data-flash`),n=e.getAttribute(`data-flash-type`)||`info`;t&&i(t,n),e.remove()})})})();