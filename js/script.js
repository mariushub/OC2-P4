/**
 * Petits scripts du site TomTroc, en JavaScript natif (aucune librairie).
 * Le fichier est chargé avec l'attribut "defer" : il s'exécute une fois toute la page HTML lue.
 */

// 1. Photo de profil : le formulaire est envoyé dès qu'une image a été choisie.
document.querySelectorAll('[data-auto-submit]').forEach(function (input) {
    input.addEventListener('change', function () {
        if (input.files.length > 0) {
            input.form.submit();
        }
    });
});

// 2. Formulaire d'un livre : on affiche un aperçu de la nouvelle photo avant de valider.
document.querySelectorAll('[data-preview]').forEach(function (input) {
    const preview = document.getElementById(input.dataset.preview);
    input.addEventListener('change', function () {
        if (preview && input.files.length > 0) {
            preview.src = URL.createObjectURL(input.files[0]);
        }
    });
});

// 3. Suppression d'un livre : on demande une confirmation avant d'envoyer le formulaire.
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

// 4. Messagerie : on fait défiler le fil jusqu'en bas pour voir directement les derniers messages.
const thread = document.getElementById('thread-messages');
if (thread) {
    thread.scrollTop = thread.scrollHeight;
}

// 5. Messagerie : la touche Entrée envoie le message, Maj + Entrée passe à la ligne.
const messageField = document.getElementById('new-message');
if (messageField) {
    messageField.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            if (messageField.value.trim() !== '') {
                messageField.form.submit();
            }
        }
    });
}
