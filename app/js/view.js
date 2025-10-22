'use strict';

// ##################################################################### //
// ############################## ACCUEIL ############################## //
// ##################################################################### //
// Récupérer les infos du formulaire
document.getElementById("formRecherche").addEventListener("submit", function(e){
  e.preventDefault();

  let depart = document.getElementById("depart").value.trim();
  let arrivee = document.getElementById("arrivee").value.trim();
  let date = document.getElementById("date").value;

  // Vérification des champs
  if(!depart || !arrivee || !date){
    alert("Merci de remplir tous les champs !");
    return;
  }

  // Sauvegarde les infos dans le localStorage
  let recherche = { depart, arrivee, date };
  localStorage.setItem("recherche", JSON.stringify(recherche));

  // Redirection vers la page covoiturage
  window.location.href = "covoiturage.html";
});

