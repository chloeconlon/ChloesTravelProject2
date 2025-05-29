let destinations = [];

function fetchDestinations() {
    return fetch('get_destinations.php')
        .then(res => res.json())
        .then(data => {
            destinations = data;
            return data;
        });
}

function showSuggestions(inputElem, suggestionsElem, typedVal) {
    const val = typedVal.toLowerCase();
    let matches = destinations.filter(d => d.name.toLowerCase().includes(val));
    let suggestions = '';
    matches.forEach(dest => {
        suggestions += `<div class="autocomplete-suggestion" data-id="${dest.destination_id}">${dest.name}</div>`;
    });
    suggestionsElem.innerHTML = suggestions;
    suggestionsElem.style.display = suggestions ? 'block' : 'none';
}

function closeSuggestions(suggestionsElem) {
    suggestionsElem.innerHTML = '';
    suggestionsElem.style.display = 'none';
}

// --- Review Form Star Rating ---
function setupStarRating(ratingInputId, ratingDivId) {
    const starRating = document.getElementById(ratingDivId);
    const ratingInput = document.getElementById(ratingInputId);
    let selectedRating = 0;

    function setStars(rating) {
        Array.from(starRating.children).forEach(star => {
            if (parseInt(star.dataset.value) <= rating) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }

    Array.from(starRating.children).forEach(star => {
        star.addEventListener('mouseover', function () {
            setStars(parseInt(this.dataset.value));
        });
        star.addEventListener('mouseout', function () {
            setStars(selectedRating);
        });
        star.addEventListener('click', function () {
            selectedRating = parseInt(this.dataset.value);
            ratingInput.value = selectedRating;
            setStars(selectedRating);
        });
    });
}

// --- Reviews Fetch/Display ---
function fetchReviews(destinationName = '') {
    let url = 'get_reviews.php';
    if (destinationName) {
        url += '?destination_name=' + encodeURIComponent(destinationName);
    }
    fetch(url)
        .then(res => res.json())
        .then(displayReviews);
}

function displayReviews(reviews) {
    const reviewsList = document.getElementById('reviewsList');
    reviewsList.innerHTML = '';
    if (!reviews.length) {
        reviewsList.innerHTML = '<p>No reviews found for this destination.</p>';
        return;
    }
    reviews.forEach(review => {
        reviewsList.innerHTML += `
            <div class="review-card">
                <h3>${review.destination_name} <span style="font-weight:400;color:#555;font-size:0.98em;">by User #${review.user_id}</span></h3>
                <div class="rating">${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}</div>
                <p>${review.comment}</p>
                <small>Reviewed on: ${new Date(review.created_at).toLocaleString()}</small>
            </div>
        `;
    });
}
// Fetch destinations first
document.addEventListener('DOMContentLoaded', function () {

    fetchDestinations().then(() => {
        // Review Form Autocomplete
        const destinationInput = document.getElementById('destinationInput');
        const destinationSuggestions = document.getElementById('destinationSuggestions');
        destinationInput.addEventListener('input', function () {
            showSuggestions(destinationInput, destinationSuggestions, this.value);
        });
        destinationSuggestions.addEventListener('click', function (e) {
            if (e.target.classList.contains('autocomplete-suggestion')) {
                destinationInput.value = e.target.textContent;
                closeSuggestions(destinationSuggestions);
            }
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.form-group')) {
                closeSuggestions(destinationSuggestions);
            }
        });

        // Filter Autocomplete
        const filterInput = document.getElementById('destinationFilter');
        const filterSuggestions = document.getElementById('filterSuggestions');
        filterInput.addEventListener('input', function () {
            showSuggestions(filterInput, filterSuggestions, this.value);
        });
        filterSuggestions.addEventListener('click', function (e) {
            if (e.target.classList.contains('autocomplete-suggestion')) {
                filterInput.value = e.target.textContent;
                closeSuggestions(filterSuggestions);
            }
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.filter-form')) {
                closeSuggestions(filterSuggestions);
            }
        });

        // Filter
        document.getElementById('filterButton').addEventListener('click', function () {
            const val = filterInput.value.trim();
            if (val) {
                fetchReviews(val);
                document.getElementById('clearFilter').style.display = 'inline-block';
            } else {
                fetchReviews();
                document.getElementById('clearFilter').style.display = 'none';
            }
        });
        document.getElementById('clearFilter').addEventListener('click', function () {
            filterInput.value = '';
            this.style.display = 'none';
            fetchReviews();
        });

        // Star Rating
        setupStarRating('rating', 'starRating');

        // Review Form Submission
        document.getElementById('reviewForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const destinationName = destinationInput.value.trim();
            const rating = document.getElementById('rating').value;
            const comment = document.getElementById('comment').value.trim();
            const reviewMsg = document.getElementById('reviewMsg');

            if (!destinationName || !rating || !comment) {
                reviewMsg.style.color = 'red';
                reviewMsg.textContent = 'Please fill out all fields.';
                return;
            }

            const formData = new URLSearchParams();
            formData.append('destination', destinationName);
            formData.append('rating', rating);
            formData.append('comment', comment);

            fetch('submit_review.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reviewMsg.style.color = '#2e7d32';
                        reviewMsg.textContent = 'Review submitted successfully!';
                        fetchReviews();
                        // Refresh destinations for autocomplete/filter
                        fetchDestinations();
                        document.getElementById('reviewForm').reset();
                        // Clear stars
                        setupStarRating('rating', 'starRating');
                    } else {
                        reviewMsg.style.color = 'red';
                        reviewMsg.textContent = data.error || 'Error submitting review.';
                    }
                })
                .catch(() => {
                    reviewMsg.style.color = 'red';
                    reviewMsg.textContent = 'Error submitting review.';
                });
        });


        fetchReviews();
    });
});