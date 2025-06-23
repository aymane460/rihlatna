
  

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
        });