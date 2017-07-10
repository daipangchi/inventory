(function (window) {
    if (!window.escapeHtml) {
        const entityMap = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': '&quot;',
            "'": '&#39;',
            "/": '&#x2F;'
        };

        /**
         * Escape string for HTML.
         *
         * @param string
         * @returns {string}
         */
        window.escapeHtml = function (string) {
            return String(string).replace(/[&<>"'\/]/g, function (s) {
                return entityMap[s];
            });
        }
    }

    if (!window.compileHbs) {
        /**
         * Compile Handlebars template.
         *
         * @param templateId
         * @param context
         * @param options
         * @returns {*}
         */
        window.compileHbs = (templateId, context = {}, options = {}) => {
            let html = $(templateId).html();

            return Handlebars.compile(html, options)(context);
        }
    }

    if (!window.createAlert) {
        /**
         * Append bootstrap alert to alerts container.
         *
         * @param type
         * @param message
         */
        window.createAlert = (type, message) => {
            switch (type) {
                case 'error':
                    type = 'danger';
                    break;
            }

            let alert = `
                <div class="alert alert-${type.toLowerCase()}">
                    <h1>${message}</h1>

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>`;

            $('#alerts-container .row').append(alert);
        }
    }

    if (!window.createValidationAlert) {
        window.createValidationAlert = function (messages, $targetContainer) {
            $targetContainer.empty();

            let errors = '';

            for (let key in messages) {
                if (messages.hasOwnProperty(key)) {
                    errors += `<li>${messages[key][0]}</li>`;
                }
            }

            let alert = `
                <div class="form-error-text-block">
                    <ul>${errors}</ul>
                </div>`;

            $targetContainer.prepend(alert);
        }
    }

    if (!window.bootstrapAlert) {
        const $alerts = $('#alerts-container');

        window.bootstrapAlert = function (type, message) {
            $alerts.prepend(`
                <div class="alert alert-${type}">
                    <h1>${message}</h1>

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            `);
        }
    }
})(window);
