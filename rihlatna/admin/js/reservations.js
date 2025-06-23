const modal = document.getElementById('detailsModal');
const closeBtn = document.querySelector('.close-modal');
const detailsContainer = document.getElementById('reservationDetails');

document.querySelectorAll('.view-details-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const reservationId = this.getAttribute('data-id');

        fetch(`../actions/get_reservation_details.php?id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    detailsContainer.innerHTML = `<p>${data.error}</p>`;
                } else {
                    detailsContainer.innerHTML = `
                        <h3>Personal Information</h3>
                        <p><strong>Name:</strong> ${data.first_name} ${data.last_name}</p>
                        <p><strong>Gender:</strong> ${data.gender}</p>
                        <p><strong>Birthday:</strong> ${data.birthday}</p>
                        <p><strong>CIN:</strong> ${data.cin}</p>
                        <p><strong>City:</strong> ${data.city}</p>
                        <p><strong>Phone:</strong> ${data.phone}</p>
                        <p><strong>Email:</strong> ${data.email}</p>
                        <p><strong>First Experience:</strong> ${data.first_experience ? 'Yes' : 'No'}</p>
                        
                        <h3>Trip Information</h3>
                        <p><strong>Trip:</strong> ${data.trip_title}</p>
                        <p><strong>Dates:</strong> ${data.trip_start_date} to ${data.trip_end_date}</p>
                        <p><strong>Price:</strong> ${data.price} Dhs</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${data.status}">${data.status}</span></p>
                    `;
                }
                modal.style.display = 'block';
            })
            .catch(error => {
                detailsContainer.innerHTML = '<p>Error loading reservation details</p>';
                modal.style.display = 'block';
            });
    });
});

closeBtn.addEventListener('click', function() {
    modal.style.display = 'none';
});

window.addEventListener('click', function(event) {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});