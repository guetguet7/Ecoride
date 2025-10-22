'use strict';
document.getElementById("formChauffeur").addEventListener("submit", function(e) {
  e.preventDefault();

  // On récupère toutes les infos du formulaire
  let chauffeurData = {
    plaque: document.getElementById("plaque").value,
    dateImmat: document.getElementById("dateImmat").value,
    marque: document.getElementById("marque").value,
    modele: document.getElementById("modele").value,
    couleur: document.getElementById("couleur").value,
    places: document.getElementById("places").value,
    fumeur: document.getElementById("fumeur").checked,
    animal: document.getElementById("animal").checked
  };

  // Enregistre les infos dans le localStorage
  localStorage.setItem("chauffeurData", JSON.stringify(chauffeurData));

  // Redirige vers la page profil
  window.location.href = "profil.html";
});