window.onload = () => {
  let switchActivate = document.querySelectorAll("[type=checkbox]");
  for (let button of switchActivate) {
    button.addEventListener("click", function () {
      let xmlhttp = new XMLHttpRequest();
      xmlhttp.open("get", `/admin/offers/activate/${this.dataset.id}`);
      xmlhttp.send();
    });
  }

  let modalDelete = document.querySelectorAll(".modal-trigger");
  for (let button of modalDelete) {
    button.addEventListener("click", function () {
      document.querySelector(".modal-footer a").href = `/admin/offers/delete/${this.dataset.id}`
      document.querySelector(".modal-content").innerText = `Vous êtes sur le point d'effacer l'annonce « ${this.dataset.title} ».\nÊtes-vous sûr(e) de vouloir continuer ?`
    });
  }
};
