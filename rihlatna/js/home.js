
function toggleDropdown(id) {
    const box = document.getElementById(id);
    box.style.display = box.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function(event) {
    const dropdowns = ['categories-box', 'hiking-box', 'departure-box'];
    dropdowns.forEach(id => {
        const box = document.getElementById(id);
        if (box && !box.parentElement.contains(event.target)) {
            box.style.display = "none";
        }
    });
});

function scrollTrips(direction) {
    const container = document.getElementById('trips-container');
    const scrollAmount = 300;
    container.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}
function resetFilters() {

    document.querySelectorAll('.checkboxes input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.querySelector('form').submit();
}