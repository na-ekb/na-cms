import Popover from 'bootstrap/js/dist/popover';
import Modal from 'bootstrap/js/dist/modal';

Object.defineProperty(String.prototype, 'capitalize', {
    value: function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    },
    enumerable: false
});

document.addEventListener('DOMContentLoaded', function(){
    popoverRegister();
    addMap();

    findAndCall('[data-js="clock"]', false, (clockEls) => {
        updateClocks(clockEls);
        window.setInterval(function(){
            updateClocks(clockEls);
        },10000);
    });

    findAndCall('[data-js="filterDay"]', true, (chooseDayEl) => {
        chooseDayEl.onclick = (ev) => {
            let city = document.querySelector('[data-js="selectCity"]').value;
            changeDayOfWeek(parseInt(ev.target.dataset.day), city);
        };
    });

    findAndCall('[data-js="changeDay"]', true, (changeDayEl) => {
        changeDayEl.onclick = (ev) => {
            let city = document.querySelector('[data-js="selectCity"]').value;
            if (typeof ev.target.dataset.dir == 'undefined') {
                changeDayMobile(ev.target.parentElement.dataset.dir, city);
            } else {
                changeDayMobile(ev.target.dataset.dir, city);
            }
        };
    });

    buttonsListRegister();

    Object.defineProperty(window, 'chosenDate', {
        get: function(){
            return this._chosenDate;
        },
        set: function(val){
            this._chosenDate = val;
            window.history.replaceState(null, '', getFullUrl());
        }
    });

    const date = getQueryVariable('date');
    if (date === undefined) {
        window._chosenDate = new Date();
    } else {
        let [day, month, year] = date.split('.').map(Number);
        window._chosenDate = new Date(year, month - 1, day);
    }
});

function updateClocks(els) {
    const formatter = new Intl.DateTimeFormat('ru-RU', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: window.timezone || 'UTC'
    });
    els.forEach((timeEl) => {
        timeEl.innerHTML = formatter.format(new Date());
    });
}

function changeDayOfWeek(day, city = null, location = null) {
    if (typeof window.currentBtnAnim !== 'undefined' || typeof window.chosenBtnAnim !== 'undefined') {
        return;
    }

    let currentEl = document.querySelector(`.meeting-days-menu>li.active`);
    let currentButton = document.querySelector(`.meeting-days-menu>li.active>button`);
    let chosenEl = document.querySelector(`.meeting-days-menu [data-js="filterDay"][data-day="${day}"]`).closest('li');

    currentEl.classList.remove('active');
    chosenEl.classList.add('active');

    if (currentButton.dataset.day !== day) {
        let now = new Date();
        let formatter = new Intl.DateTimeFormat('ru-RU', {
            day:        'numeric',
            month:      'long',
            year:       'numeric',
            timeZone:   window.timezone
        });
        let dayNow = now.getDay();
        let normalizedDayNow = dayNow === 0 ? 7 : dayNow;
        let normalizedDay = day === 0 ? 7 : day;
        let distance = normalizedDay - normalizedDayNow;
        now.setDate(now.getDate() + distance);
        let formattedDate = formatter.format(now).replace(/\s*г\./, "");

        findAndCall('[data-js="date"]', false, (dateEls) => {
            dateEls.forEach((dateEl) => {
                dateEl.innerHTML = formattedDate;
            });
        });

        window.chosenDate = now;
    }

    updateMeetings();
}

function changeDayMobile(dir) {
    if (typeof window.currentBtnAnim !== 'undefined' || typeof window.chosenBtnAnim !== 'undefined') {
        return;
    }

    let date = window.chosenDate;

    if (dir === 'next') {
        date.setDate(date.getDate() + 1);
    } else {
        date.setDate(date.getDate() - 1);
    }

    window.chosenDate = date;

    updateMeetings();

    let formatter = new Intl.DateTimeFormat('ru-RU', {
        day:        'numeric',
        month:      'long',
        year:       'numeric',
        timeZone:   window.timezone
    });

    let formattedDate = formatter.format(date).replace(/\s*г\./, "");
    findAndCall('[data-js="date"]', false, (dateEls) => {
        dateEls.forEach((dateEl) => {
            dateEl.innerHTML = formattedDate;
        });
    });

    let currentEl = document.querySelector(`.meeting-days-menu>li.active`);
    let chosenEl = document.querySelector(`.meeting-days-menu [data-js="filterDay"][data-day="${date.getDay()}"]`).closest('li');
    currentEl.classList.remove('active');
    chosenEl.classList.add('active');
}

function updateMeetings() {
    const form = document.getElementById('filterForm');
    oc.request('#filterForm', form.dataset.request, {
        update: {
            "meetings::meeting": true,
            "meetings::meeting-week": true,
            "meetings::meeting-map": true
        },
        afterUpdate: (response) => {
            popoverRegister();
            buttonsListRegister();
            ymaps.ready(function() {
                if (typeof window.meetingsMap == 'undefined') {
                    createMap();
                } else {
                    window.meetingsMap.geoObjects.removeAll();
                }
                addGroupsToMap();
            });
        }
    });
}

function popoverRegister() {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(el => new Popover(el));

    [...popoverTriggerList].map(el => {
        el.onclick = (ev) => {
            hideAllPopovers();
        };
    });

    const hideAllPopovers = () => {
        if (document.querySelectorAll('.popover-body').length > 0) {
            popoverList.forEach(pop => pop.hide());
        }
    };

    window.addEventListener('scroll', hideAllPopovers, true);
}

function buttonsListRegister() {
    findAndCall('[data-js="selectCity"]', true, (selectCityEl) => {
        if (selectCityEl.nodeName === 'BUTTON') {
            selectCityEl.onclick = (ev) => {
                let select = document.querySelector('[data-js="selectCity"]');
                select.value = ev.target.dataset.city;
                select.dispatchEvent(new Event('change'));
            };
        } else {
            selectCityEl.onchange = (ev) => {
                let day = document.querySelector('.meeting-days-menu>li.active>button').dataset.day;
                let location = document.querySelector('[data-js="selectLocation"]').value;
                window.history.replaceState(null, '', getFullUrl());
                changeDayOfWeek(day, ev.target.value, location);
            };
        }
    });

    findAndCall('[data-js="selectLocation"]', true, (selectLocationEl) => {
        if (selectLocationEl.nodeName === 'BUTTON') {
            selectLocationEl.onclick = (ev) => {
                let select = document.querySelector('[data-js="selectLocation"]');
                select.value = ev.target.dataset.location;
                select.dispatchEvent(new Event('change'));
            };
        } else {
            selectLocationEl.onchange = (ev) => {
                let day = document.querySelector('.meeting-days-menu>li.active>button').dataset.day;
                let city = document.querySelector('[data-js="selectCity"]').value;
                window.history.replaceState(null, '', getFullUrl());
                changeDayOfWeek(day, city, ev.target.value);
            };
        }
    });

    findAndCall('[data-js="switchView"]', true, (switchViewButton) => {
        switchViewButton.onclick = (ev) => {
            document.querySelectorAll('[data-js="switchView"]').forEach((em) => {
                em.classList.remove('active');
            })
            ev.target.classList.add('active');
            if (ev.target.value === 'list') {
                switchToList();
            } else if (ev.target.value === 'week') {
                switchToWeek();
            } else {
                switchToMap();
            }
        };
    });
}

function switchToList() {
    document.querySelector('.week-meetings').style.display = 'none';
    document.querySelector('#map').style.display = 'none';
    document.querySelector('.all-meetings').style.display = 'block';
}

function switchToWeek() {
    document.querySelector('.all-meetings').style.display = 'none';
    document.querySelector('#map').style.display = 'none';
    document.querySelector('.week-meetings').style.display = 'block';
}

function switchToMap() {
    ymaps.ready(function() {
        if (typeof window.meetingsMap == 'undefined') {
            createMap();
            addGroupsToMap();
        }
        document.querySelector('.all-meetings').style.display = 'none';
        document.querySelector('.week-meetings').style.display = 'none';
        document.querySelector('#map').style.display = 'block';
    });
}

function addMap() {
    let s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = `https://api-maps.yandex.ru/2.1/?apikey=5d5281d2-41f9-469d-9d1e-568c70c5e947&lang=ru_RU`;
    let fs = document.getElementsByTagName('script')[0];
    fs.parentNode.insertBefore(s, fs);
}

function createMap() {
    window.meetingsMap = new ymaps.Map('map', {
        controls: [
            'geolocationControl',
            'routeButtonControl',
            'fullscreenControl',
            'zoomControl'
        ],
        center: [0, 0],
        zoom: 10
    });
}

function addGroupsToMap() {
    let groupsCollection = [];

    let days = [
        'ВС', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'
    ]

    for (let i = 0; i < window.groups.length; i++) {
        let group = window.groups[i];
        if (group.type === 'online') {
            continue;
        }

        group.address = `${group.city}, ${unCapitalizeFirst(group.address)}`;
        if (group.address_desc !== '') {
            group.address += `, ${unCapitalizeFirst(group.address_desc)}`;
        }

        let meetingDays = [];
        for (let n = 0; n < group.meetings.length; n++) {
            let meeting = group.meetings[n];

            if (meeting.type !== 0 || (meeting.format !== 1 && meeting.format !== 2) || meeting.online === 2) {
                continue;
            }

            let time = meeting.time.split(':');
            time = `${time[0]}:${time[1]}`;

            let meetingDay = {
                format: meeting.format,
                time: time,
                duration: meeting.duration
            };

            if (typeof meetingDays[meeting.day] !== 'undefined') {
                meetingDays[meeting.day] = [
                    ...meetingDays[meeting.day],
                    meetingDay
                ];
            } else {
                meetingDays[meeting.day] = meetingDay;
            }
        }

        let balloonContent = '<div class="balloon-blocks">';
        for (let n = 1; n < 8; n++) {
            let meetingDay;
            if (n === 7) {
                meetingDay = meetingDays['0'];
            } else {
                meetingDay = meetingDays[n.toString()];
            }

            balloonContent += `<span class="balloon-block ${typeof meetingDay === 'undefined' ? 'none' : ''}">`;
            balloonContent += `<span class="balloon-title"> ${ n === 7 ? days[0] : days[n] } </span>`;
            if (typeof meetingDay !== 'undefined') {
                if (Array.isArray(meetingDay)) {
                    for (let x = 0; x < meetingDay.length; x++) {
                        balloonContent += `<span class="balloon-time"> ${ meetingDay[x].time } </span>`;
                    }
                } else {
                    balloonContent += `<span class="balloon-time"> ${ meetingDay.time } </span>`;
                }
            }
            balloonContent += `</span>`;
        }
        balloonContent += '</div>';

        groupsCollection.push(new ymaps.GeoObject({
            geometry: {
                type: 'Point',
                coordinates: [parseFloat(group.lat), parseFloat(group.lon)]
            },
            properties: {
                clusterCaption: `<span class="text-blue">${group.title}</span>`,
                balloonContentHeader: `<span class="text-blue">${group.title}</span>`,
                balloonContentFooter: group.address,
                balloonContentBody: balloonContent
            }
        }, {
            fillColor: 'F1F2F6',
            balloonMinWidth: 340,
            iconLayout: 'default#image',
            iconImageHref: 'themes/na/assets/img/minilogo-map.svg',
            iconImageSize: [30, 30],
            iconImageOffset: [-15, -15]
        }));
    }

    let meetingsClusterer = new ymaps.Clusterer({
        disableClickZoom: true,
        hideIconOnBalloonOpen: false,
        geoObjectHideIconOnBalloonOpen: false,
        hasHint: false,
        clusterIconContentLayout: ymaps.templateLayoutFactory.createClass('<div class="clusterer-icon">{{ properties.geoObjects.length }}</div>'),
        clusterIcons: [
            {
                href: 'themes/na/assets/img/cluster.svg',
                size: [30, 30],
                offset: [-15, -15]
            },
            {
                href: 'themes/na/assets/img/cluster.svg',
                size: [30, 30],
                offset: [-15, -15]
            }]
    });
    meetingsClusterer.add(groupsCollection);
    if (groupsCollection.length <= 0) {
        return;
    }
    window.meetingsMap.geoObjects.add(meetingsClusterer);
    setTimeout(() => {
        window.meetingsMap.setBounds(window.meetingsMap.geoObjects.getBounds(), {
            checkZoomRange: true,
            zoomMargin: 2
        });
    }, 500);
}

function findAndCall(selector, map, callback) {
    let elements = [].slice.call(document.querySelectorAll(selector));
    if (elements.length > 0) {
        if (map) {
            elements.map((element) => {
                callback(element);
            });
        } else {
            callback(elements);
        }
    }
}

function getQueryVariable(variable) {
    let query = window.location.search.substring(1);
    let vars = query.split('&');
    for (let i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) == variable) {
            return decodeURIComponent(pair[1]);
        }
    }
}

function getFullUrl() {
    let date = window.chosenDate;
    let citySelect = document.querySelector('[data-js="selectCity"]');
    let locationSelect = document.querySelector('[data-js="selectLocation"]');

    let backendFormatter = new Intl.DateTimeFormat('ru-RU', {
        timeZone: window.timezone
    });

    let params = {
        date: backendFormatter.format(date)
    };

    if (citySelect.value !== '') {
        params.city = citySelect.value;
    } else {
        delete params.city;
    }

    if (locationSelect.value !== '') {
        params.location = locationSelect.value;
    } else {
        delete params.location;
    }

    let searchParams = new URLSearchParams(params);
    return (window.location.origin + window.location.pathname + '?' + searchParams);
}

function unCapitalizeFirst(val) {
    return String(val).charAt(0).toLowerCase() + String(val).slice(1);
}
