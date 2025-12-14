$(document).ready(function() {
            
    //PLAN SUBSCRIPTION
    $('.btn-subscribe-plan').on('click', function() {
        const planId = $(this).data('plan-id');
        const planCard = $(this).closest('.plan-card');
        const planName = planCard.data('plan-name');
        const planPrice = planCard.data('plan-price');
        
        // Populate modal
        $('#modalPlanName').text(planName);
        $('#modalPlanPrice').text('₱' + planPrice.toLocaleString());
        $('#modal_plan_id').val(planId);
        
        // Show modal
        $('#subscriptionModal').addClass('show');
        $('body').css('overflow', 'hidden');
    });

    // Close modal - X button
    $('.modal-close').on('click', function() {
        closeSubscriptionModal();
    });

    // Close modal - Cancel button
    $('.modal-cancel').on('click', function() {
        closeSubscriptionModal();
    });

    // Close modal when clicking backdrop
    $('#subscriptionModal').on('click', function(e) {
        if ($(e.target).is('#subscriptionModal')) {
            closeSubscriptionModal();
        }
    });

    //  FAQ TOGGLE 
    $('.faq-toggle').on('click', function() {
        const faqItem = $(this).closest('.faq-item');
        const icon = $(this).find('.faq-icon');

        // Toggle active class
        faqItem.toggleClass('active');

        // Rotate icon
        if (faqItem.hasClass('active')) {
            icon.css('transform', 'rotate(180deg)');
        } else {
            icon.css('transform', 'rotate(0deg)');
        }
    });

    //  BILLING TOGGLE 
    $('#billingToggle').on('click', function() {
        const slider = $(this).find('.toggle-slider');
        
        if ($(this).hasClass('active')) {
            // Switch to monthly
            $(this).removeClass('active bg-blue-600').addClass('bg-gray-600');
            slider.css('transform', 'translateX(0)');
            updatePrices('monthly');
        } else {
            // Switch to annual
            $(this).addClass('active bg-blue-600').removeClass('bg-gray-600');
            slider.css('transform', 'translateX(32px)');
            updatePrices('annual');
        }
    });

    //  SCROLL TO PLANS 
    $('.scroll-to-plans').on('click', function() {
        $('html, body').animate({
            scrollTop: $('.plans-container').offset().top - 100
        }, 800);
    });

    //  HELPER FUNCTIONS 
    function closeSubscriptionModal() {
        $('#subscriptionModal').removeClass('show');
        $('body').css('overflow', 'auto');
        // Reset form
        $('#subscriptionForm')[0].reset();
        $('#formMessage').addClass('hidden');
    }

    function showFormMessage(message, type) {
        const messageDiv = $('#formMessage');
        const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';
        
        messageDiv.html(`
            <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                ${message}
            </div>
        `).removeClass('hidden');

        if (type !== 'error') {
            setTimeout(() => {
                messageDiv.addClass('hidden');
            }, 3000);
        }
    }

    function updatePrices(billingType) {
        // Update prices based on billing type
        $('.plan-card').each(function() {
            const priceElement = $(this).find('.price-value');
            let price = parseInt(priceElement.text().replace(/,/g, ''));
            
            if (billingType === 'annual') {
                price = Math.round(price * 12 * 0.8); // 20% discount
                priceElement.text(price.toLocaleString());
            } else {
                
                priceElement.text(price.toLocaleString());
            }
        });
    }

    // Show alerts
    function showAlert(message, type = 'info') {
        const alertClass = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        }[type] || 'bg-blue-500';

        const alert = $(`
            <div class="alert ${alertClass} text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between">
                <span>${message}</span>
                <button class="text-white hover:text-gray-200 ml-4">&times;</button>
            </div>
        `);

        alert.find('button').on('click', function() {
            alert.slideUp(300, function() {
                $(this).remove();
            });
        });

        $('#alertContainer').append(alert);

        if (type !== 'error') {
            setTimeout(() => {
                alert.slideUp(300, function() {
                    $(this).remove();
                });
            }, 4000);
        }
    }
});