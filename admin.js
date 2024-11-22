document.addEventListener('DOMContentLoaded', function() {
    var quill = new Quill('#quill-editor', {
        theme: 'snow'
    });

    var modalElement = document.getElementById('event-modal');
    var modal = new bootstrap.Modal(modalElement);
    var addButton = document.getElementById('add-event-button');
    var form = document.getElementById('event-form');
    var modalTitle = document.getElementById('modal-title');
    var saveButton = document.getElementById('save-event-button');
    var eventIdField = document.getElementById('event-id');
    var eventNameField = document.getElementById('event-name');
    var eventYearField = document.getElementById('event-year');
    var eventDescriptionField = document.getElementById('event-description');
    var eventLocationField = document.getElementById('event-location'); // Added location field

    addButton.onclick = function() {
        modalTitle.textContent = 'Add New Event';
        saveButton.name = 'dt_add_event';
        eventIdField.value = '';
        eventNameField.value = '';
        eventYearField.value = '';
        eventLocationField.value = ''; // Clear location field
        quill.root.innerHTML = '';
        modal.show();
    };

    form.addEventListener('submit', function() {
        eventDescriptionField.value = quill.root.innerHTML;
    });

    document.querySelectorAll('.update-event-button').forEach(function(button) {
        button.addEventListener('click', function() {
            modalTitle.textContent = 'Update Event';
            saveButton.name = 'dt_update_event';
            eventIdField.value = button.getAttribute('data-id');
            eventNameField.value = button.getAttribute('data-name');
            eventYearField.value = button.getAttribute('data-year');
            eventLocationField.value = button.getAttribute('data-location'); // Set location field value
            quill.root.innerHTML = button.getAttribute('data-description');
            modal.show();
        });
    });
});
