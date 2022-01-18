window.onload = () => {
    // Récupère la région
    let region = document.querySelector('#offers_regions');

    region.addEventListener("change", function () {
        // Récupère le formulaire complet
        // NOTE: « closest » = balise la plus proche de l'élément
        let form = this.closest("form");

        // récupère la 'value' et contatène au 'name' (ex : « offers[regions]=1 »)
        let data = this.name + "=" + this.value;

        // Envoie en Ajax
        fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded; charset:utf-8"
            }
        })
        .then(response => response.text())
        .then(html => {
            let content = document.createElement("html");
            content.innerHTML = html;
            let newSelect = content.querySelector('#offers_departements');
            document.querySelector("#offers_departements").replaceWith(newSelect);
        })
        .catch(error => {
            console.log(error);
        })
    });
}