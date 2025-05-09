import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './app.css';
import './styles/base.css';
import './styles/layout.css';
import './styles/components/footer.css';
import './styles/components/hero.css';
import './styles/components/news.css';
import './styles/components/header.css';

// Navbar pour mobile
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navbar = document.querySelector('.navbar');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            navbar.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }

    document.addEventListener('click', function(event) {
        if (navbar.classList.contains('active') &&
            !event.target.closest('.navbar') &&
            !event.target.closest('.mobile-menu-toggle')) {
            mobileMenuToggle.classList.remove('active');
            navbar.classList.remove('active');
            document.body.classList.remove('menu-open');
        }
    });

    //Effet de défilement lisse
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.site-header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
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

    // Add CSS for animation
    const style = document.createElement('style');
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

        /* Add delay to stagger animations */
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

    // Run animation on page load and scroll
    window.addEventListener('load', animateOnScroll);
    window.addEventListener('scroll', animateOnScroll);


});