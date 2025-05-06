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
});