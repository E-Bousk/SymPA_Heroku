window.onload = () => {
    /** CONTACT */
    // Remplit le champ « titre » de la modale
    const title = document.querySelector('.card-header').innerHTML;
    document.querySelector("#annonce_contact_title").value = title;

    /** COMMENTS */
    // Met un écouteur d'évènements sur tous les boutons « répondre »
    document.querySelectorAll('[data-reply]').forEach(element => {
        // Met la valeur de l'id du commentaire sur lequel on clique dans l'input caché « comments_parentId »
        element.addEventListener('click', function() {
            document.querySelector('#comments_parentId').value = this.dataset.id
        })
    });
}