'use strict';

//récurperer le bouton participer dans covoiturage.js
document.getElementById("btnParticiper").addEventListener("click", () => {
  //une alerte de confirmation de participation et de payer le prix du covoiturage
  alert("Merci pour votre participation ! Veuillez confirmer votre paiement de 5 crédits pour ce voyage.");
  window.location.href = "utilisateur.html";
});