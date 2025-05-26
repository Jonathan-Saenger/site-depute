import './bootstrap.js';
import './app.css';
import './styles/actu.css';
import './styles/base.css';
import './styles/layout.css';
import './styles/rencontre.css';
import './styles/depute.css';
import './styles/presse.css';
import './styles/mentions.css';
import './styles/admin.css';
import './styles/components/footer.css';
import './styles/components/hero.css';
import './styles/components/news.css';
import './styles/components/header.css';
import './styles/components/priorities.css';
import './styles/components/work.css';
import './styles/components/newsletter.css';
import './styles/components/permanence.css';

/**
 * Fonction d'initialisation principale (compatible Turbo)
 */
const initSiteScripts = () => {
    // Initialiser tous les modules de fonctionnalités
    initMobileNavbar();
    initHeaderScrollEffect();
    initAnimations();
    initNewsDropdown();
    initNewsSearch();
    initNewsletterForm();
    initRencontreFilter();
};

/**
 * Gestion de la navbar mobile
 */
const initMobileNavbar = () => {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navbar = document.querySelector('.navbar');
    if (!mobileMenuToggle || !navbar) return;

    // Ouvrir/fermer le menu
    mobileMenuToggle.addEventListener('click', () => {
        mobileMenuToggle.classList.toggle('active');
        navbar.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    });

    // Fermer le menu en cliquant à l'extérieur
    document.addEventListener('click', (event) => {
        if (
            navbar.classList.contains('active') &&
            !event.target.closest('.navbar') &&
            !event.target.closest('.mobile-menu-toggle')
        ) {
            mobileMenuToggle.classList.remove('active');
            navbar.classList.remove('active');
            document.body.classList.remove('menu-open');
        }
    });
};

/**
 * Effet de défilement sur le header
 */
const initHeaderScrollEffect = () => {
    const header = document.querySelector('.site-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 50);
    });
};

/**
 * Animations basées sur le scroll
 */
const initAnimations = () => {
    // Définir les animations CSS si non déjà présentes
    if (!document.getElementById('site-animation-style')) {
        const style = document.createElement('style');
        style.id = 'site-animation-style';
        style.textContent = `
            .news-card, .priority-card, .stat-item {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }
            .news-card.visible, .priority-card.visible, .stat-item.visible {
                opacity: 1;
                transform: translateY(0);
            }
            .site-header.scrolled {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                transform: translateY(0);
            }
            @media (max-width: 991px) {
                body.menu-open {
                    overflow: hidden;
                }
            }
            .news-card:nth-child(1), .priority-card:nth-child(1), .stat-item:nth-child(1) {
                transition-delay: 0.1s;
            }
            .news-card:nth-child(2), .priority-card:nth-child(2), .stat-item:nth-child(2) {
                transition-delay: 0.3s;
            }
            .news-card:nth-child(3), .priority-card:nth-child(3), .stat-item:nth-child(3) {
                transition-delay: 0.5s;
            }
            .news-card:nth-child(4), .priority-card:nth-child(4), .stat-item:nth-child(4) {
                transition-delay: 0.6s;
            }
        `;
        document.head.appendChild(style);
    }

    // Fonction pour animer les éléments
    const animateElements = () => {
        const elements = document.querySelectorAll('.news-card, .priority-card, .stat-item');
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (elementPosition < windowHeight - 100) {
                element.classList.add('visible');
            }
        });
    };

    // Lancer l'animation au chargement et au défilement
    window.addEventListener('load', animateElements);
    window.addEventListener('scroll', animateElements);
};

/**
 * Menu déroulant des actualités
 */
const initNewsDropdown = () => {
    const dropdown = document.querySelector('.dropdown-news');
    if (!dropdown) return;

    const toggle = dropdown.querySelector('.dropdown-toggle');
    toggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) dropdown.classList.remove('open');
    });
};

/**
 * Filtre de recherche d'actualités
 */
const initNewsSearch = () => {
    const searchInput = document.querySelector('.search-input');
    const newsCards = document.querySelectorAll('.news-card');
    const noArticlesMessage = document.getElementById('no-articles-message');

    if (!searchInput || !newsCards.length || !noArticlesMessage) return;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        let foundArticles = false;

        newsCards.forEach(card => {
            const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
            const isVisible = title.includes(query);
            card.style.display = isVisible ? '' : 'none';
            if (isVisible) foundArticles = true;
        });

        noArticlesMessage.style.display = foundArticles ? 'none' : 'block';
    });
};

/**
 * Formulaire d'inscription à la newsletter
 */
const initNewsletterForm = () => {
    const newsletterForm = document.querySelector('.newsletter-form');
    if (!newsletterForm) return;

    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // Validation de l'email
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        const email = emailInput?.value.trim() || '';
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            alert('Veuillez entrer une adresse email valide.');
            return;
        }

        // Réinitialiser et afficher le succès
        emailInput.value = '';

        // Afficher le message de confirmation
        const showSuccessMessage = () => {
            let successMessage = newsletterForm.querySelector('.success-message');

            if (!successMessage) {
                successMessage = document.createElement('div');
                successMessage.className = 'success-message';
                Object.assign(successMessage.style, {
                    color: '#0066cc',
                    marginTop: '1rem',
                    fontWeight: '500'
                });
                newsletterForm.appendChild(successMessage);
            }

            successMessage.textContent = 'Merci pour votre inscription !';
            setTimeout(() => successMessage?.remove(), 3000);
        };

        showSuccessMessage();
    });
};

/**
 * Filtrage des rencontres
 */
const initRencontreFilter = () => {
    const form = document.getElementById('rencontre-filter-form');
    if (!form) return;

    // Sélectionner les éléments DOM nécessaires
    const elements = {
        communeSelect: document.getElementById('commune-select'),
        typeSelect: document.getElementById('type-select'),
        filterButton: document.getElementById('filter-button'),
        resetButton: document.getElementById('reset-filter'),
        cards: document.querySelectorAll('.agenda-card'),
        container: document.querySelector('.agenda-cards')
    };

    const noEventsMessage = 'Aucun événement à venir n\'est programmé pour le moment.';

    // Mise à jour de l'URL pour conserver les paramètres de filtrage
    const updateUrl = (commune, type) => {
        const url = new URL(window.location);

        commune ? url.searchParams.set('commune', commune) : url.searchParams.delete('commune');
        type ? url.searchParams.set('type', type) : url.searchParams.delete('type');

        window.history.pushState({}, '', url.toString());
    };

    // Fonction de filtrage principale
    const filterEvents = () => {
        const selectedCommune = elements.communeSelect.value;
        const selectedType = elements.typeSelect.value;
        let visibleCount = 0;

        // Mise à jour URL et affichage du bouton de réinitialisation
        updateUrl(selectedCommune, selectedType);
        elements.resetButton.style.display = (selectedCommune || selectedType) ? '' : 'none';

        // Filtrer les cartes
        elements.cards.forEach(card => {
            const matches =
                (!selectedCommune || card.dataset.commune === selectedCommune) &&
                (!selectedType || card.dataset.type === selectedType);

            card.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        // Gestion du message "aucun événement"
        let noEventsElement = elements.container.querySelector('.no-events');

        if (visibleCount === 0) {
            if (!noEventsElement) {
                noEventsElement = document.createElement('p');
                noEventsElement.className = 'no-events';
                noEventsElement.textContent = noEventsMessage;
                elements.container.appendChild(noEventsElement);
            }
            noEventsElement.style.display = '';
        } else if (noEventsElement) {
            noEventsElement.style.display = 'none';
        }
    };

    // Cacher le bouton de filtre, car filtrage automatique
    if (elements.filterButton) {
        elements.filterButton.style.display = 'none';
    }

    // Ajouter les écouteurs d'événements
    elements.communeSelect.addEventListener('change', filterEvents);
    elements.typeSelect.addEventListener('change', filterEvents);
    elements.resetButton.addEventListener('click', () => {
        elements.communeSelect.value = '';
        elements.typeSelect.value = '';
        filterEvents();
    });
};

// Initialisation au chargement de la page (avec gestion Turbo)
['DOMContentLoaded', 'turbo:load'].forEach(event => {
    document.addEventListener(event, initSiteScripts);
});