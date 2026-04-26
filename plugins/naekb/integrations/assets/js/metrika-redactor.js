/**
 * Redactor 2.x plugin — добавляет кнопку «Цель метрики» в тулбар RichEditor.
 * При клике просит ввести название цели и устанавливает атрибут data-metrika-goal
 * на выбранный или ближайший блочный элемент.
 */
(function ($) {
    if (typeof $.Redactor === 'undefined') return;

    $.Redactor.prototype.metrikaGoal = function () {
        return {
            langs: {
                ru: {
                    metrikaGoal: 'Цель метрики',
                    metrikaGoalPrompt: 'Введите название цели (латиницей, без пробелов):',
                    metrikaGoalRemove: 'Снять цель метрики',
                }
            },

            init: function () {
                var lang = this.opts.lang || 'ru';
                var labels = (this.metrikaGoal.langs[lang] || this.metrikaGoal.langs['ru']);

                var btnSet = this.button.add('metrikaGoalSet', labels.metrikaGoal);
                this.button.setIcon(btnSet, '<i class="re-icon-link"></i>');
                this.button.addCallback(btnSet, this.metrikaGoal.setGoal);

                var btnRemove = this.button.add('metrikaGoalRemove', labels.metrikaGoalRemove);
                this.button.setIcon(btnRemove, '<i class="re-icon-unlink"></i>');
                this.button.addCallback(btnRemove, this.metrikaGoal.removeGoal);
            },

            setGoal: function () {
                var lang = this.opts.lang || 'ru';
                var labels = (this.metrikaGoal.langs[lang] || this.metrikaGoal.langs['ru']);

                var current = this.selection.current();
                if (!current) return;

                var $el = $(current).closest(
                    '[data-metrika-goal], a, button, .btn, p, h1, h2, h3, h4, h5, h6, li, blockquote, div'
                );
                if (!$el.length) return;

                var existing = $el.attr('data-metrika-goal') || '';
                var goalName = prompt(labels.metrikaGoalPrompt, existing);
                if (goalName === null) return;

                goalName = goalName.trim().replace(/\s+/g, '_');
                if (!goalName) return;

                $el.attr('data-metrika-goal', goalName);
                this.code.sync();
            },

            removeGoal: function () {
                var current = this.selection.current();
                if (!current) return;

                var $el = $(current).closest('[data-metrika-goal]');
                if (!$el.length) return;

                $el.removeAttr('data-metrika-goal');
                this.code.sync();
            }
        };
    };
}(jQuery));
