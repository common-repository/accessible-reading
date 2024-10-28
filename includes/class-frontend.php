<?php

/**
 * Plugin Frontend File
 *
 * @package AuRise\Plugin\AccessibleReading
 */

namespace AuRise\Plugin\AccessibleReading;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AuRise\Plugin\AccessibleReading\Utilities;
use AuRise\Plugin\AccessibleReading\Settings;
use AuRise\Plugin\AccessibleReading\AccessibleReading;


class Frontend
{

    /**
     * Plugin Settings
     *
     * @var AuRise\Plugin\AccessibleReading\Settings
     */
    private $settings;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('init', array($this, 'init_shortcodes')); //Add init hook to add shortcodes
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 20); //Enqueue styles for frontend
        add_filter('the_content', array($this, 'display_frontend'), 15); //display on frontend
    }

    /**
     * Initialise Shortcodes
     */
    public function init_shortcodes()
    {
        //Displays the toggle switch on the frontend
        add_shortcode('accessible_reading_toggle', array($this, 'display_toggle_switch'));

        //Displays the toggle switch on the frontend
        add_shortcode('accessible_reading_original_content', array($this, 'display_original_content'));

        //Displays the toggle switch on the frontend
        add_shortcode('accessible_reading_content', array($this, 'display_accessible_content'));

        //Flags save/update to generate content within shortcode and display it on frontend
        add_shortcode('accessible_reading', array($this, 'maybe_display_accessible_content'));
    }

    /**
     * Shortcode: Display Accessible Reading Toggle Switch
     *
     * @param array $atts Optional. An associative array of shortcode attributes. Default is an empty array.
     * @param string $content Optional. A string of content between the opening and closing tags. Default is an empty string.
     * @param string $tag Optional. The shortcode tag. Default is an empty string.
     *
     * @return string HTML output of the shortcode.
     */
    public function display_toggle_switch($atts = array(), $content = '', $tag = '')
    {
        //Optionally enqueue the registered assets
        Utilities::optionally_load_resource(array('type' => 'style', 'handle' => Settings::$vars['slug'], 'inline_styles' => Settings::dynamic_toggle_switch_styles()));
        Utilities::optionally_load_resource(array('type' => 'script', 'handle' => Settings::$vars['slug']));

        return sprintf(
            '<div class="accessible-reading-toggle lang-%s">%s</div>',
            substr(get_locale(), 0, 2),
            $this->get_checkbox_switch(array(
                'name' => Settings::$vars['slug'],
                'label' => __('Click/tap to toggle accessible reading mode', 'accessible-reading'),
                'value' => 'swap-accessible-reading',
                'reverse' => false,
                'no' => Settings::get('toggle_switch_text_enable', true, 'toggle_switch'), //__('Enable Accessible Reading', 'accessible-reading'),
                'yes' => Settings::get('toggle_switch_text_disable', true, 'toggle_switch') // __('Disable Accessible Reading', 'accessible-reading')
            )),
        );
    }

    /**
     * Shortcode: Display Content
     *
     * @since 2.0.0
     * @param array $atts Optional. An associative array of shortcode attributes. Default is an empty array.
     * @param string $content Optional. A string of content between the opening and closing tags. Default is an empty string.
     * @param string $tag Optional. The shortcode tag. Default is an empty string.
     * @return string HTML output of the shortcode.
     */
    private function display_content($atts = array(), $content = '', $tag = '')
    {
        if ($content) {
            //Optionally enqueue the registered assets
            Utilities::optionally_load_resource(array('type' => 'style', 'handle' => Settings::$vars['slug'], 'inline_styles' => Settings::dynamic_toggle_switch_styles()));
            Utilities::optionally_load_resource(array('type' => 'script', 'handle' => Settings::$vars['slug']));

            // Normalize attribute keys to lowercase and configure default values
            $atts = shortcode_atts(array(
                'type' => 'original', //Accepts `original` or `accessible`
                'classes' => '', //String of space-separated classes to add to the block
                'display' => 'block' //Accepts `block` or `none`
            ), array_change_key_case((array)$atts, CASE_LOWER), $tag);

            if ($atts['type'] == 'original') {
                $atts['classes'] .= ($atts['classes'] ? ' ' : '') . 'accessible-reading-original-content';
            } else {
                $atts['classes'] .= ($atts['classes'] ? ' ' : '') . 'accessible-reading-bionic-content';
            }

            return sprintf(
                '<div class="%s" style="display:%s">%s</div>',
                esc_attr($atts['classes']),
                esc_attr($atts['display']),
                $content
            );
        }
        return $content;
    }

    /**
     * Shortcode: Display Original Content
     *
     * @param array $atts Optional. An associative array of shortcode attributes. Default is an empty array.
     * @param string $content Optional. A string of content between the opening and closing tags. Default is an empty string.
     * @param string $tag Optional. The shortcode tag. Default is an empty string.
     *
     * @return string HTML output of the shortcode.
     */
    public function display_original_content($atts = array(), $content = '', $tag = '')
    {
        // Normalize attribute keys to lowercase and configure default values
        $atts = shortcode_atts(array(), array_change_key_case((array)$atts, CASE_LOWER), $tag);
        return $this->display_content(array_replace($atts, array('type' => 'original', 'classes' => 'shortcode-for-original-content')), $content, $tag);
    }

    /**
     * Shortcode: Display Accessible Content
     *
     * @param array $atts Optional. An associative array of shortcode attributes. Default is an empty array.
     * @param string $content Optional. A string of content between the opening and closing tags. Default is an empty string.
     * @param string $tag Optional. The shortcode tag. Default is an empty string.
     *
     * @return string HTML output of the shortcode.
     */
    public function display_accessible_content($atts = array(), $content = '', $tag = '')
    {
        $atts = shortcode_atts(array(), array_change_key_case((array)$atts, CASE_LOWER), $tag);
        return $this->display_content(array_replace($atts, array('type' => 'accessible', 'classes' => 'shortcode-for-accessible-content', 'display' => 'none')), $content, $tag);
    }

    /**
     * Shortcode: Display Original Content
     *
     * @since 2.0.0
     * @param array $atts Optional. An associative array of shortcode attributes. Default is an empty array.
     * @param string $content Optional. A string of content between the opening and closing tags. Default is an empty string.
     * @param string $tag Optional. The shortcode tag. Default is an empty string.
     * @return string HTML output of the shortcode.
     */
    public function maybe_display_accessible_content($atts = array(), $content = '', $tag = '')
    {
        // Normalize attribute keys to lowercase and configure default values
        $atts = shortcode_atts(array(
            'id' => '',
            'disabled' => '', //Disables processing
            'hide_toggle' => '', //Hide toggle switch for shortcode
            'post_id' => ''
        ), array_change_key_case((array)$atts, CASE_LOWER), $tag);
        if ($atts['disabled']) {
            return sprintf(
                '<!-- %s -->%s',
                __('Processing accessible reading content is disabled for this shortcode', 'accessible-reading'),
                $content
            );
        } elseif (empty($atts['id'])) {
            return sprintf(
                '<!-- %s -->%s',
                __('The `id` attribute for the accessible reading shortcode is missing! Cannot save processed content without it', 'accessible-reading'),
                $content
            );
        }
        $post_id = is_numeric($atts['post_id']) && $atts['post_id'] > 0 ? intval($atts['post_id']) : '';
        if (!$post_id) {
            global $post;
            $post_id = $post->ID;
        }
        $accessible_content = '';
        if (current_user_can(Settings::$vars['capability_post'], $post_id)) {
            //Maybe process shortcode for users that can edit/manage posts
            global $accessible_reading;
            $process = $accessible_reading->save_shortcode($post_id, $atts['id'], $content);
            if ($process !== false) {
                if ($process['content']) {
                    $accessible_content = $process['content'];
                } elseif ($process['same_as_prev']) {
                    //Retrieve previous content
                    $accessible_content = Settings::get_meta($post_id, 'shortcode_' . $atts['id']);
                }
            }
        } else {
            $accessible_content = Settings::get_meta($post_id, 'shortcode_' . $atts['id']);
        }
        if ($accessible_content) {
            return sprintf(
                '<script>window.AU_AccessibleReading_Default = window.AU_AccessibleReading_Default || "%s";</script><div class="accessible-reading">%s%s%s</div>',
                Settings::get_meta(
                    $post_id,
                    'toggle_switch',
                    Settings::get('toggle_switch', true) //Fallback to the global setting
                ),
                $atts['hide_toggle'] ? '' : (Settings::get('toggle_switch_content', true) ? $this->display_toggle_switch() : ''),
                $this->display_original_content(array(), $content),
                $this->display_accessible_content(array(), $accessible_content)
            );
        }
        return sprintf(
            '<!-- %s -->%s',
            __('No accessible content available for this shortcode content', 'accessible-reading'),
            $content
        );
    }

    /**
     * Display Switch Toggle HTML
     *
     * @since 1.0.0
     * @param array $args An associatve array with keys for `name` (string), `value`
     * (string of `true` or `false`), `yes` (string), `no` (string), `label`, and
     * `reverse` (bool)
     * @return string HTML for the checkbox switch
     */
    private function get_checkbox_switch($args)
    {
        $checked = checked($args['reverse'] ? '0' : '1', $args['value'], false); //Checked attribute, if reversed, compare against the opposite value
        return sprintf(
            '<span class="checkbox-switch" role="switch" aria-checked="%8$s" title="%7$s">
                    <label><span class="screen-reader-text">%7$s</span>
                    <input class="input-checkbox%6$s" type="checkbox" id="%9$s" name="%1$s" %3$s />
                    <span class="checkbox-animate">
                        <span class="checkbox-off" aria-hidden="true">%4$s</span>
                        <span class="checkbox-on" aria-hidden="true">%5$s</span>
                    </span>
                    </label>
                </span>',
            esc_attr($args['name']), //1 - Input name
            esc_attr($args['value']), //2 - Input value
            esc_attr($checked), //3 - checked attribute
            esc_attr($args['no']), //4 - on value
            esc_attr($args['yes']), //5 - off value
            $args['reverse'] ? ' reverse-checkbox' : '', //6 - whether checkbox should be visibly reversed
            esc_attr($args['label']), //7 The label of the checkbox, used for screenreaders
            $checked ? 'true' : 'false', //8 - ARIA checked attribute
            uniqid($args['name']) //9 - unique ID
        );
    }

    //**** Frontend ****//

    /**
     * Enqueue Frontend Styles and Scripts
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        $minified = WP_DEBUG ? '' : '.min'; //Use unminified files if debugging

        //Register the stylesheet
        wp_register_style(
            Settings::$vars['slug'],
            Settings::$vars['url'] . 'assets/styles/accessible-reading' . $minified . '.css',
            array(),
            filemtime(Settings::$vars['path'] . 'assets/styles/accessible-reading' . $minified . '.css')
        );

        //Register the script
        wp_register_script(
            Settings::$vars['slug'],
            Settings::$vars['url'] . 'assets/scripts/accessible-reading' . $minified . '.js',
            array(),
            filemtime(Settings::$vars['path'] . 'assets/scripts/accessible-reading' . $minified . '.js'),
            true
        );
    }

    /**
     * Frontend Display
     *
     * Display the swap button on the frontend of a post in the loop of `the_content()`
     *
     * @since 1.0.0
     * @param string $content. Post content.
     * @return string Modified post content.
     */
    public function display_frontend($content = '')
    {
        if (is_singular(Settings::get_post_types())) {
            $post_id = get_the_ID();
            //Do not continue if disabled at the post level
            if (Settings::post_enabled($post_id)) {
                $accessible_content = Settings::get_meta($post_id, 'content');
                if ($accessible_content) {
                    return sprintf(
                        '<script>window.AU_AccessibleReading_Default = window.AU_AccessibleReading_Default || "%s";</script><div class="accessible-reading post-loop">%s%s%s</div>',
                        Settings::get_meta(
                            $post_id,
                            'toggle_switch',
                            Settings::get('toggle_switch', true) //Fallback to the global setting
                        ),
                        Settings::get('toggle_switch_content', true) ? $this->display_toggle_switch() : '',
                        $this->display_original_content(array(), $content),
                        $this->display_accessible_content(array(), $accessible_content)
                    );
                }
            }
        }
        return $content;
    }
}
