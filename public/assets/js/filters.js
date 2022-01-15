window.onload = () => {
    const filtersForm = document.querySelector("#filters");

    // Boucle sur les inputs
    document.querySelectorAll("#filters input").forEach(input => {
        // Inttercepte les cliques
        input.addEventListener("change", () => {
            // Récupère les données du formulaire
            const Form = new FormData(filtersForm);

            // Crée la 'queryString' de l'URL
            const Params = new URLSearchParams();

            // Boucle sur les données du formulaire et récupère le 'queryString' correspondant
            Form.forEach((value, key) => {
                // console.log(key, value)
                Params.append(key, value)
                // console.log(Params.toString());
            });

            // Récupère l'URL active
            const Url = new URL(window.location.href);
            // console.log(Url);

            // Lance une requête Ajax (« &ajax=1 » sert à filtrer dans le controlleur)
            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
                headers: {
                    "x-requested-with": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()
            ).then(data => {
                // console.log(data);
                // Récupère la zone de contenu
                const content = document.querySelector('#content');
                // pour y injecter le nouveau code
                content.innerHTML = data.content;

                // Met à jour l'URL
                history.pushState({}, null, Url.pathname + "?" + Params.toString())
            })
            .catch(e => alert(e));

        });
    });








}