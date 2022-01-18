// Initialise CKEditor
// ‼ Valable uniquement pour la version 'balloon' ‼
BalloonEditor
.create(
    document.querySelector('#editor'), {
        toolbar: ['heading','|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList'],
        heading: {
            options: [{
                model: 'paragraph',
                title: 'Paragraphe',
                class: 'ck-heading_paragraph'
            },
            {
                model: 'heading1',
                title: 'Titre 1',
                class: 'ck-heading_heading1',
                view: 'h1'
            },
            {
                model: 'heading2',
                title: 'Titre 2',
                class: 'ck-heading_heading2',
                view: 'h2'
            }]
        }
    }
)
.then(editor => {
    // document.querySelector("#ajout-commentaire form").addEventListener("submit", function(e){
    // console.log(editor);

    // Cherche l'élément dans lequel est l'éditeur, prend son parent (formulaire) et met un 'listeneur' sur son 'submit'
    editor.sourceElement.parentElement.addEventListener("submit", function(e){
        // Interrompt l'envoi du formulaire
        e.preventDefault();
        // Modifie le champ (ajoute les données de la « div.id=editor » dans l'input (hidden))
        // ie : prend ce qui a été tapé dans l'éditeur pour le mettre dans l'input caché
        // NOTE : le selecteur CSS « + » signifie « juste après » ==> l'input qui suit « #editor »
        this.querySelector("#editor + input").value = editor.getData();
        // Envoie le formulaire
        this.submit();
    })
});