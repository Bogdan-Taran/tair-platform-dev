(() => {
    let previousFocus;
    const getModal = (mode) => document.querySelector(`[data-auth-modal="${mode}"]`);
    const activeModal = () => document.querySelector('[data-auth-modal]:not([hidden])');
    const close = () => {
        document.querySelectorAll('[data-auth-modal]').forEach((modal) => { modal.hidden = true; });
        document.body.style.overflow = '';
        previousFocus?.focus();
    };
    const open = (mode, trigger) => {
        const modal = getModal(mode);
        if (!modal) return false;
        previousFocus = trigger || previousFocus || document.activeElement;
        document.querySelectorAll('[data-auth-modal]').forEach((item) => { item.hidden = item !== modal; });
        document.body.style.overflow = 'hidden';
        modal.querySelector('input:not([type="hidden"])')?.focus();
        return true;
    };
    window.openLoginModal = () => open('login');
    window.openRegisterModal = () => open('register');
    window.closeModal = close;
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-auth-open]');
        if (trigger && open(trigger.dataset.authOpen, trigger)) event.preventDefault();
        if (event.target.closest('[data-auth-close]') || event.target.matches('[data-auth-modal]')) close();
    });
    document.addEventListener('keydown', (event) => {
        const modal = activeModal();
        if (!modal) return;
        if (event.key === 'Escape') close();
        if (event.key !== 'Tab') return;
        const focusable = [...modal.querySelectorAll('a[href], button, input:not([type="hidden"])')].filter((element) => !element.disabled);
        const first = focusable[0], last = focusable.at(-1);
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
        if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
    });
    document.querySelectorAll('[data-auth-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const button = form.querySelector('[type="submit"]');
            if (button.disabled) return;
            const feedback = form.querySelector('[data-auth-feedback]');
            form.querySelectorAll('[data-error-for]').forEach((node) => { node.textContent = ''; });
            form.querySelectorAll('[aria-invalid]').forEach((node) => node.removeAttribute('aria-invalid'));
            feedback.hidden = true;
            feedback.classList.remove('is-error');
            button.disabled = true;
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 20000);
            try {
                const response = await fetch(form.action, {
                    method: 'POST', body: new FormData(form), credentials: 'same-origin', signal: controller.signal,
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = response.headers.get('content-type')?.includes('application/json') ? await response.json() : {};
                if (response.ok && data.redirect) { window.location.assign(data.redirect); return; }
                if (response.ok) {
                    feedback.textContent = data.message || 'Готово.';
                } else {
                    feedback.classList.add('is-error');
                    if (response.status === 422 && data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const error = [...form.querySelectorAll('[data-error-for]')].find((node) => node.dataset.errorFor === field);
                            if (error) error.textContent = messages[0];
                            form.elements.namedItem(field)?.setAttribute('aria-invalid', 'true');
                        }
                        form.querySelector('[aria-invalid="true"]')?.focus();
                        feedback.textContent = 'Проверьте заполненные поля.';
                    } else {
                        feedback.textContent = response.status === 419 ? 'Сессия истекла. Обновите страницу и попробуйте снова.'
                            : response.status === 429 ? 'Слишком много попыток. Попробуйте немного позже.'
                            : response.status >= 500 ? 'Сервис временно недоступен. Попробуйте позже.'
                            : 'Не удалось выполнить запрос. Обновите страницу и попробуйте снова.';
                    }
                }
            } catch {
                feedback.classList.add('is-error');
                feedback.textContent = 'Не удалось связаться с сервером. Проверьте соединение и попробуйте снова.';
            } finally {
                clearTimeout(timeout);
                feedback.hidden = false;
                button.disabled = false;
            }
        });
    });
})();
