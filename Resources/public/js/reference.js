(function (global, doc) {
    function init(target) {
        var modal = doc.querySelector('#kaliop-remote-id-modal--edit-' + target);
        var form = modal.querySelector('form');
        var submit = modal.querySelector('#' + target + '_remote_id_save');
        var errors = modal.querySelector('.kaliop-remote-id-modal__errors');
        var field = form.querySelector('input[name$="[remoteId]"]');
        var currentValue = field.value;
        var saving = false;

        function onFieldChange() {
            field.value = window.jQuery.trim(field.value);
            submit.disabled = saving || currentValue === field.value;
        }

        function startSubmit() {
            saving = true;
            errors.style.display = 'none';
            field.readonly = true;
            onFieldChange();
        }

        function doneSubmit() {
            saving = false;
            field.readonly = false;
            onFieldChange();
        }


        window.jQuery(modal).on('hidden.bs.modal', function () {
            field.value = currentValue;
            errors.style.display = 'none';
            errors.innerHTML = '';
            onFieldChange();
        });

        onFieldChange();
        field.addEventListener('input', onFieldChange);
        form.addEventListener('submit', function (event) {
            var formData, remoteId, request;

            event.preventDefault();
            startSubmit();

            formData = new FormData();
            remoteId = field.value;
            formData.append('remoteId', remoteId);
            request = new Request(form.dataset.validateUrl, {
                method: 'POST',
                body: formData,
                mode: 'same-origin',
                credentials: 'same-origin',
            });

            fetch(request)
                .then(function (response) {
                    return response.json();
                })
                .then(function (json) {
                    if (json.valid) {
                        field.readonly = true;
                        form.submit();
                    } else {
                        errors.innerHTML = json.errors.join('<br />');
                        errors.style.display = '';
                        doneSubmit();
                    }
                })
                .catch(function (error) {
                    errors.innerHTML = error;
                    errors.style.display = '';
                    doneSubmit();
                })
        }, true);
    }

    window.addEventListener('load', function () {
        init('location');
        init('content');
    });
})(window, document);
