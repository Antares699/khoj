// script.js
document.addEventListener("DOMContentLoaded", function () {
  const burger = document.getElementById("burger");
  const nav = document.getElementById("nav-links");

  if (burger && nav) {
    burger.addEventListener("click", () => {
      nav.classList.toggle("active");
    });
  }

  // --- Homepage Autocomplete ---
  const searchInput = document.getElementById("searchInput");
  const resultsContainer = document.getElementById("home-autocomplete-results");

  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  if (searchInput && resultsContainer) {
    const fetchBusinesses = async (query) => {
      if (query.length < 2) {
        resultsContainer.style.display = "none";
        return;
      }

      try {
        const response = await fetch(
          `api/search_businesses.php?q=${encodeURIComponent(query)}`,
        );
        const data = await response.json();
        resultsContainer.innerHTML = "";

        if (data.length > 0) {
          resultsContainer.style.display = "block";

          data.forEach((biz) => {
            const a = document.createElement("a");
            a.className = "autocomplete-item";
            a.href = `business.php?id=${biz.id}`;
            a.innerHTML = `
                        <div class="ac-text-box" style="margin-left: 12px;">
                            <span class="ac-main-text">${biz.name}</span>
                            <span class="ac-sub-text">${biz.category} • ${biz.location}</span>
                        </div>
                    `;
            resultsContainer.appendChild(a);
          });
        } else {
          resultsContainer.style.display = "none";
        }
      } catch (error) {
        console.error("Error fetching autocomplete:", error);
      }
    };

    searchInput.addEventListener(
      "input",
      debounce((e) => {
        fetchBusinesses(e.target.value);
      }, 300),
    );

    // Hide dropdown if user clicks outside
    document.addEventListener("click", function (e) {
      if (
        !searchInput.contains(e.target) &&
        !resultsContainer.contains(e.target)
      ) {
        resultsContainer.style.display = "none";
      }
    });
  }

  // --- Header Autocomplete (Explore Page) ---
  const headerSearchInput = document.getElementById("headerSearchInput");
  const headerResultsContainer = document.getElementById(
    "header-autocomplete-results",
  );

  if (headerSearchInput && headerResultsContainer) {
    // Reuse debounce
    const fetchHeaderBusinesses = async (query) => {
      if (query.length < 2) {
        headerResultsContainer.style.display = "none";
        return;
      }

      try {
        const response = await fetch(
          `api/search_businesses.php?q=${encodeURIComponent(query)}`,
        );
        const data = await response.json();
        headerResultsContainer.innerHTML = "";

        if (data.length > 0) {
          headerResultsContainer.style.display = "block";

          data.forEach((biz) => {
            const a = document.createElement("a");
            a.className = "autocomplete-item";
            a.href = `business.php?id=${biz.id}`;
            a.innerHTML = `
                            <div class="ac-text-box" style="margin-left: 12px;">
                                <span class="ac-main-text">${biz.name}</span>
                                <span class="ac-sub-text">${biz.category} • ${biz.location}</span>
                            </div>
                        `;
            headerResultsContainer.appendChild(a);
          });
        } else {
          headerResultsContainer.style.display = "none";
        }
      } catch (error) {
        console.error("Error fetching autocomplete:", error);
      }
    };

    headerSearchInput.addEventListener(
      "input",
      debounce((e) => {
        fetchHeaderBusinesses(e.target.value);
      }, 300),
    );

    // Hide dropdown if user clicks outside
    document.addEventListener("click", function (e) {
      if (
        !headerSearchInput.contains(e.target) &&
        !headerResultsContainer.contains(e.target)
      ) {
        headerResultsContainer.style.display = "none";
      }
    });

    // Handle search button click
    const searchBtn = document.querySelector(".h-search-go");
    if (searchBtn) {
      searchBtn.addEventListener("click", () => {
        const q = document.getElementById("headerSearchInput").value;
        const loc = document.querySelector(
          '.h-search-box input[placeholder="Location"]',
        ).value;
        window.location.href = `explore.php?q=${encodeURIComponent(q)}&loc=${encodeURIComponent(loc)}`;
      });
    }
  }
});
