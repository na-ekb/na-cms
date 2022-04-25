import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

if (document.getElementById('calendar') !== null) {
    let calendar = new Calendar(document.getElementById('calendar'), {
        plugins: [ dayGridPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            right: 'title'
        }
    });
    calendar.setOption('locale', 'ru');
    calendar.render();
}

if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark')
} else {
    document.documentElement.classList.remove('dark')
}