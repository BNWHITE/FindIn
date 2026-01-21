// assets/js/main.js - FindIN Platform 
// Modern theme toggle and utilities

document.addEventListener('DOMContentLoaded', function() {
    // ===== THEME TOGGLE FUNCTIONALITY =====
    const themeToggle = document.getElementById('themeToggle');
    const htmlEl = document.documentElement;

    // Apply saved theme on page load
    try {
        const saved = localStorage.getItem('findin-theme');
        if (saved) {
            htmlEl.setAttribute('data-theme', saved);
            updateThemeIcon(saved);
        } else {
            // Default to dark theme
            htmlEl.setAttribute('data-theme', 'dark');
            updateThemeIcon('dark');
        }
    } catch (e) { /* ignore */ }

    if (themeToggle) {
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const currentTheme = htmlEl.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlEl.setAttribute('data-theme', newTheme);
            try { 
                localStorage.setItem('findin-theme', newTheme); 
            } catch(e) { 
                /* ignore */ 
            }
            updateThemeIcon(newTheme);
            
            // Add animation
            themeToggle.style.transform = 'scale(1.1) rotate(180deg)';
            setTimeout(() => {
                themeToggle.style.transform = '';
            }, 300);
        });
    }

    function updateThemeIcon(theme) {
        const themeIcon = themeToggle ? themeToggle.querySelector('.theme-icon') : null;
        if (themeIcon) {
            themeIcon.className = 'theme-icon fas fa-' + (theme === 'dark' ? 'sun' : 'moon');
        }
    }

    // ===== FORM VALIDATION =====
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    });

    // ===== AUTO-DISMISS ALERTS =====
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // ===== MOBILE NAV TOGGLE =====
    const navToggle = document.getElementById('navToggle');
    const navPanel = document.getElementById('navPanel');
    
    if (navToggle && navPanel) {
        navToggle.addEventListener('click', () => {
            navPanel.classList.toggle('open');
        });

        // Close panel on link click
        navPanel.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navPanel.classList.remove('open');
            });
        });
    }

    // ===== SMOOTH SCROLL FOR ANCHORS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
                }
            }
        });
    });

    // ===== HEADER SHRINK ON SCROLL =====
    const header = document.querySelector('.findin-header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 60) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // ===== SEARCH FUNCTIONALITY =====
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // Implémentation future de la recherche en temps réel
        });
    }

    // ===== MENU MOBILE =====
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            const navMenu = document.querySelector('.nav-menu');
            if (navMenu) {
                navMenu.classList.toggle('show');
            }
        });
    }
});

// ===== UTILITY FUNCTIONS =====

// Toggle filter visibility
function toggleFilters() {
    const filters = document.getElementById('search-filters');
    if (filters) {
        filters.style.display = filters.style.display === 'none' ? 'block' : 'none';
    }
}

// Confirm action dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Update competency level display
function updateLevelDisplay(element) {
    const level = element.value;
    const display = element.nextElementSibling;
    if (display && display.classList.contains('level-display')) {
        display.textContent = `Niveau ${level}`;
        display.className = `level-display level-${level}`;
    }
}
