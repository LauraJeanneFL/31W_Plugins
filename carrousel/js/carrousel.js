(function () {
  console.log("Vive JavaScript");

  // Sélection des éléments
  const carrousel__bouton = document.querySelector(".carrousel__bouton");
  const carrousel = document.querySelector(".carrousel");
  const carrousel__x = document.querySelector(".carrousel__x");
  const carrousel__gauche = document.querySelector(".carrousel__gauche");
  const carrousel__droite = document.querySelector(".carrousel__droite");
  const carrousel__figure = document.querySelector(".carrousel__figure");
  const galerie__img = document.querySelectorAll(".wp-block-gallery img");

  let currentIndex = 0;

  // Vérifie si des images existent
  if (!galerie__img.length) {
    console.error("Aucune image trouvée dans la galerie.");
    return;
  }

  // Remplit le carrousel
  function remplirCarrousel() {
    carrousel__figure.innerHTML = ""; // Vide les anciennes images
    galerie__img.forEach((img) => {
      const nouvelleImage = document.createElement("img");
      nouvelleImage.src = img.src;
      nouvelleImage.classList.add("carrousel__img");
      carrousel__figure.appendChild(nouvelleImage);
    });
  }

  // Affiche une image spécifique
  function afficheImage(index) {
    const carrousel__img = document.querySelectorAll(".carrousel__img");
    carrousel__img.forEach((img, i) => {
      img.style.display = i === index ? "block" : "none";
    });
    currentIndex = index;
  }

  // Ajoute les événements
  carrousel__bouton.addEventListener("click", () => {
    remplirCarrousel();
    afficheImage(0);
    carrousel.classList.add("carrousel--ouvrir");
  });

  carrousel__x.addEventListener("click", () => {
    carrousel.classList.remove("carrousel--ouvrir");
  });

  carrousel__gauche.addEventListener("click", () => {
    currentIndex =
      (currentIndex - 1 + galerie__img.length) % galerie__img.length;
    afficheImage(currentIndex);
  });

  carrousel__droite.addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % galerie__img.length;
    afficheImage(currentIndex);
  });
})();
