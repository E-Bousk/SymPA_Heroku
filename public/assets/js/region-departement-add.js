let collection, addButton, span

window.onload = () => {
    collection = document.querySelector("#departements");

    span = collection.querySelector("span");

    addButton = document.createElement("button");
    addButton.className = "ajout-departement btn shadow-1 rounded-1 small secondary";
    addButton.innerText = "Ajouter un département";

    let newButton = span.append(addButton);

    // Crée un « data-index » et compte le nombre d'input (formulaire) générés (ex : « data-index="0" »)
    collection.dataset.index = collection.querySelectorAll("input").length;

    addButton.addEventListener('click', function() {
        buttonAdd(collection, newButton);
    });

    function buttonAdd(collection, newButton) {
        let prototype = collection.dataset.prototype; // chaîne de caractère  

        let index = collection.dataset.index;

        prototype = prototype.replace(/__name__/g, index);

        let content = document.createElement("html");
        content.innerHTML = prototype;

        let newForm = content.querySelector("div"); // objet du DOM

        let deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className = "mb-3 btn shadow-1 rounded-1 small red";
        deleteButton.id = "delete-departement-" + index;
        deleteButton.innerText = "Supprimer ce département";

        newForm.append(deleteButton);

        collection.dataset.index++;

        let addButton = collection.querySelector(".ajout-departement");

        span.insertBefore(newForm, addButton);

        deleteButton.addEventListener('click', function() {
            this.previousElementSibling.parentElement.remove();
        })
    }
}