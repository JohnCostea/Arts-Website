/**
 * Handles displaying and submitting product reviews
 */

const ReviewSystem = {
    /**
     * Load and display reviews for a product
     * @param {number} productId - The product ID
     * @param {string} containerId - ID of container to display reviews
     */
    loadReviews: function(productId, containerId) {
        fetch(`get_reviews.php?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ReviewSystem.displayReviews(data, containerId, productId);
                } else {
                    console.error('Failed to load reviews:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading reviews:', error);
            });
    },

    /**
     * Display reviews in the specified container
     * @param {object} data - Review data from server
     * @param {string} containerId - Container element ID
     * @param {number} productId - Product ID for submit form
     */
    displayReviews: function(data, containerId, productId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let html = '';

        // Display average rating and count
        if (data.review_count > 0) {
            html += `
                <div class="review-summary">
                    <div class="average-rating">
                        <span class="rating-number">${data.average_rating}</span>
                        <span class="stars">${ReviewSystem.renderStars(data.average_rating)}</span>
                    </div>
                    <p class="review-count">${data.review_count} ${data.review_count === 1 ? 'review' : 'reviews'}</p>
                </div>
            `;
        }

        // Add review form (only if logged in)
        if (isLoggedIn) {
            html += ReviewSystem.renderReviewForm(productId);
        } else {
            html += `
                <div class="review-login-prompt">
                    <p>Please <a href="#" onclick="showLoginModal(); return false;">log in</a> to leave a review.</p>
                </div>
            `;
        }

        // Display individual reviews
        if (data.reviews.length > 0) {
            html += '<div class="reviews-list">';
            html += '<h3>Customer Reviews</h3>';
            
            data.reviews.forEach(review => {
                html += ReviewSystem.renderReview(review);
            });
            
            html += '</div>';
        } else {
            html += '<p class="no-reviews">No reviews yet. Be the first to review this product!</p>';
        }

        container.innerHTML = html;
    },

    /**
     * Render star rating display
     * @param {number} rating - Rating value (1-5, can be decimal)
     * @returns {string} HTML string of stars
     */
    renderStars: function(rating) {
        let stars = '';
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

        // Full stars
        for (let i = 0; i < fullStars; i++) {
            stars += '<span class="star filled">★</span>';
        }

        // Half star
        if (hasHalfStar) {
            stars += '<span class="star half">★</span>';
        }

        // Empty stars
        for (let i = 0; i < emptyStars; i++) {
            stars += '<span class="star empty">☆</span>';
        }

        return stars;
    },

    /**
     * Render clickable star rating input with visual feedback
     * IMPROVED: Shows rating number and better visual feedback
     * @param {string} formId - Form ID for unique star IDs
     * @returns {string} HTML string of interactive stars
     */
    renderStarInput: function(formId) {
        let html = '<div class="star-rating-wrapper">';
        html += '<div class="star-rating-input" data-rating="0">';
        
        // Create stars from 1 to 5 (left to right for better UX)
        for (let i = 1; i <= 5; i++) {
            html += `
                <input type="radio" id="${formId}-star${i}" name="rating" value="${i}" required>
                <label for="${formId}-star${i}" class="star-label" data-rating="${i}" title="${i} star${i > 1 ? 's' : ''}">★</label>
            `;
        }
        
        html += '</div>';
        html += '<span class="rating-label empty">Select a rating</span>';
        html += '</div>';
        
        return html;
    },

    /**
     * Render review form
     * @param {number} productId - Product ID
     * @returns {string} HTML string of review form
     */
    renderReviewForm: function(productId) {
        const formId = `review-form-${productId}`;
        
        return `
            <div class="review-form-container">
                <h3>Write a Review</h3>
                <form id="${formId}" class="review-form">
                    <input type="hidden" name="product_id" value="${productId}">
                    
                    <div class="form-group">
                        <label>Your Rating *</label>
                        ${ReviewSystem.renderStarInput(formId)}
                    </div>
                    
                    <div class="form-group">
                        <label for="${formId}-comment">Your Review *</label>
                        <textarea 
                            id="${formId}-comment"
                            name="comment" 
                            rows="4" 
                            placeholder="Share your experience with this product..."
                            data-validate="required|min:10|max:1000"
                            required></textarea>
                        <small class="char-count">Minimum 10 characters</small>
                    </div>
                    
                    <button type="submit" class="submit-review-btn">Submit Review</button>
                </form>
            </div>
        `;
    },

    /**
     * Render a single review
     * @param {object} review - Review object
     * @returns {string} HTML string of review
     */
    renderReview: function(review) {
        return `
            <div class="review-item">
                <div class="review-header">
                    <div class="review-author">
                        <span class="author-name">${review.user_name}</span>
                        <span class="review-date">${review.formatted_date}</span>
                    </div>
                    <div class="review-rating">
                        ${ReviewSystem.renderStars(review.rating)}
                    </div>
                </div>
                <div class="review-body">
                    <p>${review.comment}</p>
                </div>
            </div>
        `;
    },

    /**
     * Submit a review
     * @param {HTMLFormElement} form - The review form
     */
    submitReview: function(form) {
        const formData = new FormData(form);

        // Client-side validation
        const comment = formData.get('comment');
        const rating = formData.get('rating');

        if (!rating) {
            showToast('Please select a rating', 'error');
            return;
        }

        if (!comment || comment.trim().length < 10) {
            showToast('Review must be at least 10 characters', 'error');
            return;
        }

        if (comment.trim().length > 1000) {
            showToast('Review must be less than 1000 characters', 'error');
            return;
        }

        // Submit to server
        fetch('add_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                
                // Reset form
                form.reset();
                
                // Reload reviews after short delay
                setTimeout(() => {
                    const productId = formData.get('product_id');
                    const containerId = form.closest('[id$="-reviews"]').id;
                    ReviewSystem.loadReviews(productId, containerId);
                }, 1000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error submitting review:', error);
            showToast('Failed to submit review. Please try again.', 'error');
        });
    },

    /**
     * Initialize review forms
     * Sets up event listeners for review submission
     */
    initializeForms: function() {
        // Use event delegation for dynamically added forms
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('review-form')) {
                e.preventDefault();
                ReviewSystem.submitReview(e.target);
            }
        });

        // Character counter for textareas
        document.addEventListener('input', function(e) {
            if (e.target.name === 'comment') {
                const charCount = e.target.value.length;
                const small = e.target.parentElement.querySelector('.char-count');
                if (small) {
                    if (charCount < 10) {
                        small.textContent = `${10 - charCount} more characters needed`;
                        small.style.color = '#dc3545';
                    } else {
                        small.textContent = `${charCount}/1000 characters`;
                        small.style.color = '#28a745';
                    }
                }
            }
        });
        
        // Initialize star rating interactions
        ReviewSystem.initializeStarRating();
    },
    
    /**
     * Initialize star rating interactions
     * Adds hover and click effects for better UX
     */
    initializeStarRating: function() {
        // Handle star hover - show what rating will be selected
        document.addEventListener('mouseover', function(e) {
            if (e.target.classList.contains('star-label')) {
                const rating = e.target.dataset.rating;
                const container = e.target.closest('.star-rating-wrapper');
                const label = container.querySelector('.rating-label');
                
                if (label) {
                    label.textContent = `${rating} out of 5 stars`;
                    label.classList.remove('empty');
                }
                
                // Highlight stars up to hovered star
                const stars = container.querySelectorAll('.star-label');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.style.color = '#ffc107';
                    } else {
                        star.style.color = '#ddd';
                    }
                });
            }
        });
        
        // Handle mouse leave - reset if no rating selected
        document.addEventListener('mouseout', function(e) {
            if (e.target.classList.contains('star-label')) {
                const container = e.target.closest('.star-rating-wrapper');
                const input = container.querySelector('input[name="rating"]:checked');
                const label = container.querySelector('.rating-label');
                const stars = container.querySelectorAll('.star-label');
                
                if (input) {
                    // Show selected rating
                    const selectedRating = input.value;
                    label.textContent = `${selectedRating} out of 5 stars`;
                    label.classList.remove('empty');
                    
                    // Highlight selected stars
                    stars.forEach((star, index) => {
                        if (index < selectedRating) {
                            star.style.color = '#ffc107';
                        } else {
                            star.style.color = '#ddd';
                        }
                    });
                } else {
                    // No rating selected
                    label.textContent = 'Select a rating';
                    label.classList.add('empty');
                    stars.forEach(star => {
                        star.style.color = '#ddd';
                    });
                }
            }
        });
        
        // Handle star click - update label
        document.addEventListener('change', function(e) {
            if (e.target.name === 'rating') {
                const rating = e.target.value;
                const container = e.target.closest('.star-rating-wrapper');
                const label = container.querySelector('.rating-label');
                const stars = container.querySelectorAll('.star-label');
                
                if (label) {
                    label.textContent = `${rating} out of 5 stars`;
                    label.classList.remove('empty');
                }
                
                // Highlight selected stars
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.style.color = '#ffc107';
                    } else {
                        star.style.color = '#ddd';
                    }
                });
            }
        });
    }
};

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    ReviewSystem.initializeForms();

});
