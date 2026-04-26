import { Collapse } from 'bootstrap';
import { Modal } from 'bootstrap';

// Yandex Metrika auto-tracking
document.addEventListener('click', function (e) {
    if (typeof ym === 'undefined' || !window._metrikaId) return;

    const target = e.target.closest('[data-metrika-goal], a, button, [role="button"], .btn');
    if (!target) return;

    const goal = target.dataset?.metrikaGoal;
    if (goal) {
        ym(window._metrikaId, 'reachGoal', goal);
        return;
    }

    if (target.tagName === 'A') {
        const text = target.textContent.trim().slice(0, 50);
        ym(window._metrikaId, 'reachGoal', 'click_link', { target: target.href, text });
    } else {
        const text = target.textContent.trim().slice(0, 50);
        ym(window._metrikaId, 'reachGoal', 'click_button', { text });
    }
}, true);
