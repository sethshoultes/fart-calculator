jQuery(document).ready(function($) {
    $('.fc-vote-button').on('click', function() {
        var detailId = $(this).data('detail-id');  // For fart details
        var jokeId = $(this).data('joke-id');      // For fart jokes
        var voteType = $(this).data('vote-type');  // Vote type (upvote or downvote)

        // Log for debugging
        console.log('Detail ID:', detailId);
        console.log('Joke ID:', jokeId);
        console.log('Vote Type:', voteType);

        var action, id, nonce;
        
        if (detailId) {
            // Voting on fart details
            action = 'fc_fart_detail_vote';
            id = detailId;
            nonce = fc_ajax_object.fc_ajax_nonce;  // Fart details nonce
        } else if (jokeId) {
            // Voting on fart jokes
            action = 'fc_fart_joke_vote';
            id = jokeId;
            nonce = fc_ajax_object.fc_ajax_nonce;  // Fart jokes nonce
        } else {
            // If neither ID is found, exit
            console.error('No valid fart detail or joke ID found.');
            return;
        }

        // Proceed with the AJAX call
        $.ajax({
            url: fc_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: action,
                fart_id: id,                // Either detailId or jokeId
                vote_type: voteType,
                nonce: nonce                // Correct nonce for the action
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);    // Notify the user of a successful vote
                    location.reload();       // Optionally reload to update vote count
                } else {
                    alert(response.data);    // Show error message if vote failed
                }
            }
        });
    });
});
