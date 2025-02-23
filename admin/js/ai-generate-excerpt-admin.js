(function( $ ) {
	'use strict';

	// On DOM ready
	$(function() {
		const $excerptMetaBox = $('#postexcerpt');

		if ($excerptMetaBox.length) {
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
					data: { action: "ai_generate_excerpt" },
					success: function(response) {
						if (response.type === "success") {
							$excerptTextArea.val(htmlDecode(response.excerpt));
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

			$generateButton.insertAfter($excerptTextArea);
		}

		// Gutenberg Integration
		if (typeof wp !== 'undefined' && wp.data) {
			wp.domReady(() => {
				const el = wp.element.createElement;
				const registerPlugin = wp.plugins.registerPlugin;
				const PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
				const withSelect = wp.data.withSelect;
				const withDispatch = wp.data.withDispatch;
				const compose = wp.compose.compose;

				const AIExcerptButton = compose(
					withSelect((select) => ({
						excerpt: select('core/editor').getEditedPostAttribute('excerpt')
					})),
					withDispatch((dispatch) => ({
						setExcerpt: (excerpt) => dispatch('core/editor').editPost({ excerpt })
					}))
				)(({ excerpt, setExcerpt }) => {
					const handleClick = () => {
						wp.apiFetch({
							path: '/wp-admin/admin-ajax.php',
							method: 'POST',
							data: { action: 'ai_generate_excerpt' }
						}).then(response => {
							if (response.type === "success") {
								setExcerpt(response.excerpt);
							} else {
								alert("Error: " + response.message);
							}
						});
					};

					return el(
						PluginDocumentSettingPanel,
						{ title: 'AI Generate Excerpt', initialOpen: true },
						el('button', { className: 'button', onClick: handleClick }, 'Generate Excerpt with AI')
					);
				});

				registerPlugin('ai-generate-excerpt', { render: AIExcerptButton });
			});
		}

	});

})( jQuery );
