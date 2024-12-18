(function () {
  // Sélectionner tous les boutons avec la classe `filtre__bouton`
  const filtreBoutons = document.querySelectorAll(".filtre__bouton button");

  // Vérifier si des boutons sont présents
  if (!filtreBoutons.length) {
    console.error("Aucun bouton de filtre trouvé.");
    return;
  }

  // Ajouter un écouteur d'événement pour chaque bouton
  filtreBoutons.forEach((button) => {
    button.addEventListener("click", () => {
      const categorie = button.getAttribute("data-id"); // Récupérer l'ID de catégorie
      if (!categorie) {
        console.error("ID de catégorie non trouvé pour ce bouton.");
        return;
      }

      // Lancer la fonction pour extraire les articles
      extraireCours(categorie);
    });
  });

  // Fonction pour extraire les articles via l'API REST
  function extraireCours(categorie) {
    const restUrl = `https://localhost:81/31w05/wp-json/wp/v2/posts?categories=${categorie}&per_page=30`;

    fetch(restUrl)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Articles récupérés:", data);
        afficherArticles(data);
      })
      .catch((error) => {
        console.error("Erreur lors de l'extraction des cours:", error);
      });
  }

  // Fonction pour afficher les articles
  function afficherArticles(data) {
    const resultsDiv = document.getElementById("filtrepost-results");
    if (!resultsDiv) {
      console.error("Div pour afficher les résultats introuvable.");
      return;
    }

    resultsDiv.innerHTML = ""; // Nettoyer les résultats précédents

    if (data.length === 0) {
      resultsDiv.innerHTML =
        "<p>Aucun article trouvé pour cette catégorie.</p>";
      return;
    }

    // Afficher chaque article dans le conteneur
    data.forEach((article) => {
      const articleElement = document.createElement("p");
      articleElement.innerHTML = `<a href="${article.link}" target="_blank">${article.title.rendered}</a>`;
      resultsDiv.appendChild(articleElement);
    });
  }
})();
