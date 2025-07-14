// JavaScript untuk mengelola inline editing nilai
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('nilai-form');
    const submitBtn = document.getElementById('save-btn');
    const resetBtn = document.getElementById('reset-all-btn');
    const inputs = form.querySelectorAll('input[type="number"]');

    // Track changes
    let hasChanges = false;

    // Store original values
    const originalValues = new Map();
    inputs.forEach(input => {
        originalValues.set(input.name, input.value);
    });

    // Listen for changes
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            checkForChanges();
        });
    });

    function checkForChanges() {
        hasChanges = false;
        inputs.forEach(input => {
            if (input.value !== originalValues.get(input.name)) {
                hasChanges = true;
            }
        });

        // Update button states
        submitBtn.disabled = !hasChanges;
        resetBtn.disabled = !hasChanges;

        if (hasChanges) {
            submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            resetBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
            resetBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
        } else {
            submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            resetBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
            resetBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        }
    }

    // Reset button functionality
    resetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (hasChanges) {
            inputs.forEach(input => {
                input.value = originalValues.get(input.name);
            });
            checkForChanges();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        if (!hasChanges) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;
    });

    // Initialize button states
    checkForChanges();

    // Validate numeric inputs
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = parseFloat(this.value);
            if (isNaN(value) || value < 0) {
                this.value = '';
            } else if (value > 100) {
                this.value = '100';
            }
        });
    });
});
