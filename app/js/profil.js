document.addEventListener("DOMContentLoaded", function() {
  // On récupère les infos enregistrées
  let chauffeurData = JSON.parse(localStorage.getItem("chauffeurData"));

  // Si pas d'infos => redirection vers le formulaire
  if (!chauffeurData) {
    alert("Aucune information trouvée. Veuillez remplir le formulaire chauffeur.");
    window.location.href = "chauffeur.html";
    return;
  }

  // On affiche les informations dans la page
  let html = `
    <ul class="list-group">
      <li class="list-group-item"><strong>Plaque :</strong> ${chauffeurData.plaque}</li>
      <li class="list-group-item"><strong>Date d'immatriculation :</strong> ${chauffeurData.dateImmat}</li>
      <li class="list-group-item"><strong>Véhicule :</strong> ${chauffeurData.marque} ${chauffeurData.modele} (${chauffeurData.couleur})</li>
      <li class="list-group-item"><strong>Places disponibles :</strong> ${chauffeurData.places}</li>
      <li class="list-group-item"><strong>Préférences :</strong>
        ${chauffeurData.fumeur ? "Fumeur accepté" : "Non-fumeur"} /
        ${chauffeurData.animal ? "Animaux acceptés" : "Pas d'animaux"}
      </li>
    </ul>
  `;

  document.getElementById("infosChauffeur").innerHTML = html;
});

// Simulation d'un utilisateur connecté
// (dans un vrai site, ces infos viendraient du backend ou du localStorage)
'use strict';
let utilisateur = {
  type: "chauffeur", // changer ici : "chauffeur" ou "passager"
  pseudo: "JeanDupont",
  email: "jean.dupont@mail.com",
  vehicules: [
    { marque: "Peugeot", modele: "208", couleur: "Bleu", plaque: "AB-123-CD" }
  ]
};

// Fonction d'affichage du profil selon le type d'utilisateur
function afficherProfil() {
  document.getElementById("pseudo").value = utilisateur.pseudo;
  document.getElementById("email").value = utilisateur.email;

  if (utilisateur.type === "chauffeur") {
    document.getElementById("sectionChauffeur").style.display = "block";
    document.getElementById("sectionPassager").style.display = "none";
    afficherVehicules();
  } else {
    document.getElementById("sectionPassager").style.display = "block";
    document.getElementById("sectionChauffeur").style.display = "none";
  }
}

// Affiche la liste des véhicules du chauffeur
function afficherVehicules() {
  const liste = document.getElementById("listeVehicules");
  liste.innerHTML = "";

  utilisateur.vehicules.forEach((v, index) => {
    const li = document.createElement("li");
    li.className = "list-group-item d-flex justify-content-between align-items-center";
    li.innerHTML = `
      <span>${v.marque} ${v.modele} (${v.couleur}) - ${v.plaque}</span>
      <button class="btn btn-sm btn-danger" onclick="supprimerVehicule(${index})">Supprimer</button>
    `;
    liste.appendChild(li);
  });
}

// Ajouter un véhicule
document.getElementById("formVehicule").addEventListener("submit", function(e) {
  e.preventDefault();
  const marque = document.getElementById("marque").value;
  const modele = document.getElementById("modele").value;
  const couleur = document.getElementById("couleur").value;
  const plaque = document.getElementById("plaque").value;

  utilisateur.vehicules.push({ marque, modele, couleur, plaque });
  afficherVehicules();
  this.reset();
});

// Supprimer un véhicule
function supprimerVehicule(index) {
  utilisateur.vehicules.splice(index, 1);
  afficherVehicules();
}

// Sauvegarder les infos personnelles
document.getElementById("btnSaveInfos").addEventListener("click", function() {
  utilisateur.pseudo = document.getElementById("pseudo").value;
  utilisateur.email = document.getElementById("email").value;
  alert("Informations mises à jour !");
});

// Passager → Chauffeur
document.getElementById("btnDevenirChauffeur").addEventListener("click", function() {
  utilisateur.type = "chauffeur";
  alert("Vous êtes maintenant chauffeur !");
  afficherProfil();
});

// Au chargement
afficherProfil();

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