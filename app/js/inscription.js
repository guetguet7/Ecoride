'use strict';

document.getElementById("formInscription").addEventListener("submit", function(e){
  e.preventDefault();

  let pseudo = document.getElementById("pseudo").value;
  let email = document.getElementById("email").value;
  let password = document.getElementById("password").value;
  let role = document.querySelector('input[name="role"]:checked');

  if(!role){
    alert("Veuillez sélectionner chauffeur, passager ou les deux.");
    return;
  }

  // Sauvegarde dans localStorage
  localStorage.setItem("pseudo", pseudo);
  localStorage.setItem("email", email);
  localStorage.setItem("password", password);
  localStorage.setItem("role", role.value);

  // Redirection selon rôle
  if(role.value === "chauffeur" || role.value === "lesdeux"){
    window.location.href = "utilisateur.html";
  } else {
    window.location.href = "index.html";
  }
});
