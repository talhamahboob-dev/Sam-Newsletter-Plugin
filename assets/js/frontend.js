/**
 * Frontend JavaScript for SAM Newsletter Form
 * Handles form validation and AJAX submission
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        /**
         * Form submission handler
         */
        $('.sam-newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('.sam-newsletter-submit');
            const $message = $form.find('.sam-newsletter-message');
            
            // Clear previous errors and messages
            $form.find('.sam-newsletter-error').text('').hide();
            $message.removeClass('success error').text('').hide();
            
            // Get form data
            const name = $form.find('input[name="name"]').val().trim();
            const email = $form.find('input[name="email"]').val().trim();
            const nonce = $form.data('nonce');
            
            // Client-side validation
            let isValid = true;
            
            if (name === '') {
                showError($form, 'name', 'Name is required.');
                isValid = false;
            } else if (name.length < 2) {
                showError($form, 'name', 'Name must be at least 2 characters.');
                isValid = false;
            } else if (name.length > 255) {
                showError($form, 'name', 'Name is too long.');
                isValid = false;
            }
            
            if (email === '') {
                showError($form, 'email', 'Email is required.');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showError($form, 'email', 'Please enter a valid email address.');
                isValid = false;
            }
            
            if (!isValid) {
                return;
            }
            
            // Disable submit button
            $submitBtn.prop('disabled', true).text('Subscribing...');
            
            // AJAX request
            $.ajax({
                url: samNewsletterData.ajax_url,
                type: 'POST',
                data: {
                    action: 'sam_newsletter_subscribe',
                    nonce: nonce,
                    name: name,
                    email: email
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $message
                            .addClass('success')
                            .html('<span class="dashicons dashicons-yes-alt"></span> ' + response.data.message)
                            .fadeIn();
                        
                        // Reset form
                        $form[0].reset();
                        
                        // Hide success message after 5 seconds
                        setTimeout(function() {
                            $message.fadeOut();
                        }, 5000);
                    } else {
                        // Show error message
                        $message
                            .addClass('error')
                            .html('<span class="dashicons dashicons-warning"></span> ' + response.data.message)
                            .fadeIn();
                        
                        // Show field-specific errors
                        if (response.data.errors) {
                            $.each(response.data.errors, function(field, error) {
                                showError($form, field, error);
                            });
                        }
                    }
                },
                error: function() {
                    $message
                        .addClass('error')
                        .html('<span class="dashicons dashicons-warning"></span> An unexpected error occurred. Please try again.')
                        .fadeIn();
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).text('Subscribe');
                }
            });
        });
        
        /**
         * Real-time validation on input blur
         */
        $('.sam-newsletter-form input[name="name"]').on('blur', function() {
            const $input = $(this);
            const $form = $input.closest('.sam-newsletter-form');
            const value = $input.val().trim();
            
            if (value !== '') {
                if (value.length < 2) {
                    showError($form, 'name', 'Name must be at least 2 characters.');
                } else if (value.length > 255) {
                    showError($form, 'name', 'Name is too long.');
                } else {
                    clearError($form, 'name');
                }
            }
        });
        
        $('.sam-newsletter-form input[name="email"]').on('blur', function() {
            const $input = $(this);
            const $form = $input.closest('.sam-newsletter-form');
            const value = $input.val().trim();
            
            if (value !== '' && !isValidEmail(value)) {
                showError($form, 'email', 'Please enter a valid email address.');
            } else if (value !== '') {
                clearError($form, 'email');
            }
        });
        
        /**
         * Helper function to show error message
         */
        function showError($form, field, message) {
            const $errorSpan = $form.find('.sam-newsletter-error[data-field="' + field + '"]');
            $errorSpan.text(message).fadeIn();
            $form.find('input[name="' + field + '"]').addClass('error');
        }
        
        /**
         * Helper function to clear error message
         */
        function clearError($form, field) {
            const $errorSpan = $form.find('.sam-newsletter-error[data-field="' + field + '"]');
            $errorSpan.text('').hide();
            $form.find('input[name="' + field + '"]').removeClass('error');
        }
        
        /**
         * Email validation function
         */
        function isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }
        
    });
    
})(jQuery);