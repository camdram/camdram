import Routing from 'router';

const q = document.querySelector.bind(document);

document.addEventListener('DOMContentLoaded', event => {
    const container = q('#home-diary-container');
    if (!container) return;

    let diary = q('.home-diary');
    const nav = q('#home-diary-nav');

    var load_new_diary = function(url, ltr) {
        diary.classList.add('diary_loading');

        Camdram.get(url, function(data) {
            diary.classList.remove('diary_loading');

            container.style.height = 'auto';
            const old_height = container.clientHeight;
            diary.style.position = 'absolute';
            const new_diary = document.createElement('div');
            new_diary.style.transform = `translateX(${ltr ? -100 : 100}%)`;
            new_diary.className = 'home-diary';
            new_diary.innerHTML = data;

            container.appendChild(new_diary);

            const new_height = container.clientHeight;
            container.style.transition = 'height 200ms';
            container.style.height = old_height + 'px';
            container.getClientRects()

            window.setTimeout(() => {
                new_diary.style.transform = 'none';
                diary.style.transform = `translateX(${ltr ? 100 : -100}%)`;
                container.style.height = new_height + 'px';
            }, 10);

            window.setTimeout(() => {
                diary.parentNode.removeChild(diary);
                diary = new_diary;
                container.style.transition = 'none';
                container.style.height = 'auto';
            }, 200);
        });
    };

    for (const el of nav.querySelectorAll('li.week-link')) {
        el.addEventListener('click', e => {
            if (e.currentTarget.classList.contains('current')) return;
            var date = e.currentTarget.dataset.weekStart;

            const prev_sel = nav.querySelector('li.current');
            prev_sel.classList.remove('current');
            e.currentTarget.classList.add('current');
            let ltr = true;
            for (let j = prev_sel; j; j = j.nextElementSibling) {
                if (j == e.currentTarget) {
                    ltr = false;
                    break;
                }
            }

            load_new_diary(Routing.generate('acts_camdram_diary_single_week', {date: date, fragment: 'true'}), ltr);
        });
    }

    nav.querySelector('.diary-expand').addEventListener('click', e => {
        nav.classList.add('expanded');
    });
});
