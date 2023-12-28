(function( $ ) {
	'use strict';

	// On DOM ready
	$(function() {
		const $excerptMetaBox = $('#postexcerpt');

		if($excerptMetaBox) {
			const $generateButton = $('<button class="button" id="ai-generate-excerpt-generate-button">Generate excerpt with AI</button>');

			const $excerptTextArea = $excerptMetaBox.find('textarea');

			$generateButton.on('click', function() {
				this.blur();

				const originalButtonText = $generateButton.text();

				$generateButton.text('Generating...').addClass('disabled');

				jQuery.ajax({
					type: "post",
					dataType: "json",
					url: '/wp-admin/admin-ajax.php',
					data: {action: "ai_generate_excerpt"},
					success: function (response) {
						if (response.type === "success") {
							$excerptTextArea.text(response.excerpt);
						} else {
							alert("Error: " + response.message);
						}
					},
					complete: function() {
						$generateButton.text(originalButtonText).removeClass('disabled');
					}
				});
			
				return false;
			});

			$generateButton.insertAfter($excerptMetaBox.find('textarea'));
		}
	});

})( jQuery );
