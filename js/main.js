// This is the main JavaScript file for the La La Lush website
// It controls interactive features like cart updates, search, forms, and UI behaviour

// This ensures all JavaScript runs only after the entire webpage has loaded
document.addEventListener('DOMContentLoaded', function () {

    // This function creates pop-up messages (alerts) on the screen
    // It is used to show success or error messages to the user
    function showAlert(message, type) {

        // Create a new HTML element for the alert box
        const alertDiv = document.createElement('div');

        // Add CSS classes to style the alert (success or error)
        alertDiv.className = 'alert alert-' + type;

        // Set the message text inside the alert box
        alertDiv.textContent = message;

        // Find the main page area or fallback to body if not found
        const mainContent = document.querySelector('.main-content') || document.body;

        // Insert the alert at the top of the page
        mainContent.insertBefore(alertDiv, mainContent.firstChild);

        // Automatically remove the alert after 4 seconds
        setTimeout(function () {
            alertDiv.remove();
        }, 4000);
    }

    // CART FUNCTIONALITY
    // This section controls the plus and minus buttons for product quantity
    const qtyButtons = document.querySelectorAll('.qty-btn');

    qtyButtons.forEach(function (btn) {

        btn.addEventListener('click', function () {

            // Get the quantity input field next to the button
            const input = this.parentElement.querySelector('.qty-input');

            // Convert current value to a number
            let currentVal = parseInt(input.value);

            // If plus button clicked, increase quantity
            if (this.dataset.action === 'increase') {
                input.value = currentVal + 1;
            }

            // If minus button clicked, decrease quantity but not below 1
            else if (this.dataset.action === 'decrease' && currentVal > 1) {
                input.value = currentVal - 1;
            }

            // Update the price for this product row
            updateLineTotal(this);
        });
    });

    // This function recalculates the total price for one product row
    function updateLineTotal(triggerBtn) {

        // Find the table row of the product
        const row = triggerBtn.closest('tr');
        if (!row) return;

        // Get unit price stored in the row attribute
        const unitPrice = parseFloat(row.dataset.price);

        // Get current quantity value
        const qty = parseInt(row.querySelector('.qty-input').value);

        // Calculate total price for this row
        const lineTotal = unitPrice * qty;

        // Update the displayed total price in the row
        const lineTotalCell = row.querySelector('.line-total');

        if (lineTotalCell) {
            lineTotalCell.textContent = 'R ' + lineTotal.toFixed(2);
        }

        // Update overall cart total
        updateGrandTotal();
    }

    // This function calculates the total price of all cart items combined
    function updateGrandTotal() {

        let grand = 0;

        // Loop through all product totals in the cart
        document.querySelectorAll('.line-total').forEach(function (cell) {
            grand += parseFloat(cell.textContent.replace('R ', ''));
        });

        // Display final total on page
        const grandTotalEl = document.getElementById('grand-total');

        if (grandTotalEl) {
            grandTotalEl.textContent = 'R ' + grand.toFixed(2);
        }
    }

    // REGISTER FORM VALIDATION
    // This checks if user inputs correct password details before submitting
    const registerForm = document.getElementById('register-form');

    if (registerForm) {

        registerForm.addEventListener('submit', function (e) {

            // Get password fields
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            // Stop form if passwords do not match
            if (pass !== confirm) {
                e.preventDefault();
                showAlert('Passwords do not match. Please try again.', 'error');
            }

            // Stop form if password is too short
            if (pass.length < 8) {
                e.preventDefault();
                showAlert('Password must be at least 8 characters long.', 'error');
            }
        });
    }

    // PRODUCT SEARCH FUNCTION
    // Filters products as the user types in the search bar
    const searchInput = document.getElementById('product-search');

    if (searchInput) {

        searchInput.addEventListener('keyup', function () {

            const query = this.value.toLowerCase();

            const cards = document.querySelectorAll('.product-card');

            cards.forEach(function (card) {

                // Get product name from each card
                const name = card.querySelector('h3').textContent.toLowerCase();

                // Show or hide product based on search match
                if (name.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // PAYMENT METHOD FUNCTION
    // Shows EFT banking details only when EFT is selected
    const paymentSelect = document.getElementById('payment_method');

    if (paymentSelect) {

        paymentSelect.addEventListener('change', function () {

            const eftDetails = document.getElementById('eft-details');

            if (eftDetails) {
                eftDetails.style.display = this.value === 'eft' ? 'block' : 'none';
            }
        });
    }

    // IMAGE PREVIEW FUNCTION
    // Shows preview of product image before uploading
    const imageInput = document.getElementById('product_image');

    if (imageInput) {

        imageInput.addEventListener('change', function () {

            const preview = document.getElementById('image-preview');

            if (preview && this.files && this.files[0]) {

                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // MOBILE MENU FUNCTION
    // Opens and closes navigation menu on mobile devices
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');

    if (hamburger && navMenu) {

        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }
});