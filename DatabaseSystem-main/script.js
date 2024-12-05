'use strict';

const addEventOnElem = function (elem, type, callback) {
  if (elem.length > 1) {
    for (let i = 0; i < elem.length; i++) {
      elem[i].addEventListener(type, callback);
    }
  } else {
    elem.addEventListener(type, callback);
  }
}

// Navbar toggle
const navbar = document.querySelector("[data-navbar]");
const navbarLinks = document.querySelectorAll("[data-nav-link]");
const navToggler = document.querySelector("[data-nav-toggler]");

const toggleNavbar = function () {
  navbar.classList.toggle("active");
  this.classList.toggle("active");
}

addEventOnElem(navToggler, "click", toggleNavbar);

const closeNavbar = function () {
  navbar.classList.remove("active");
  navToggler.classList.remove("active");
}

addEventOnElem(navbarLinks, "click", closeNavbar);

// Search bar toggle
const searchBar = document.querySelector("[data-search-bar]");
const searchTogglers = document.querySelectorAll("[data-search-toggler]");
const overlay = document.querySelector("[data-overlay]");

const toggleSearchBar = function () {
  searchBar.classList.toggle("active");
  overlay.classList.toggle("active");
  document.body.classList.toggle("active");
}

addEventOnElem(searchTogglers, "click", toggleSearchBar);

// Search functionality
const searchForm = document.getElementById('searchForm');
const searchInput = document.getElementById('searchInput');
const resultsList = document.getElementById('resultsList');
const categoryItems = document.querySelectorAll('.category-item');

const searchRestaurants = (query) => {
  // Replace with actual search logic and API calls
  const results = [
    { name: 'Taco Place', category: 'mexican' },
    { name: 'Burger Joint', category: 'american' },
    { name: 'Sushi Spot', category: 'asian' },
  ];

  const filteredResults = results.filter(item => 
    item.name.toLowerCase().includes(query.toLowerCase())
  );

  displayResults(filteredResults);
}

const filterByCategory = (category) => {
  // Replace with actual category filtering logic and API calls
  const results = [
    { name: 'Taco Place', category: 'mexican' },
    { name: 'Burger Joint', category: 'american' },
    { name: 'Sushi Spot', category: 'asian' },
  ];

  const filteredResults = results.filter(item => 
    item.category === category
  );

  displayResults(filteredResults);
}

const displayResults = (results) => {
  resultsList.innerHTML = '';
  results.forEach(result => {
    const listItem = document.createElement('li');
    listItem.classList.add('results-item');
    listItem.textContent = result.name;
    resultsList.appendChild(listItem);
  });
}

searchForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const query = searchInput.value;
  searchRestaurants(query);
});

categoryItems.forEach(item => {
  item.addEventListener('click', () => {
    const category = item.dataset.category;
    filterByCategory(category);
  });
});
