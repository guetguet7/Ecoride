'use strict';

document.getElementById("formContact").addEventListener("submit", function(e) {
  e.preventDefault(); // Empêche le rechargement de la page

  // Récupération des valeurs
  const nom = document.getElementById("nom").value;
  const email = document.getElementById("email").value;
  const sujet = document.getElementById("sujet").value;
  const message = document.getElementById("message").value;


  // Affiche les données récupérées
  const resultat = document.getElementById("resultat");
  resultat.style.display = "block";
  resultat.innerHTML = `
    <h5> Message envoyé avec succès !</h5>
    <p><strong>Nom :</strong> ${nom}</p>
    <p><strong>Email :</strong> ${email}</p>
    <p><strong>Sujet :</strong> ${sujet}</p>
    <p><strong>Message :</strong> ${message}</p>
  `;

  // Vide le formulaire
  document.getElementById("formContact").reset();

  // Simule un "envoi"
  console.log("Données du formulaire :", { nom, email, sujet, message });
});