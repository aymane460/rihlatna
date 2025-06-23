// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            if (this.value && endDateInput.value && this.value > endDateInput.value) {
                endDateInput.value = '';
            }
            endDateInput.min = this.value;
        });
        
        endDateInput.addEventListener('change', function() {
            if (this.value && startDateInput.value && this.value < startDateInput.value) {
                alert('End date must be after start date');
                this.value = '';
            }
        });
    }
    
    // Add new day
    document.getElementById('addDay').addEventListener('click', function() {
        const daysContainer = document.getElementById('daysContainer');
        const dayCount = document.querySelectorAll('.day-section').length;
        
        const newDay = document.createElement('div');
        newDay.className = 'day-section';
        newDay.dataset.dayIndex = dayCount;
        newDay.innerHTML = `
            <h4>Day ${dayCount + 1}</h4>
            <input type="hidden" name="day_id[]" value="">
            
            <div class="form-group">
                <label>Day Number</label>
                <input type="number" name="day_number[]" min="1" value="${dayCount + 1}">
                <p class="error"></p>
            </div>
            
            <div class="form-group">
                <label>Day Title</label>
                <input type="text" name="day_title[]" value="">
                <p class="error"></p>
            </div>
            
            <div class="activities-container">
                <div class="activity">
                    <input type="hidden" name="activity_id[${dayCount}][]" value="">
                    
                    <div class="form-group">
                        <label>Activity Content</label>
                        <textarea name="activity_content[${dayCount}][]"></textarea>
                        <p class="error"></p>
                    </div>
                    
                    <div class="form-group">
                        <label>Activity Order</label>
                        <input type="number" name="activity_order[${dayCount}][]" min="1" value="1">
                        <p class="error"></p>
                    </div>
                    
                    <button type="button" class="remove-activity">Remove Activity</button>
                </div>
            </div>
            
            <button type="button" class="add-activity">Add Activity</button>
            <button type="button" class="remove-day">Remove Day</button>
        `;
        
        daysContainer.appendChild(newDay);
    });
    
    // Add new activity
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-activity')) {
            const daySection = e.target.closest('.day-section');
            const dayIndex = daySection.dataset.dayIndex;
            const activitiesContainer = daySection.querySelector('.activities-container');
            const activityCount = activitiesContainer.querySelectorAll('.activity').length;
            
            const newActivity = document.createElement('div');
            newActivity.className = 'activity';
            newActivity.innerHTML = `
                <input type="hidden" name="activity_id[${dayIndex}][]" value="">
                
                <div class="form-group">
                    <label>Activity Content</label>
                    <textarea name="activity_content[${dayIndex}][]"></textarea>
                    <p class="error"></p>
                </div>
                
                <div class="form-group">
                    <label>Activity Order</label>
                    <input type="number" name="activity_order[${dayIndex}][]" min="1" value="${activityCount + 1}">
                    <p class="error"></p>
                </div>
                
                <button type="button" class="remove-activity">Remove Activity</button>
            `;
            
            activitiesContainer.appendChild(newActivity);
        }
        
        // Remove activity
        if (e.target.classList.contains('remove-activity')) {
            const activity = e.target.closest('.activity');
            const activitiesContainer = activity.parentElement;
            
            if (activitiesContainer.querySelectorAll('.activity').length > 1) {
                activity.remove();
                
                // Update order numbers
                activitiesContainer.querySelectorAll('.activity').forEach((act, index) => {
                    act.querySelector('input[name*="activity_order"]').value = index + 1;
                });
            } else {
                alert('You must have at least one activity per day.');
            }
        }
        
        // Remove day
        if (e.target.classList.contains('remove-day')) {
            const daysContainer = document.getElementById('daysContainer');
            if (daysContainer.querySelectorAll('.day-section').length > 1) {
                e.target.closest('.day-section').remove();
                
                // Reindex remaining days
                document.querySelectorAll('.day-section').forEach((day, index) => {
                    day.dataset.dayIndex = index;
                    day.querySelector('h4').textContent = `Day ${index + 1}`;
                    
                    // Update day fields
                    day.querySelectorAll('[name^="day_number"]').forEach(field => {
                        field.name = `day_number[]`;
                    });
                    day.querySelectorAll('[name^="day_title"]').forEach(field => {
                        field.name = `day_title[]`;
                    });
                    day.querySelectorAll('[name^="day_id"]').forEach(field => {
                        field.name = `day_id[]`;
                    });
                    
                    // Reindex activities
                    day.querySelectorAll('.activity').forEach((activity, activityIndex) => {
                        activity.querySelectorAll('[name^="activity_content"]').forEach(field => {
                            field.name = `activity_content[${index}][]`;
                        });
                        activity.querySelectorAll('[name^="activity_order"]').forEach(field => {
                            field.name = `activity_order[${index}][]`;
                        });
                        activity.querySelectorAll('[name^="activity_id"]').forEach(field => {
                            field.name = `activity_id[${index}][]`;
                        });
                    });
                });
            } else {
                alert('You must have at least one day.');
            }
        }
    });
});