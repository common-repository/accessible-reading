<?php

/**
 * Main Plugin File
 *
 * @package AuRise\Plugin\AccessibleReading
 */

namespace AuRise\Plugin\AccessibleReading;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AuRise\Plugin\AccessibleReading\Utilities;
use AuRise\Plugin\AccessibleReading\Settings;
use \WP_Query;

class AccessibleReading
{

    /**
     * The single instance of the class.
     *
     * @var AccessibleReading
     * @since 1.0.0
     */
    protected static $_instance = null;

    /** @var bool Whether or not the bulk function is processing */
    private $processing_bulk = false;

    /** @var bool Whether or not the bulk function is being cancelled */
    private $cancel_bulk = false;

    /**
     * Main Instance
     *
     * Ensures only one instance of is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return AccessibleReading Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $settings = Settings::instance(); //Run settings once

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts')); //Enqueue styles/scripts for admin page
        add_action('add_meta_boxes', array($this, 'add_metabox')); //Add metabox to posts dashboard pages
        add_action('save_post', array($this, 'save_post'), 5, 3); //Save accessible reading meta on post save
        add_action('post_updated', array($this, 'update_post'), 5, 3); //Update accessible reading meta on post update
        add_action('wp_ajax_accessible_reading_estimate_post', array($this, 'estimate_post')); //Add AJAX call for logged in users to estimate a single post
        add_action('wp_ajax_accessible_reading_start_bulk_update', array($this, 'bulk_start')); //Add AJAX call for logged in users to bulk save from admin page
        add_action('wp_ajax_accessible_reading_stop_bulk_update', array($this, 'cancel_bulk_processing')); //Add AJAX call for logged in users to bulk save from admin page
        add_action(Settings::$vars['hook'], array($this, 'bulk_event'), 10, 3); //Add custom CRON scheduler action

        //Init frontend class
        $frontend = new Frontend();
    }

    //**** Post Edit Screen ****//

    /**
     * Admin Scripts and Styles
     *
     * Enqueue scripts and styles to be used on the admin pages
     *
     * @since 1.0.0
     * @param string $hook Hook suffix for the current admin page
     */
    public function admin_enqueue_scripts($hook)
    {
        // Load only on the new/edit pages for the appropriate post types
        $post_types = Settings::get_post_types();
        foreach ($post_types as $post_type) {
            $edit_hook = $post_type . '.php';
            $new_hook = $post_type . '-new.php';
            if ($hook == $new_hook || $hook == $edit_hook) {
                //Stylesheets
                wp_enqueue_style(
                    Settings::$vars['prefix'] . 'metabox',
                    Settings::$vars['url'] . 'assets/styles/admin-metabox.css',
                    array(),
                    filemtime(Settings::$vars['path'] . 'assets/styles/admin-metabox.css')
                );
                //Scripts
                wp_enqueue_script('jquery');
                wp_enqueue_script(
                    Settings::$vars['prefix'] . 'metabox',
                    Settings::$vars['url'] . 'assets/scripts/admin-metabox.js',
                    array('jquery'),
                    filemtime(Settings::$vars['path'] . 'assets/scripts/admin-metabox.js'),
                    true
                );
                wp_localize_script(
                    Settings::$vars['prefix'] . 'metabox',
                    'au_object',
                    array('ajax_url' => admin_url('admin-ajax.php'))
                );
                return;
            }
        }
    }

    /**
     * Add Metabox
     *
     * Adds this plugin's metabox to the edit pages of the posts of the appropriate post type.
     *
     * @since 1.3.0
     */
    public function add_metabox()
    {
        $post_types = Settings::get_post_types();
        foreach ($post_types as $post_type) {
            add_meta_box(
                Settings::$vars['slug'], // Unique ID
                Settings::$vars['name'], // Box title
                array($this, 'display_metabox'), // Content callback, must be of type callable
                $post_type // Post type
            );
        }
    }

    /**
     * Display Metabox
     *
     * HTML markup for the metabox used to manage this plugin at the post level
     *
     * @since 1.3.0
     * @param WP_Post $post Post Object.
     */
    public function display_metabox($post)
    {
        //Prevent unauthorized users from viewing the page
        if (!current_user_can(Settings::$vars['capability_post'], $post->ID)) {
            return;
        }

        //Get the post-level meta field HTML
        $fields = array();
        foreach (Settings::$vars['post_options'] as $option => $option_settings) {
            switch ($option_settings['atts']['type']) {
                case 'select':
                    $fields[$option] = array(
                        'input' => $this->get_dropdown(array(
                            'name' => $option,
                            'value' => Settings::get_meta(
                                $post->ID,
                                $option,
                                $option_settings['default']
                            ),
                            'label' => $option_settings['label'],
                            'options' => $option_settings['atts']['options']
                        )),
                        'label' => $option_settings['label'],
                        'description' => $option_settings['description']
                    );
                    break;
                case 'richtext':
                    $fields[$option] = array(
                        'input' => $this->get_textarea(array(
                            'name' => $option,
                            'value' => Settings::get_meta(
                                $post->ID,
                                $option,
                                $option_settings['default']
                            ),
                            'label' => $option_settings['label']
                        )),
                        'label' => $option_settings['label'],
                        'description' => $option_settings['description']
                    );
                    break;
                default:
                    //Might make other types later, IDK
                    break;
            }
        }

        //Load the template file
        load_template(Settings::$vars['path'] . 'templates/edit-metabox.php', true, array(
            'post' => array('ID' => $post->ID, 'type' => $post->post_type),
            'plugin_settings' => Settings::$vars,
            'has_api_configured' => !empty(Settings::get(Settings::$vars['prefix'] . 'api_key', true)),
            'api_request_count' => Settings::get_api_request_tracker()['count'],
            'api_request_limit' => Settings::get_daily_limit(),
            'api_request_estimate' => sprintf(
                __('This post will use approximately %s API request(s) to process accessible content due to the character limitations.', 'accessible-reading'),
                $this->estimate_post_content(
                    $post->ID,
                    '',
                    true,
                    true,
                    $post->post_type
                )
            ),
            'fields' => $fields
        ));
    }

    /**
     * Display Dropdown HTML
     *
     * @since 1.3.0
     * @param array $args An associatve array with keys for `name` (string), `value`
     * (string of `true` or `false`), `yes` (string), `no` (string), and `label` (string)
     * @return string HTML for the checkbox switch
     */
    private function get_dropdown($args)
    {
        $value = esc_attr(strval($args['value']));
        $html = sprintf(
            '<label for="%1$s%2$s">%3$s</label><select id="%1$s%2$s" name="%1$s%2$s">',
            esc_attr(Settings::$vars['prefix']), // 1 - Setting Prefix
            esc_attr($args['name']), //2 - Input name
            esc_html($args['label']), //3 - Input Label
        );
        if (is_array($args['options']) && count($args['options'])) {
            foreach ($args['options'] as $option_value => $option) {
                $option_value = esc_attr(strval($option_value));
                $html .= sprintf(
                    '<option value="%s"%s>%s</option>',
                    $option_value,
                    $option_value === $value ? ' selected' : '',
                    esc_html($option['label'])
                );
            }
        } else {
            $html .= sprintf('<option value="global">%s</option>', __('Use Global Setting', 'accessible-reading'));
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Display Rich Text Editor HTML
     *
     * @since 1.3.0
     * @param array $args An associatve array with keys for `name` (string), `value`
     * (string of `true` or `false`), `yes` (string), `no` (string), and `label` (string)
     * @return string HTML for the checkbox switch
     */
    private function get_textarea($args)
    {
        $html = sprintf(
            '<label for="%1$s%2$s">%3$s</label><textarea id="%1$s%2$s" name="%1$s%2$s">%4$s</textarea>',
            esc_attr(Settings::$vars['prefix']), // 1 - Setting Prefix
            esc_attr($args['name']), //2 - Input name
            esc_html($args['label']), //3 - Input Label
            esc_attr(strval($args['value'])) //4 - Input Value
        );
        $html .= '</select>';
        return $html;
    }

    //**** Saving Post Data ****//

    /**
     * Update Post Meta
     *
     * @since 1.3.0
     * @param int $post_id Post ID
     * @param array $args Post args
     * @param string $post_type
     */
    private function update_post_meta($post_id, $args, $post_type = 'post')
    {
        if (is_array($args) && count($args)) {
            //Loop through post-level options
            foreach (Settings::$vars['post_options'] as $option => $option_settings) {
                // Utilities::debug_log(Utilities::array_has_key(
                //     Settings::$vars['prefix'] . $option,
                //     $args,
                //     'DEFAULT'
                // ), Settings::$vars['prefix'] . $option);
                Settings::set_meta(
                    $post_id,
                    $option,
                    Utilities::array_has_key(
                        Settings::$vars['prefix'] . $option,
                        $args,
                        $option_settings['default']
                    )
                );
            }
        }
    }

    /**
     * Process Post on Save/Update
     *
     * @since 2.0.0
     * @param int $post_id Post ID
     * @param string $content Optional. The content to process into accessible content.
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @param array $posted_content Optional. The posted content on save/update. Default is empty array.
     * @param string $before_content Optional. The content from the previous version to compare if overwriting. Default is empty string.
     * @return void
     */
    private function process_on_save($post_id, $content = '', $post_type = 'post', $posted_content = array(), $before_content = '')
    {
        // Utilities::debug_log(' ');
        //Update the settings in the metabox
        $this->update_post_meta($post_id, $posted_content, $post_type); //Only does anything if "posted content" is an array with values

        //Continue checking if accessible content needs to be created
        if (!Settings::post_enabled($post_id, $posted_content)) {
            // Utilities::debug_log('This post is disabled, do nothing more');
            return;
        }

        //Looks for shortcodes in content for processing
        // Utilities::debug_log('Processing for shortcodes');
        $this->process_shortcodes($post_id, $content, $post_type);

        if (!in_array($post_type, Settings::get_post_types())) {
            // Utilities::debug_log('The post type [' . $post_type . '] is not allowed, do nothing more');
            return;
        } elseif (!empty($before_content) && $before_content == $content && Settings::get_meta($post_id, 'content')) {
            // Utilities::debug_log('The body content did not change and meta content exists, do nothing more');
            return;
        }

        //Process post body content
        // Utilities::debug_log('Continue processing the full post body content');
        $this->process_post_content($post_id, $content, $post_type);
    }

    /**
     * Save Post
     *
     * When creating a new post, update the accessible reading content
     *
     * @since 1.0.0
     * @param integer $post_id Post ID.
     * @param WP_Post $post Post object
     * @param integer $update If post is being updated, 1 is passed. Otherwise 0.
     */
    public function save_post($post_id, $post, $update)
    {
        //Utilities::debug_log(' ');
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            //Utilities::debug_log('Save Post - autosaving, do nothing');
            return; //Autosave, do nothing
        } elseif (defined('DOING_AJAX') && DOING_AJAX) {
            //Utilities::debug_log('Save Post - doing AJAX, do nothing');
            return; // AJAX call, do nothing
        } elseif (!current_user_can(Settings::$vars['capability_post'], $post_id)) {
            //Utilities::debug_log('Save Post - user does not have permission, do nothing');
            return; // If user does not have permissions, do nothing
        } elseif ($update) {
            //Utilities::debug_log('Save Post - updating, do nothing');
            return; //If this is an update, do nothing
        } elseif (!in_array($post->post_type, Settings::get_post_types())) {
            // Utilities::debug_log('Save Post - the post type [' . $post->post_type . '] is not allowed, only do shortcodes');
            //Looks for shortcodes in content for processing
            $this->process_shortcodes($post_id, $post->post_content, $post->post_type);
            return;
        }
        //Utilities::debug_log('Save Post - continue with processing on save');
        $this->process_on_save($post_id, $post->content, $post->post_type, $_POST);
    }

    /**
     * Update Post
     *
     * @since 1.0.0
     * @param int $post_id Post ID
     * @param WP_Post $post_after Post object following the update
     * @param WP_Post $post_before Post object before the update
     */
    public function update_post($post_id, $post_after, $post_before)
    {
        //Utilities::debug_log(' ');
        if (!in_array($post_after->post_type, Settings::get_post_types())) {
            // Utilities::debug_log('Update Post - the post type [' . $post_after->post_type . '] is not allowed, only do shortcodes');
            //Looks for shortcodes in content for processing
            $this->process_shortcodes($post_id, $post_after->post_content, $post_after->post_type);
            return;
        }
        //Utilities::debug_log('Update Post - continue with processing on save');
        $this->process_on_save($post_id, $post_after->content, $post_after->post_type, $_POST, $post_before->content);
    }

    //**** Content Manipulation ****//

    /**
     * Get Content Chunks
     *
     * @since 1.2.0
     * @param string $content
     * @param bool $count_only If true, will return the array count instead
     * @return array|int a sequential array of HTML content divided
     * up to fit within the API request parameters or the count of that array
     */
    private function get_content_chunks($content = '', $count_only = false)
    {
        $chunks = array();
        if (is_string($content) && !empty($content)) {
            $length = strlen($content);
            $char_limit = Settings::get_character_limit();
            $s = 0; //Starting index
            do {
                //Get the excerpt using the character limit
                $excerpt = substr(
                    $content, //Original content
                    $s, //Starting index point
                    $char_limit //Length
                );
                if (is_string($excerpt)) {
                    //Trim the content to a block level elements
                    $possible_ends = array(
                        'line' => strripos($excerpt, PHP_EOL), //Identify the last occurrence of a new line
                        'div' => strripos($excerpt, '</div>'), //Identify the last occurrence of a closing div tag
                        'ul' => strripos($excerpt, '</ul>'), //Identify the last occurrence of a closing unordered list
                        'ol' => strripos($excerpt, '</ol>'), //Identify the last occurrence of a closing ordered list
                        'p' => strripos($excerpt, '</p>'), //Identify the last occurrence of a closing paragraph tag
                        'h1' => strripos($excerpt, '</h1>'), //Identify the last occurrence of a closing header 1 tag
                        'h2' => strripos($excerpt, '</h2>'),  //Identify the last occurrence of a closing header 2 tag
                        'h3' => strripos($excerpt, '</h3>'), //Identify the last occurrence of a closing header 3 tag
                        'h4' => strripos($excerpt, '</h4>'), //Identify the last occurrence of a closing header 4 tag
                        'h5' => strripos($excerpt, '</h5>'), //Identify the last occurrence of a closing header 5 tag
                        'h6' => strripos($excerpt, '</h6>') //Identify the last occurrence of a closing header 6 tag
                    );
                    $excerpt_end = $char_limit;
                    foreach ($possible_ends as $tag => $possible_end) {
                        if (is_numeric($possible_end) && $possible_end < $excerpt_end) {
                            $excerpt_end = $possible_end;
                            if ($tag !== 'line') {
                                $excerpt_end += strlen($tag) + 3; //Include the closing tag in the trim
                            }
                        }
                    }
                    $excerpt = substr($excerpt, 0, $excerpt_end); //Chop the excerpt
                    $s += $excerpt_end; //New starting index is at the excerpt's end
                    $chunks[] = $excerpt; //Add the excerpt to the list of chunks
                }
            } while ($s < $length);
        }
        if ($count_only) {
            return count($chunks); //Return the number of chunked contents
        }
        return $chunks; //Return the array of chunked content
    }

    /**
     * Compare Chunked Content
     *
     * @since 2.0.0
     * @param array $new The new sequential array of HTML content to be processed into accessible content
     * @param array $old Optional. The old sequential array of HTML content that was processed into accessible content. Default is an empty array.
     * @return bool Returns true if the new content is the same as the old content, false otherwise.
     */
    private function compare_content_chunks($new = array(), $old = array())
    {
        $new_c = count($new);
        $old_c = count($old);
        if ($old_c && $old_c === $new_c) {
            //Only consider comparing if we have any previous content to compare, and if it's the same amount
            $same = 0;
            for ($i = 0; $i < $old_c; $i++) {
                if ($old[$i] == $new[$i]) {
                    $same++;
                }
            }
            return $same == $old_c; //These are identical, no need to pull from API
        }
        return false;
    }

    /**
     * Get API Request Count for Single Post
     *
     * @since 1.2.0
     * @param int $post_id Post ID
     * @param string $content Optional. The content to process into accessible content.
     * @param bool $count_only
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @return int|false|array Optional. The number of API requests that will be needed to process
     * this post, or false on failure, or if `$count_only` was false, will return a
     * sequential array of chunked content ready for processing
     */
    private function estimate_post_content($post_id, $content = '', $force_new = false, $count_only = true, $post_type = 'post')
    {
        $request_count = false;
        $valid_post_id = is_numeric($post_id) && $post_id > 0;
        if (!$force_new && $valid_post_id) {
            if ($count_only) {
                $request_count = Settings::get_meta($post_id, 'api_request_count', 0);
                if (is_numeric($request_count)) {
                    return $request_count;
                }
            } else {
                $content_chunks = Settings::get_meta($post_id, 'content_chunked');
                if (is_array($content_chunks)) {
                    return $content_chunks;
                }
            }
        }
        if (!$content && $valid_post_id) {
            //Get this post's content, this may have to change depending on post type?
            $content = get_the_content(null, false, $post_id);
        }
        $content_chunks = $this->get_content_chunks(Utilities::normalize_html($content));
        $request_count = count($content_chunks);
        if ($count_only) {
            return $request_count;
        }
        return $content_chunks;
    }

    /**
     * Get API Request Count for Single Post
     *
     * AJAX request from admin-metabox.js. It will also set the count if it wasn't already set
     *
     * @since 1.3.0
     * @param int $post_id Post ID
     * @param string $content Optional. The content to process into accessible content.
     * @param bool $count_only
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @return int|false|array Optional. The number of API requests that will be needed to process
     * this post, or false on failure, or if `$count_only` was false, will return a
     * sequential array of chunked content ready for processing
     */
    public function estimate_post()
    {
        //Get parameters from POST
        $return = array(
            'success' => 0,
            'error' => 0,
            'messages' => array('Processing single post'),
            'output' => '',
            'fields' => array(
                'post_id' => Utilities::array_has_key('post_id', $_POST),
                'post_type' => Utilities::array_has_key('post_type', $_POST),
                'post_content' => Utilities::array_has_key('post_content', $_POST)
            )
        );
        // Utilities::debug_log($return);
        if ($return['fields']['post_content'] || ($return['fields']['post_id'] && is_numeric($return['fields']['post_id']) && $return['fields']['post_id'] > 0)) {
            $return['output'] = sprintf(
                __('This post will use approximately %s API request(s) to process accessible content due to the character limitations.', 'accessible-reading'),
                $this->estimate_post_content(
                    $return['fields']['post_id'],
                    $return['fields']['post_content'],
                    true, //Force new
                    true, //Return just the count
                    $return['fields']['post_type']
                )
            );
        } else {
            $return['error']++;
            $return['output'] = __('Could not estimate API usage because either the post type or post ID is invalid.', 'accessible-reading');
        }
        wp_die(json_encode($return));
    }

    /**
     * Get Post Estimates
     *
     * @since 1.2.0
     * @param int $batch_limit
     * @param bool $all
     * @param bool $force_new
     * @return array an associatve array that include keys for `total` (int|bool, total
     * number of requests that would be used), `last_run` (TBD), `count` (int, the total
     * number of posts included in this query), and `post_ids` (array, a sequential array
     * of Post IDs)
     */
    private function get_post_estimates($batch_limit, $all = false, $force_new = false)
    {
        $return = array(
            'total' => false, //The total number of requests that would be used if the bulk updater was run on these settings
            'last_run' => '', //Estimating when the last CRON event would be run
            'count' => 0,
            'post_ids' => array()
        );
        //Query the blog for posts
        $query_args = array(
            'fields' => 'ids', //Return an array of post IDs
            'post_type' => Settings::get_post_types(), //Only our allowed post types
            'posts_per_page' => -1, //Get all
            'orderby' => 'date', //Order by publish date
            'order' => 'DESC', //Get the newest first
        );

        if (!$all) {
            //$return['messages'][] = 'Only processing posts that do not have accessible content.';
            $meta_key = Settings::$vars['prefix'] . 'content';
            $query_args['meta_query'] = array(
                'relation' => 'OR',
                //return results where field does not exist
                array(
                    'key' => $meta_key,
                    'value' => 'bug #23268', //Included for compatibility for WordPress version below 3.9
                    'compare' => 'NOT EXISTS'
                ),
                //return results where field exists but is empty
                array(
                    'key' => $meta_key,
                    'value' => array(''),
                    'compare' => 'IN'
                )
            );
        } else {
            //$return['messages'][] = 'All posts excluding those that are disabled';
            $meta_key = Settings::$vars['prefix'] . 'enabled';
            $query_args['meta_query'] = array(
                'relation' => 'OR',
                //return results where field does not exist
                array(
                    'key' => $meta_key,
                    'value' => 'bug #23268', //Included for compatibility for WordPress version below 3.9
                    'compare' => 'NOT EXISTS'
                ),
                //return results where field exists but is empty or is equal to 1
                array(
                    'key' => $meta_key,
                    'value' => array('', '1'),
                    'compare' => 'IN'
                )
            );
        }

        $query = new WP_Query($query_args);
        $post_ids = $query->posts;
        // Utilities::debug_log($post_ids, 'Post IDs');
        $return['count'] = is_array($post_ids) ? count($post_ids) : 0;
        if ($return['count']) {
            //$return['post_ids'] = $post_ids;
            if (!$all) {
                //Resetting count because we need to filter disabled posts, can't mix and/or meta queries
                $return['count'] = 0;
            }
            $return['total'] = 0;
            foreach ($post_ids as $post_id) {
                //Only get post ID if enabled
                $post_type = get_post_type($post_id);
                if (Settings::get_meta($post_id, 'enabled', Settings::$vars['post_options']['enabled']['default'], $post_type)) {
                    $return['post_ids'][] = $post_id; //Add post ID to final array
                    $requests = $this->estimate_post_content($post_id, '', $force_new, true, $post_type);
                    if (is_numeric($requests)) {
                        $return['total'] += $requests;
                    }
                }
            }

            //How many CRON events would get scheduled
            $return['crons'] = ceil($return['total'] / $batch_limit);
        } else {
            // Utilities::debug_log('Post IDs were not an array or were empty, could not create an estimate.');
        }
        $return['count'] = count($return['post_ids']);
        return $return;
    }

    //**** Data Processing ****//

    /**
     * Create Accessible Reading Content for Post
     *
     * @since 1.1.0
     * @param int $post_id Post ID
     * @param string $content Optional. The content to process into accessible content.
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @return string accessible content or empty string on failure
     */
    public function process_post_content($post_id, $content = '', $post_type = 'post')
    {
        if (is_numeric($post_id) && $post_id) {
            if (!$content) {
                //Get this post's content
                $content = get_the_content(null, false, $post_id);
            }
            if (is_string($content) && $content) {
                $accessible_content = $this->set_accessible_content($content, Settings::get_meta($post_id, 'content_chunked', array())); //Sent it through the API
                if ($accessible_content['content']) {
                    Settings::set_meta($post_id, 'content', $accessible_content['content']); //Save accessible content to post
                    Settings::set_meta($post_id, 'content_chunked', $accessible_content['chunks']); //Save chunks of excerpts to post
                    Settings::set_meta($post_id, 'api_request_count', count($accessible_content['chunks'])); //Save the number of requests it took to process
                    // Utilities::debug_log('Accessible content saved to ' . $post_id);
                    return $accessible_content['content'];
                } elseif ($accessible_content['same_as_prev']) {
                    // Utilities::debug_log('Accessible content not saved to ' . $post_id . ' because it was identical to current content');
                    return Settings::get_meta($post_id, 'content'); //Return previously saved content
                } else {
                    // Utilities::debug_log('Accessible content not saved to ' . $post_id . ' because it was empty!');
                }
            } else {
                // Utilities::debug_log('Process Post - original content was blank');
            }
        } else {
            // Utilities::debug_log('Process Post - invalid post ID');
        }
        return '';
    }

    /**
     * Process Shortcode Content
     *
     * Search content for `[accessible_reading]` shortcodes and process it's content if found
     *
     * @since 2.0.0
     * @param int $post_id Post ID
     * @param string $content Optional. The content to process into accessible content.
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @return void
     */
    private function process_shortcodes($post_id, $content = '', $post_type = 'post')
    {
        //Search for our shortcodes in the content
        if (strpos($content, '[/accessible_reading]') !== false) {
            $found = Utilities::discover_shortcodes(Utilities::normalize_html($content), 'accessible_reading', true);
            if (count($found['shortcodes'])) {
                foreach ($found['shortcodes'] as $i => $s) {
                    //Process and save shortcodes that have a configured ID and content
                    $this->save_shortcode($post_id, Utilities::array_has_key('id', $s['atts']), $s['content']);
                }
            }
        }
    }

    /**
     * Save Shortcode
     *
     * Saves shortcode's accessible content to post meta
     *
     * @since 2.0.0
     * @param int $post_id
     * @param string $id The `id` attribute of the shortcode
     * @param string $content The shortcode content
     * @return array|false An associative array with `chunks` (sequential array of strings), `content` (string of HTML content), `same_as_prev` (bool), and `saved` (bool) keys. False on failure.
     */
    public function save_shortcode($post_id, $id, $content)
    {
        if ($id && $content) {
            $accessible_content = $this->set_accessible_content($content, Settings::get_meta($post_id, 'shortcode_' . $id . '_chunks', array()));
            $accessible_content['saved'] = false;
            if ($accessible_content['content']) {
                //Save the accessible content
                //Utilities::debug_log('Saving shortcode content - ID: ' . $id);
                Settings::set_meta($post_id, 'shortcode_' . $id, $accessible_content['content']);
                Settings::set_meta($post_id, 'shortcode_' . $id . '_chunks', $accessible_content['chunks']);
                $accessible_content['saved'] = true;
            }
            return $accessible_content;
        }
        return false;
    }

    /**
     * Create Accessible Reading Content
     *
     * Utilize the Bionic Reading API to generate accessible
     * content of the original content.
     *
     * @since 1.0.0
     * @param string $content The content to process into accessible content.
     * @return string accessible content
     */
    private function create_accessible_content($content = '')
    {
        $accessible_content = '';
        if ($content) {
            //Use the API to generate bionic content
            $api_key = Settings::get(Settings::$vars['prefix'] . 'api_key', true);
            if ($api_key) {
                $api = wp_remote_post('https://bionic-reading1.p.rapidapi.com/convert', array(
                    'method' => 'POST',
                    'timeout' => 30,
                    'redirection' => 10,
                    'httpversion' => CURL_HTTP_VERSION_1_1,
                    'user-agent' => 'AccessibleReading/' . Settings::$vars['version'] . ' (WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url') . ')',
                    'blocking' => true,
                    'headers' => array(
                        'content-type' => 'application/x-www-form-urlencoded',
                        'X-RapidAPI-Host' => 'bionic-reading1.p.rapidapi.com',
                        'X-RapidAPI-Key' => $api_key
                    ),
                    'body' => array(
                        'content' => $content,
                        'response_type' => 'html',
                        'request_type' => 'html',
                        'fixation' => Settings::get(Settings::$vars['prefix'] . 'fixation', true),
                        'saccade' => Settings::get(Settings::$vars['prefix'] . 'saccade', true)
                    )
                ));
                if (is_wp_error($api)) {
                    // Utilities::debug_log($api, 'API Connection Error');
                } else {
                    //Update request tracker
                    /**
                     * Possible response headers:
                     *
                     * "access-control-allow-origin": "*"
                     * "access-control-expose-headers": "Content-Disposition"
                     * "connection": "close"
                     * "content-type": "text/html; charset=utf-8"
                     * "content-language": "en"
                     * "content-length": "1121"
                     * "etag": "W/"461-wetg6ykMeFm+a+qMCUnoOSAlj+4"
                     * "server": "RapidAPI-1.2.8"
                     * "strict-transport-security": "max-age=31536000"
                     * "x-powered-by": "Express"
                     * "x-rapidapi-region": "AWS - us-east-1"
                     * "x-rapidapi-version": "1.2.8"
                     * "x-ratelimit-limit": "100000" <-- referrs to the character limit?
                     * "x-ratelimit-remaining": "99999"
                     * "x-ratelimit-reset": "1663949945"
                     * "x-ratelimit-requests-limit": "500" <-- referrs to the API request limit
                     * "x-ratelimit-requests-remaining": "496"
                     * "x-ratelimit-requests-reset": "84856"
                     */
                    $remaining = wp_remote_retrieve_header($api, 'x-ratelimit-requests-remaining');
                    $daily_limit = wp_remote_retrieve_header($api, 'x-ratelimit-requests-limit');
                    // Utilities::debug_log(sprintf('API response: %s of %s requests remaining today', $remaining, $daily_limit));
                    Settings::update_api_request_tracker($remaining, $daily_limit, 1);
                    //Get the accessible content from the API
                    $accessible_content = wp_remote_retrieve_body($api);
                    if (strpos($accessible_content, '{') === 0) {
                        //This is a JS error
                        // Utilities::debug_log(json_decode($accessible_content, true, 512, JSON_PRETTY_PRINT), 'API Error');
                        $accessible_content = '';
                    }
                }
            } else {
                // Utilities::debug_log('Cannot create accessible content: API key is missing!');
            }
        } else {
            // Utilities::debug_log('Cannot create accessible content: content parameter was falsy');
        }
        return Utilities::normalize_html($accessible_content);
    }

    /**
     * Process Content into Accessible Content
     *
     * @since 2.0.0
     * @param string $content Content to process
     * @param array A sequential array of HTML content divided up to compare before sending through API.
     * @return array An associative array with `chunks` (sequential array of strings), `content` (string of HTML content), and `same_as_prev` (bool) keys.
     */
    private function set_accessible_content($content = '', $prev_content_chunks = array())
    {
        $return = array(
            'chunks' => array(),
            'content' => '',
            'same_as_prev' => false
        );
        if (is_string($content) && $content) {
            $return['chunks'] = $this->get_content_chunks(Utilities::normalize_html($content));
            // Utilities::debug_log($return['chunks']);
            $request_count = count($return['chunks']); //How many API requests this is going to need
            $return['same_as_prev'] = $this->compare_content_chunks($return['chunks'], $prev_content_chunks);
            $limit = Settings::is_within_daily_limit(50, $request_count);
            //Only set new accessible content if we have content, if it's not the same as the old content, and if we have the API requests to do it
            if ($request_count && !$return['same_as_prev'] && $limit) {
                $return['content'] = array();
                foreach ($return['chunks'] as $excerpt) {
                    $content_from_api = Utilities::normalize_html($this->create_accessible_content($excerpt)); //Sent it through the API
                    //Remove the wrapping stuff the API adds e.g. `<div class="bionic-reader bionic-reader-5505974f-59cf-4822-ab7a-6c50db5a21ca origin "> <div class="bionic-reader-content"> <div class="bionic-reader-container">`
                    if ($content_from_api) {
                        $last_div = '<div class="bionic-reader-container">';
                        $p = strpos($content_from_api, $last_div);
                        if ($p !== false) {
                            $p += strlen($last_div);
                            //This is added to the end of the content from the API, `</div> </div> </div>`, so remove it
                            $accessible_content = trim(str_replace('</div> </div> </div>', '', substr($content_from_api, $p)));
                            if ($accessible_content) {
                                $return['content'][] = $accessible_content;
                            }
                        }
                    }
                }
                if (count($return['content'])) {
                    $return['content'] = sprintf(
                        //Re-add the wrapper so styles and such apply
                        '<div class="bionic-reader compiled-by-accessible-reading"><div class="bionic-reader-content"><div class="bionic-reader-container">%s</div></div></div>',
                        implode('', $return['content']) //Return array to string
                    );
                }
            } else {
                if (!$limit) {
                    // Utilities::debug_log('No more API requests are available today!');
                }
                if ($return['same_as_prev']) {
                    // Utilities::debug_log('Identical chonkers :O');
                }
                if (!$request_count) {
                    // Utilities::debug_log('NooOOOoooO!! chonkers D:<');
                }
            }
        }
        return $return;
    }

    //**** Bulk Update Functions ****//

    /**
     * Stop Processing Bulk Operation
     *
     * Is called from within a script if the cancellation flag is true
     *
     * @since 1.3.0
     */
    private function stop_processing_bulk()
    {
        //Remove next scheduled event
        $cron = Utilities::next_scheduled(Settings::$vars['hook'], false, true);
        // Utilities::debug_log($cron, 'Cancel this CRON event!');
        // if ($timestamp = wp_next_scheduled(Settings::$vars['hook'])) {
        //     wp_unschedule_event($timestamp, Settings::$vars['hook']);
        // }

        //Reset the flag
        Settings::set('cancel_bulk', false);
    }

    /**
     * Cancel Bulk Processing
     *
     * AJAX request from admin-dashboard.js, sets the cancellation flag to true
     *
     * @since 1.3.0
     */
    public function cancel_bulk_processing()
    {
        //Flag the cancellation
        Settings::set('cancel_bulk', true);
        // Utilities::debug_log('Setting the cancel bulk flag to true');
        $this->stop_processing_bulk();
        wp_die('Setting the cancel bulk flag to true');
    }

    /**
     * Start Bulk Post Update
     *
     * AJAX request from admin-dashboard.js
     *
     * @since 1.1.0
     */
    public function bulk_start()
    {
        $already_processing = Settings::is_processing_bulk();
        Settings::set('processing_bulk', true);
        // Utilities::debug_log('Start Bulk Updater', ' ');
        //Get parameters from POST
        $fields = Utilities::array_has_key('fields', $_POST, array());
        $return = array(
            'success' => 0,
            'error' => 0,
            'messages' => array('Starting bulk updater'),
            'output' => '',
            'fields' => $fields
        );
        if ($fields) {
            $return['success']++;
            $fields = explode('&', $fields);
            $args = array();
            foreach ($fields as $property_string) {
                $split = explode('=', $property_string);
                if (count($split) > 1) {
                    $args[$split[0]] = $split[1];
                }
            }
            $daily_limit = Settings::get_daily_limit();
            $batch_limit = Utilities::array_has_key('batch_limit', $args);
            if ($batch_limit && $daily_limit && $batch_limit <= $daily_limit) {
                $return['success']++;

                $estimate = Utilities::array_has_key('estimate', $args);
                $estimation = $this->get_post_estimates(
                    $batch_limit,
                    Utilities::array_has_key('force_generate', $args),
                    $estimate
                );

                $plan = Settings::get_plan();
                $end = $estimation['count'] < $batch_limit ? $estimation['count'] : $batch_limit;
                $same_day = Settings::is_within_daily_limit(50, $end); //If we've reached our daily limit

                if ($estimate) {
                    //Return estimation details
                    if ($estimation['count'] && $estimation['total']) {
                        $return['output'] = sprintf(
                            __('There are %s post(s) that will be processed with these settings.', 'accessible-reading'),
                            $estimation['count']
                        );
                        $same_day = Settings::is_within_daily_limit(50, $estimation['count']); //If we've reached our daily limit
                        $return['output'] .= ' ' . sprintf(
                            __('Based on your API plan and character counts of your post content, it will take %s API request(s) to complete this process.', 'accessible-reading'),
                            $estimation['total']
                        );
                        $return['output'] .= ' ' . sprintf(
                            __('Based on your batch limit, it will take approximately %s minutes to complete. Processing takes place in the background so you can safely leave this page once it is started.', 'accessible-reading'),
                            $estimation['crons']
                        );
                        if (!$same_day) {
                            $return['output'] .= ' ' . sprintf(
                                __("Processing will not be completed today due to your plan's daily API request limit. It will automatically resume 24 hours after it's last request.", 'accessible-reading'),
                                $estimation['crons']
                            );
                        }
                    } else {
                        $return['output'] = sprintf(
                            __('There are no posts that will be processed with these settings.', 'accessible-reading'),
                            $estimation['count']
                        );
                    }
                    $return['output'] = '<p>' . $return['output'] . '</p>';
                } elseif ($already_processing) {
                    $return['output'] .= ' ' . sprintf(
                        __('Processing is already in progress. Please stop it before starting a new one.', 'accessible-reading'),
                        $estimation['crons']
                    );
                    $return['output'] = '<p>' . $return['output'] . '</p>';
                } else {
                    //Process posts
                    $updated = Settings::set('bulk_update_posts', implode(',',  $estimation['post_ids']));
                    if ($updated) {
                        // Utilities::debug_log('Successfully updated post IDs meta');
                        $return['messages'][] = 'Successfully updated post IDs meta';
                    } else {
                        // Utilities::debug_log('Post IDs option failed to update (usually because the value did not change)');
                        $return['messages'][] = 'Post IDs option failed to update (usually because the value did not change)';
                    }

                    //Schedule first CRON event
                    $this->schedule_event(array(
                        'start' => 0,
                        'end' => $end,
                        'batch_limit' => $batch_limit,
                        'daily_limit' => $daily_limit
                    ), $same_day);

                    $return['output'] .= ' ' . sprintf(
                        __('The bulk updater has been started and will take approximately %s minute(s) to complete. Processing takes place in the background so you can safely leave this page.', 'accessible-reading'),
                        $estimation['crons']
                    );
                    $return['output'] = '<p>' . $return['output'] . '</p>';
                }
            } else {
                $return['error']++;
                $return['messages'][] = 'Either the batch limit or daily limit were invalid, or the batch limit was greater than the daily limit. Please check settings.';
            }
        } else {
            $return['error']++;
            $return['messages'][] = 'Posted fields were empty!';
        }
        Settings::set('processing_bulk', false);
        wp_die(json_encode($return));
    }

    /**
     * Schedule Bulk Event
     *
     * @since 1.1.0
     * @param array $event_args An associatve array with `start`, `end`, `batch_limit`, and `daily_limit` keys that all have integer values.
     * @param bool $same_day If true, will schedule the next batch in a few minutes. If false, 24 hours.
     */
    private function schedule_event($event_args, $same_day = true)
    {
        if (Settings::get('cancel_bulk')) {
            //If cancellation flag is true, stop processing
            // Utilities::debug_log('Schedule event: cancellation flag is true, stop processing');
            $this->stop_processing_bulk();
        } else {
            $time = $same_day ? '+' . Settings::$vars['schedule'] . ' minutes' : '+ 1 days';
            $scheduled = wp_schedule_single_event(
                strtotime($time), //time
                Settings::$vars['hook'], //event hook
                $event_args //event args
            );
            if ($scheduled) {
                // Utilities::debug_log('Cron Event Scheduled Successfully: [' . Settings::$vars['hook'] . '] for [' . $time . '] ' . json_encode($event_args, JSON_PRETTY_PRINT));
            } else {
                // Utilities::debug_log('Cron Event Failed to Schedule: [' . Settings::$vars['hook'] . '] for [' . $time . '] ' . json_encode($event_args, JSON_PRETTY_PRINT));
            }
        }
    }

    /**
     * Bulk Update
     *
     * CRON event. This updates a set amount of posts from
     * the saved IDs in options based on the parameters.
     *
     * @since 1.1.0
     * @param int $start The index of the post IDs array to start processing
     * @param int $end The index of the post IDs array to stop processing
     * @param int $batch_limit The maximum number of posts to process for this event.
     * @param int $daily_limit The maximum number of posts to process for today.
     */
    public function bulk_event($start = 0, $end = 0, $batch_limit = 100, $daily_limit = 400)
    {
        //Get the post IDs
        Settings::set('processing_bulk', true);
        if (Settings::get('cancel_bulk')) {
            //If cancellation flag is true, stop processing
            // Utilities::debug_log('CRON event: cancellation flag is true, stop processing');
            $this->stop_processing_bulk();
        } else {
            $post_ids = Settings::get('bulk_update_posts');
            $post_ids = $post_ids ? explode(',', $post_ids) : false;
            if (is_array($post_ids) && count($post_ids)) {
                $index = $start; //200
                do {
                    $this->process_post_content($post_ids[$index]); //Process the post ID at this index
                    $index++; //Increment index
                } while ($index < $end);
                /*
                    Pretend there are 500 articles, batch limit is 100, daily limit is 1000 and we already did the first 100
                    post count = 500 (total count) - 200 (current index)
                    end = 300 = 200 + 100 = (current index + batch limit)

                    if the end (300) is less or equal to than the total remaining (300), next round will be last, set it to the count

                    else if the end (300) is greater than the daily limit, schedule the next batch for tomorrow
                */
                //Calculate the next batch
                $post_count = count($post_ids);
                if ($index >= $post_count) {
                    //all batches are completed!
                    // Utilities::debug_log('Bulk Event - all batches are completed');
                } else {
                    //$post_count = count($post_ids) - $index; //Remaining articles 500 - 200 = 300
                    $start = $index; //Where we left off = 400 (of 450 articles)
                    $end = $index + $batch_limit; // 400 + 100 = 500
                    $batch_total = $batch_limit; //100
                    if ($end >= $post_count) { //500 < 450
                        //There are less posts than the batch limit, end at the end and it'll be the last batch
                        $batch_total = $end - $post_count; //500 - 450 = 50
                        $end = $post_count; //450
                    }
                    $same_day = Settings::is_within_daily_limit(50, $batch_total); //If we've reached our daily limit

                    //Schedule the next event
                    $this->schedule_event(array(
                        'start' => $start,
                        'end' => $end,
                        'batch_limit' => $batch_limit,
                        'daily_limit' => $daily_limit
                    ), $same_day);
                }
            } else {
                // Utilities::debug_log($post_ids, 'Bulk Event - post IDs were not an array or was empty');
            }
        }
        Settings::set('processing_bulk', false);
    }
}

/**
 * Returns the main instance of AccessibleReading.
 *
 * @since  1.0.0
 * @return AccessibleReading
 */
function accessible_reading()
{
    return AccessibleReading::instance();
}

/**
 * The global instance of the main class
 *
 * @var AccessibleReading
 * @since 1.0.0
 */
global $accessible_reading;
$accessible_reading = accessible_reading();//Run once to init