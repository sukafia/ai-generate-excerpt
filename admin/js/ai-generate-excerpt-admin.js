(function( $ ) {
	'use strict';

	// On DOM ready
	$(function() {
		const $excerptMetaBox = $('#postexcerpt');

		if($excerptMetaBox) {
			const $generateButton = $('<button class="button" id="ai-generate-excerpt-generate-button">Generate excerpt with AI</button>');

			const $excerptTextArea = $excerptMetaBox.find('textarea');

			$generateButton.on('click', function() {


				function htmlDecode(input) {
					var doc = new DOMParser().parseFromString(input, "text/html");
					return doc.documentElement.textContent;
				}

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
							$excerptTextArea[0].value = htmlDecode(response.excerpt);
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
