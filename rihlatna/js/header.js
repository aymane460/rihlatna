document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userIcon && userDropdown) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show-dropdown');
        });
        
        document.addEventListener('click', function() {
            if (userDropdown.classList.contains('show-dropdown')) {
                userDropdown.classList.remove('show-dropdown');
            }
        });
    }
    
    const modal = document.getElementById('authModal');
    const btn = document.getElementById('userIcon');
    const span = document.getElementById('closeModal');
    
    if (btn && modal && span) {
        btn.addEventListener('click', function() {
            if (!document.getElementById('userDropdown')) {
                modal.style.display = "block";
            }
        });
        
        span.addEventListener('click', function() {
            modal.style.display = "none";
        });
        
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    }
    
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    
    if (loginTab && signupTab && loginForm && signupForm) {
        loginTab.addEventListener('click', function() {
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
            loginForm.style.display = "block";
            signupForm.style.display = "none";
        });
        
        signupTab.addEventListener('click', function() {
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
            signupForm.style.display = "block";
            loginForm.style.display = "none";
        });
    }

    const burgerMenu = document.getElementById('burgerMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const closeMenu = document.getElementById('closeMenu');
    const mobileAuthTrigger = document.getElementById('mobileAuthTrigger');
    
    if (burgerMenu && mobileMenuOverlay) {
        burgerMenu.addEventListener('click', function() {
            burgerMenu.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.style.overflow = mobileMenuOverlay.classList.contains('active') ? 'hidden' : '';
        });
    }
    
    if (closeMenu && mobileMenuOverlay) {
        closeMenu.addEventListener('click', function() {
            burgerMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function(e) {
            if (e.target === mobileMenuOverlay) {
                burgerMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
    
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-links a, .mobile-icon-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            burgerMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    const mobileAuthDropdown = document.getElementById('mobileAuthDropdown');
    if (mobileAuthTrigger && mobileAuthDropdown) {
        mobileAuthTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileAuthDropdown.classList.toggle('active');
        });
    }
    
    const mobileLoginTab = document.getElementById('mobileLoginTab');
    const mobileSignupTab = document.getElementById('mobileSignupTab');
    const mobileLoginForm = document.getElementById('mobileLoginForm');
    const mobileSignupForm = document.getElementById('mobileSignupForm');
    
    if (mobileLoginTab && mobileSignupTab && mobileLoginForm && mobileSignupForm) {
        mobileLoginTab.addEventListener('click', function() {
            mobileLoginTab.classList.add('active');
            mobileSignupTab.classList.remove('active');
            mobileLoginForm.style.display = "flex";
            mobileSignupForm.style.display = "none";
        });
        
        mobileSignupTab.addEventListener('click', function() {
            mobileSignupTab.classList.add('active');
            mobileLoginTab.classList.remove('active');
            mobileSignupForm.style.display = "flex";
            mobileLoginForm.style.display = "none";
        });
    }
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            burgerMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});