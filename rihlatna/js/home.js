function toggleDropdown(id) {
    const allDropdowns = ['categories-box', 'hiking-box', 'departure-box'];
    const allMultiselects = document.querySelectorAll('.custom-multiselect');
    const allSelectBoxes = document.querySelectorAll('.select-box');
    
    allDropdowns.forEach(dropdownId => {
        const dropdown = document.getElementById(dropdownId);
        const parentMultiselect = dropdown.parentElement;
        const selectBox = parentMultiselect.querySelector('.select-box');
        
        if (dropdownId !== id) {
            dropdown.style.display = "none";
            dropdown.classList.remove('show');
            parentMultiselect.classList.remove('active');
            selectBox.classList.remove('active');
        }
    });
    
    const clickedDropdown = document.getElementById(id);
    const parentMultiselect = clickedDropdown.parentElement;
    const selectBox = parentMultiselect.querySelector('.select-box');
    
    if (clickedDropdown.style.display === "block") {
        clickedDropdown.style.display = "none";
        clickedDropdown.classList.remove('show');
        parentMultiselect.classList.remove('active');
        selectBox.classList.remove('active');
    } else {
        clickedDropdown.style.display = "block";
        clickedDropdown.classList.add('show');
        parentMultiselect.classList.add('active');
        selectBox.classList.add('active');
    }
}

document.addEventListener("click", function(event) {
    const dropdowns = ['categories-box', 'hiking-box', 'departure-box'];
    dropdowns.forEach(id => {
        const box = document.getElementById(id);
        const parentMultiselect = box.parentElement;
        const selectBox = parentMultiselect.querySelector('.select-box');
        
        if (box && !box.parentElement.contains(event.target)) {
            box.style.display = "none";
            box.classList.remove('show');
            parentMultiselect.classList.remove('active');
            selectBox.classList.remove('active');
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