(function($) {
    'use strict';

    // On DOM ready
    $(function() {
        const $excerptMetaBox = $('#postexcerpt');

        // Classic Editor Integration
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

                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: aiGenerateExcerptData.ajax_url, // Use localized AJAX URL
                    data: {
                        action: "ai_generate_excerpt",
                        nonce: aiGenerateExcerptData.nonce // Add nonce for security
                    },
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
        if (typeof wp !== 'undefined' && wp.data && wp.plugins && wp.editPost) {
            const { PluginDocumentSettingPanel } = wp.editPost;
            const { Button } = wp.components;
            const { useState } = wp.element;
            const { useDispatch, useSelect } = wp.data;
            const { __ } = wp.i18n;
            const { apiFetch } = wp;

            const AIGenerateExcerptButton = () => {
                const [isLoading, setIsLoading] = useState(false);
                const postId = useSelect((select) => select('core/editor').getCurrentPostId(), []);
                const postContent = useSelect((select) => select('core/editor').getEditedPostAttribute('content'), []);
                const { editPost } = useDispatch('core/editor');

                const handleGenerateExcerpt = async () => {
                    setIsLoading(true);

                    try {
                        const response = await apiFetch({
                            path: '/ai-generate-excerpt/v1/generate', // Use REST API endpoint
                            method: 'POST',
                            data: {
                                post_id: postId,
                                post_content: postContent,
                            },
                        });

                        if (response.success) {
                            editPost({ excerpt: response.data.excerpt });
                        } else {
                            alert("Error: " + response.data.message);
                        }
                    } catch (error) {
                        console.error('Error generating excerpt:', error);
                        alert("An error occurred while generating the excerpt.");
                    } finally {
                        setIsLoading(false);
                    }
                };

                return (
                    <PluginDocumentSettingPanel
                        name="ai-generate-excerpt-panel"
                        title={__('AI Generate Excerpt', 'ai-generate-excerpt')}
                        className="ai-generate-excerpt-panel"
                    >
                        <Button
                            isPrimary
                            onClick={handleGenerateExcerpt}
                            disabled={isLoading}
                        >
                            {isLoading ? __('Generating...', 'ai-generate-excerpt') : __('Generate Excerpt with AI', 'ai-generate-excerpt')}
                        </Button>
                    </PluginDocumentSettingPanel>
                );
            };

            wp.plugins.registerPlugin('ai-generate-excerpt', {
                render: AIGenerateExcerptButton,
            });
        }
    });
})(jQuery);
