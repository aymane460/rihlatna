document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-stars .star');
    const ratingInput = document.getElementById('rating-value');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            ratingInput.value = value;
            
            stars.forEach(s => {
                s.classList.remove('active');
                if (parseInt(s.getAttribute('data-value')) <= value) {
                    s.classList.add('active');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const hoverValue = parseInt(this.getAttribute('data-value'));
            
            stars.forEach(s => {
                s.classList.remove('hovered');
                if (parseInt(s.getAttribute('data-value')) <= hoverValue) {
                    s.classList.add('hovered');
                }
            });
        });
        
        star.addEventListener('mouseout', function() {
            stars.forEach(s => s.classList.remove('hovered'));
        });
    });

    const reservationForm = document.querySelector('.reservation-form');
    if (reservationForm) {
        reservationForm.addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('phone');
            const cinInput = document.getElementById('cin');

            if (!/^0[5-7][0-9]{8}$/.test(phoneInput.value.trim())) {
                e.preventDefault();
                alert('Please enter a valid Moroccan phone number (e.g., 0612345678)');
                phoneInput.focus();
                return false;
            }
            
            if (!/^[A-Z]{1,2}[0-9]{6,7}$/.test(cinInput.value.trim())) {
                e.preventDefault();
                alert('Please enter a valid CIN (e.g., AB123456 or A1234567)');
                cinInput.focus();
                return false;
            }
            
            return true;
        });
    }
});