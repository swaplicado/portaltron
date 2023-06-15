window.history.pushState(null, "", window.location.href);
window.onpopstate = function() {
    window.history.pushState(null, "", window.location.href);
};

function disableSubmitButton(form) {
    form.addEventListener('submit', function() {
        // Disable the submit button to prevent multiple submissions
        var submitButton = form.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
    });
}

$(document).ready(function () {
    var form = document.getElementById('login_form');
    disableSubmitButton(form);
});

function showMessage(message){
    SGui.showMessage(message, '' , 'error');
}