// récupère le click sur le lien "supprimer"
// Récupère dans la balise "a" le token
// Envoie la requête sur l'URL
// Supprime l'image et le lien si réponse OK

window.onload = () => {
  // Gestion des boutons "Supprimer"
  let links = document.querySelectorAll("[data-delete]");

  links.forEach(el => {
    el.addEventListener('click', function(e){
      // Désactive le lien (empêche la navigation)
      e.preventDefault();
      // Demande confirmation
      if (confirm("Voulez-vous supprimer cette image ?")) {
        // Envoie requête AJAX vers le href du lien avec la méthode 'DELETE'
        // NOTE: « this » est le lien sur lequel on a cliqué
        fetch(this.getAttribute('href'), {
          method: "DELETE",
          headers: {
            'X-Requested-With': "XMLHttpRequest",
            "Content-Type": "application/Json"
          },
          // Récupère le token
          body: JSON.stringify({'_token': this.dataset.token})
        }).then(
          // Récupère la réponse en JSON
          response => response.json()
        ).then(data => {
          if (data.success)
            // Supprime la « div » qui contient le lien (et l'image)
            this.parentElement.remove()
          else
            alert(data.error)
        }).catch(e => alert(e))
      }
    })
  });
}