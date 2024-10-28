/*!
    Name: admin-metabox.js
    Plugin: Accessible Reading
    Author: AuRise Creative | https://aurisecreative.com
    Last Modified: 2022.10.09.08.35
*/
var $ = jQuery.noConflict(),
    accessibleReadingMetaBox = {
        version: '2022.10.09.08.35',
        init: function() {
            $('.au-metabox-accessible-reading .process-dry-run .button').on('click', accessibleReadingMetaBox.estimatePost.start);
        },
        estimatePost: {
            start: function(e) {
                e.preventDefault();
                accessibleReadingMetaBox.estimatePost.toggleFormStatus(true); //Disable the form
                $('.au-metabox-accessible-reading .process-dry-run .output').html('').hide(); //Clear out and hide old response
                var post_content = '',
                    $gt = $('.block-editor .is-mode-text textarea.editor-post-text-editor'); //Gutenberg code editor field
                if ($gt.length) {
                    post_content = $gt.val();
                }
                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: au_object.ajax_url,
                        data: {
                            'action': 'accessible_reading_estimate_post', //name of handle after "wp_ajax_" prefix
                            'post_id': $('.au-metabox-accessible-reading .process-dry-run input[name="post_id"]').val(),
                            'post_type': $('.au-metabox-accessible-reading .process-dry-run input[name="post_type"]').val(),
                            'post_content': post_content
                        },
                        cache: false,
                        error: function(xhr) {
                            accessibleReadingMetaBox.estimatePost.complete({
                                'success': 0,
                                'error': xhr,
                                'output': 'AJAX ERROR: ' + xhr.responseText
                            });
                        },
                        success: function(response) {
                            try {
                                response = JSON.parse(response);
                                setTimeout(function() {
                                    accessibleReadingMetaBox.estimatePost.complete(response);
                                }, 500);
                            } catch (xhr) {
                                accessibleReadingMetaBox.estimatePost.complete({
                                    'success': 0,
                                    'error': xhr,
                                    'response': response,
                                    'output': 'JSON ERROR: ' + xhr.responseText
                                });
                            }
                        }
                    });
                }, 500);
            },
            toggleFormStatus(toggle) {
                var $btn = $('.au-metabox-accessible-reading .process-dry-run .button'),
                    $spinner = $btn.next('.progress-spinner');
                if (toggle) {
                    //Disable the form
                    $btn.attr('disabled', 'disabled');
                    $spinner.show();
                } else {
                    //Enable the form
                    $btn.removeAttr('disabled');
                    $spinner.hide();
                }
            },
            complete(response) {
                accessibleReadingMetaBox.estimatePost.toggleFormStatus(false); //Enable the form
                if (response.error) {
                    console.error(response.error, 'If you keep seeing this error, please reach out to support@aurisecreative.com for assistance.', response.response);
                }
                if (response.output) {
                    $('.au-metabox-accessible-reading .process-dry-run .output').html(response.output).show();
                }
            }
        }
    };
$(document).ready(accessibleReadingMetaBox.init);