document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("filtrecategorie-selecteur");
  const resultsContainer = document.getElementById("filtrecategorie-results");

    if (!select) {
    console.error("L'élément #filtrecategorie-selecteur est introuvable.");
    return;
    }

  select.addEventListener("change", function () {
    const categoryId = select.value;

    fetch(`${filtrecategorie.rest_url}?category_id=${categoryId}`, {
      headers: {
        "X-WP-Nonce": filtrecategorie.nonce,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        resultsContainer.innerHTML = ""; // Efface les anciens résultats
        if (data.length > 0) {
          data.forEach((post) => {
            resultsContainer.innerHTML += `
                        <div class="destination-item">
                            <h3>${post.title}</h3>
                            ${
                              post.thumbnail
                                ? `<img src="${post.thumbnail}" alt="${post.title}">`
                                : ""
                            }
                            <p>${post.excerpt}</p>
                            <a href="${
                              post.link
                            }" class="en-savoir-plus">En savoir plus</a>
                        </div>
                    `;
          });
        } else {
          resultsContainer.innerHTML = "<p>Aucun résultat trouvé.</p>";
        }
      })
      .catch((error) => {
        resultsContainer.innerHTML = "<p>Une erreur est survenue.</p>";
        console.error("Erreur : ", error);
      });
  });
});
