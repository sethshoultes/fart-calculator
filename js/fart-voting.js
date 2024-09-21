jQuery(document).ready(function($) {
    $('.fc-vote-button').on('click', function() {
        var jokeId = $(this).data('joke-id');
        var voteType = $(this).data('vote-type');

        $.ajax({
            url: fc_ajax_object.ajax_url, // Use the localized AJAX URL
            type: 'POST',
            data: {
                action: 'fc_fart_joke_vote', // This matches the PHP action hook
                fart_id: jokeId,
                vote_type: voteType,
                fc_fart_joke_vote_nonce: fc_ajax_object.fc_ajax_nonce // Use the localized nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data); // Optional: Alert user about successful vote
                    location.reload(); // Optionally, reload the page to update the vote counts
                } else {
                    alert(response.data); // Display the error message returned by the server
                }
            }
        });
    });
});
