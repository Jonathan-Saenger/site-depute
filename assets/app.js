import './bootstrap.js';
import './app.css';
import './styles/actu.css';
import './styles/base.css';
import './styles/layout.css';
import './styles/rencontre.css';
import './styles/components/footer.css';
import './styles/components/hero.css';
import './styles/components/news.css';
import './styles/components/header.css';
import './styles/components/priorities.css';
import './styles/components/work.css';
import './styles/components/newsletter.css';

// Fonction d'initialisation principale (compatible Turbo)
function initSiteScripts() {
    // Navbar pour mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navbar = document.querySelector('.navbar');

    if (mobileMenuToggle && navbar) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            navbar.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        document.addEventListener('click', function(event) {
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
    }

    // Effet de défilement lisse sur le header
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.site-header');
        if (header) {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });

    // Animation on scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('.news-card, .priority-card, .stat-item');
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            if (elementPosition < windowHeight - 100) {
                element.classList.add('visible');
            }
        });
    }

    // Ajout du CSS pour l'animation si non déjà présent
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

    // Run animation on page load and scroll
    window.addEventListener('load', animateOnScroll);
    window.addEventListener('scroll', animateOnScroll);

    // Dropdown actualités (menu déroulant des actus)
    const dropdown = document.querySelector('.dropdown-news');
    if (dropdown) {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) dropdown.classList.remove('open');
        });
    }

    // Filtrage des articles par titre
    const searchInput = document.querySelector('.search-input');
    const newsCards = document.querySelectorAll('.news-card');
    const noArticlesMessage = document.getElementById('no-articles-message');

    if (searchInput && newsCards && noArticlesMessage) {
        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase();
            let foundArticles = false;

            newsCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                if (title.includes(query)) {
                    card.style.display = '';
                    foundArticles = true;
                } else {
                    card.style.display = 'none';
                }
            });

            noArticlesMessage.style.display = foundArticles ? 'none' : 'block';
        });
    }

    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get email value
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();

            // Validation simple d'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Veuillez entrer une adresse email valide.');
                return;
            }

            // Efface le champ
            emailInput.value = '';

            // Affiche le message de succès
            let successMessage = this.querySelector('.success-message');
            if (!successMessage) {
                successMessage = document.createElement('div');
                successMessage.className = 'success-message';
                successMessage.style.color = '#0066cc';
                successMessage.style.marginTop = '1rem';
                successMessage.style.fontWeight = '500';
                this.appendChild(successMessage);
            }
            successMessage.textContent = 'Merci pour votre inscription !';

            // Retire le message après 3 secondes
            setTimeout(() => {
                if (successMessage) successMessage.remove();
            }, 3000);
        });
    }
}

// Initialisation pour navigation classique ET Turbo
document.addEventListener('DOMContentLoaded', initSiteScripts);
document.addEventListener('turbo:load', initSiteScripts);