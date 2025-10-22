document.addEventListener("DOMContentLoaded", () => {
  // Affiche le pseudo de l'utilisateur
  let pseudo = localStorage.getItem("pseudo");
  if(pseudo){
    document.getElementById("userPseudo").innerText = pseudo;
  }

  // Sauvegarde des infos véhicule
  document.getElementById("formVehicule").addEventListener("submit", function(e){
    e.preventDefault();

    let vehicule = {
      plaque: document.getElementById("plaque").value,
      dateImmat: document.getElementById("dateImmat").value,
      marque: document.getElementById("marque").value,
      modele: document.getElementById("modele").value,
      couleur: document.getElementById("couleur").value,
      places: document.getElementById("places").value,
      fumeur: document.getElementById("fumeur").checked,
      animal: document.getElementById("animal").checked,
      autres: document.getElementById("autresPrefs").value
    };

    localStorage.setItem("vehicule", JSON.stringify(vehicule));

    alert("Informations sauvegardées !");
    window.location.href = "index.html";
  });
});