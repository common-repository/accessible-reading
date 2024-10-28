<?php

/**
 * Plugin Settings File
 *
 * @package AuRise\Plugin\AccessibleReading
 */

namespace AuRise\Plugin\AccessibleReading;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use \DateTime;
use \DateTimeZone;
use AuRise\Plugin\AccessibleReading\Utilities;

class Settings
{
    /**
     * The single instance of the class.
     *
     * @var Settings
     * @since 1.0.0
     */
    protected static $_instance = null;

    /**
     * Plugin variables of settings and options
     *
     * @var array
     * @since 1.0.0
     */
    public static $vars = array();

    /**
     * Main Instance
     *
     * Ensures only one instance of is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Settings Main instance.
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
        $basename = plugin_basename(ACCESSIBLEREADING_FILE);
        $path = plugin_dir_path(ACCESSIBLEREADING_FILE);
        $url = plugin_dir_url(ACCESSIBLEREADING_FILE);
        $slug = dirname($basename);
        $slug_underscore = str_replace('-', '_', $slug);
        load_plugin_textdomain($slug, false, $slug . '/languages'); //Translations

        self::$vars = array(

            // Basics
            'name' => __('Accessible Reading', 'accessible-reading'),
            'version' => ACCESSIBLEREADING_VERSION,
            'capability_post' => 'edit_post',
            'capability_settings' => 'manage_options',

            // URLs
            'file' => ACCESSIBLEREADING_FILE,
            'basename' => $basename, // E.g.: "plugin-folder/file.php"
            'path' => $path, // E.g.: "/path/to/wp-content/plugins/plugin-folder/"
            'url' => $url, // E.g.: "https://domain.com/wp-content/plugins/plugin-folder/"
            'admin_url' => admin_url(sprintf('tools.php?page=%s', $slug)), // E.g.: "https://domain.com/wp-admin/tools.php?page=plugin-folder"
            'slug' => $slug, // E.g.: "plugin-folder"
            'slug_underscore' => $slug_underscore, // E.g.: "plugin_folder"
            'prefix' => $slug_underscore . '_', // E.g.: "plugin_folder_"
            'group' => $slug . '-group', // E.g.: "plugin-folder-group"

            //Plugin-specific Settings
            'hook' => $slug_underscore,
            'schedule' => 1,

            //Plugin-specific Options
            'options' => array(
                'settings' => array(
                    'title' => __('Settings', 'accessible-reading'),
                    'options' => array(
                        'api_key' => array(
                            'label' => __('API Key', 'accessible-reading'),
                            'description' => sprintf(
                                '<a href="https://aurisecreative.com/accessible-reading-api-key/" target="_blank" rel="noopener noreferrer">%s</a> %s',
                                __('Get an API key.', 'accessible-reading'),
                                __('Free and paid versions are available.', 'accessible-reading')
                            ),
                            'default' => '',
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => true, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text'
                            )
                        ),
                        'api_plan' => array(
                            'label' => __('Plan', 'accessible-reading'),
                            'description' => __('Select the plan that your API key is subscribed to. This will determine your API request limits. If unsure, choose "Basic."', 'accessible-reading'),
                            'default' => 'basic',
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'select',
                                'required' => 'required',
                                'options' => array(
                                    //Pricing Structure: https://rapidapi.com/bionic-reading-bionic-reading-default/api/bionic-reading1/pricing
                                    'basic' => array(
                                        'label' => __('Basic', 'accessible-reading'), //Plan Name
                                        'daily_request' => 500, //Daily API Request Limit
                                        'char' => 1000 //Character length request limit
                                    ),
                                    'pro' => array(
                                        'label' => __('Pro', 'accessible-reading'), //Plan Name
                                        'daily_request' => 20000, //Daily API Request Limit
                                        'char' => 10000 //Character length request limit
                                    )
                                )

                            )
                        ),
                        'fixation' => array(
                            'label' => __('Fixation', 'accessible-reading'),
                            'description' => __('Define the expression of the letter combinations.', 'accessible-reading'),
                            'default' => 1,
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'info' => __('The fixation calculates how many letters in a world is bolded. A low fixation has fewer bolded letters for the word and is indicated by a higher fixation number. Check out the preview tab for dynamic examples.', 'accessible-reading'),
                            'atts' => array(
                                'type' => 'number',
                                'min' => 1,
                                'max' => 5,
                                'step' => 1,
                                'required' => 'required'
                            )
                        ),
                        'saccade' => array(
                            'label' => __('Saccade', 'accessible-reading'),
                            'description' => __('Define the visual jumps from fixation to fixation.', 'accessible-reading'),
                            'default' => '10',
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'info' => __('The saccade calculates how often a word is chosen for bolded letters. A lot of saccades will have nearly every word with bolded letters, indicated by a higher saccade number. Check out the preview tab for dynamic examples.', 'accessible-reading'),
                            'atts' => array(
                                'type' => 'number',
                                'min' => 10,
                                'max' => 50,
                                'step' => 10,
                                'required' => 'required'
                            )
                        ),
                        'toggle_switch' => array(
                            'label' => __('Default Setting', 'accessible-reading'),
                            'description' => __('Choose the default setting for the toggle switch.', 'accessible-reading'),
                            'default' => '0',
                            'global_override' => false, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'switch',
                                'options' => array(
                                    '0' => array(
                                        'label' => __('Disabled', 'accessible-reading')
                                    ),
                                    '1' => array(
                                        'label' => __('Enabled', 'accessible-reading')
                                    )
                                )
                            )
                        ),
                        'toggle_switch_content' => array(
                            'label' => __('Visibility', 'accessible-reading'),
                            'description' => __('Display or hide the toggle switch that automatically appears at the top of the post content. Only recommended if you use the shortcodes instead.', 'accessible-reading'),
                            'default' => '1',
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'switch',
                                'options' => array(
                                    '0' => array(
                                        'label' => __('Hidden', 'accessible-reading')
                                    ),
                                    '1' => array(
                                        'label' => __('Visible', 'accessible-reading')
                                    )
                                )
                            )
                        ),
                        //Hidden/Internal plugin settings
                        'bulk_update_posts' => array(
                            'default' => '',
                            'atts' => array(
                                'type' => 'hidden'
                            )
                        ),
                        'daily_limit_tracker' => array(
                            'default' => '',
                            'atts' => array(
                                'type' => 'hidden'
                            )
                        ),
                        'daily_limit' => array(
                            'default' => '',
                            'atts' => array(
                                'type' => 'hidden'
                            )
                        ),
                        'processing_bulk' => array(
                            'default' => '0',
                            'atts' => array(
                                'type' => 'hidden'
                            )
                        ),
                        'cancel_bulk' => array(
                            'default' => '',
                            'atts' => array(
                                'type' => 'hidden'
                            )
                        ),
                        //Allowing other post types is coming soon
                        'post_types' => array(
                            'default' => 'post',
                            'atts' => array(
                                'type' => 'hidden'
                            )
                        )
                    )
                ),
                'toggle_switch' => array(
                    'title' => __('Toggle Button Appearance', 'accessible-reading'),
                    'options' => array(
                        'toggle_switch_text_enable' => array(
                            'label' => __('Button Label to Enable', 'accessible-reading'),
                            'description' => __('Change the default button label that tells users to turn accessible reading on.', 'accessible-reading'),
                            'default' => __('Enable Accessible Reading', 'accessible-reading'),
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text'
                            )
                        ),
                        'toggle_switch_text_disable' => array(
                            'label' => __('Button Label to Disable', 'accessible-reading'),
                            'description' => __('Change the default button label that tells users to turn accessible reading off.', 'accessible-reading'),
                            'default' => __('Disable Accessible Reading', 'accessible-reading'),
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text'
                            )
                        ),
                        'toggle_switch_width' => array(
                            'label' => __('Width', 'accessible-reading'),
                            'description' => __('Width of the toggle button, in pixels', 'accessible-reading'),
                            'default' => '285', //Same as stylesheet for English language
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'atts' => array(
                                'type' => 'number',
                                'min' => 0,
                                'step' => 1
                            )
                        ),
                        'toggle_switch_color_off_bg' => array(
                            'label' => __('Disabled State Background Color', 'accessible-reading'),
                            'description' => __('Select a background color for your toggle switch when it is disabled.', 'accessible-reading'),
                            'default' => '#c3c4c7', //Same as stylesheet
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text',
                                'class' => 'au-color-picker'
                            )
                        ),
                        'toggle_switch_color_off_text' => array(
                            'label' => __('Disabled State Text Color', 'accessible-reading'),
                            'description' => __('Select a text color for your toggle switch when it is disabled.', 'accessible-reading'),
                            'default' => '#ffffff', //Same as stylesheet
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text',
                                'class' => 'au-color-picker'
                            )
                        ),
                        'toggle_switch_color_off_ball' => array(
                            'label' => __('Disabled State Circle Color', 'accessible-reading'),
                            'description' => __("Select a color for your toggle switch's ball when it is disabled.", 'accessible-reading'),
                            'default' => '#9a9a9a', //Same as stylesheet
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text',
                                'class' => 'au-color-picker'
                            )
                        ),
                        'toggle_switch_color_on_bg' => array(
                            'label' => __('Enabled State Background Color', 'accessible-reading'),
                            'description' => __('Select a background color for your toggle switch when it is enabled.', 'accessible-reading'),
                            'default' => '#019ecf', //Same as stylesheet
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text',
                                'class' => 'au-color-picker'
                            )
                        ),
                        'toggle_switch_color_on_text' => array(
                            'label' => __('Enabled State Text Color', 'accessible-reading'),
                            'description' => __('Select a text color for your toggle switch when it is enabled.', 'accessible-reading'),
                            'default' => '#ffffff', //Same as stylesheet
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text',
                                'class' => 'au-color-picker'
                            )
                        ),
                        'toggle_switch_color_on_ball' => array(
                            'label' => __('Enabled State Circle Color', 'accessible-reading'),
                            'description' => __("Select a color for your toggle switch's ball when it is enabled.", 'accessible-reading'),
                            'default' => '#1c395d', //Same as stylesheet
                            'global_override' => true, //Allow to be overriden by constant variable in wp-config.php
                            'private' => false, //Don't display value in dashboard if set in wp-config.php
                            'atts' => array(
                                'type' => 'text',
                                'class' => 'au-color-picker'
                            )
                        )
                    )
                )
            ),

            //Post-level Options
            'post_options' => array(
                'enabled' => array(
                    'label' => __('Allow Processing', 'accessible-reading'),
                    'description' => __('Opt this post out from being processed for accessible reading. This will also prevent the toggle switch from being shown to visitors.', 'accessible-reading'),
                    'default' => 1,
                    'atts' => array(
                        'type' => 'select',
                        'required' => 'required',
                        'options' => array(
                            '1' => array(
                                'label' => __('Enabled', 'accessible-reading')
                            ),
                            '0' => array(
                                'label' => __('Disabled', 'accessible-reading')
                            )
                        )
                    )
                ),
                'toggle_switch' => array(
                    'label' => __('Toggle Switch Default Setting', 'accessible-reading'),
                    'description' => __('Choose the default setting for the toggle switch. This will have no effect if processing is disabled for this post.', 'accessible-reading'),
                    'default' => 'global',
                    'atts' => array(
                        'type' => 'select',
                        'required' => 'required',
                        'options' => array(
                            'global' => array(
                                'label' => __('Use Global Setting', 'accessible-reading')
                            ),
                            '0' => array(
                                'label' => __('Disabled', 'accessible-reading')
                            ),
                            '1' => array(
                                'label' => __('Enabled', 'accessible-reading')
                            )
                        )
                    )
                ),
                // 'accessible_content' => array(
                //     'label' => __('Preview/Edit Accessible Content', 'accessible-reading'),
                //     'description' => __('If the API has added strange formatting, you can fix it here.', 'accessible-reading'),
                //     'default' => '',
                //     'atts' => array(
                //         'type' => 'richtext'
                //     )
                // ),
                // 'overwrite_edits' => array(
                //     'label' => __('Overwrite Edits', 'accessible-reading'),
                //     'description' => __('When disabled, content will not automatically be updated if you have edited it above. Enable this to overwrite the manual edits.', 'accessible-reading'),
                //     'default' => '0',
                //     'atts' => array(
                //         'type' => 'select',
                //         'options' => array(
                //             '0' => array(
                //                 'label' => __('Disabled', 'accessible-reading')
                //             ),
                //             '1' => array(
                //                 'label' => __('Enabled', 'accessible-reading')
                //             )
                //         )
                //     )
                // )
            )
        );

        //Plugin Setup
        add_action('admin_init', array($this, 'register_settings')); //Register plugin option settings
        add_action('admin_menu', array($this, 'admin_menu')); //Add admin page link in WordPress dashboard
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts')); //Enqueue styles/scripts for admin page
        add_filter('plugin_action_links_' . $basename, array($this, 'plugin_links')); //Add link to admin page from plugins page
        add_action('wp_ajax_accessible_reading_check_bulk_update', array($this, 'is_processing_bulk_js')); //Add AJAX call for logged in users to bulk save from admin page
    }

    //**** Plugin Settings ****//

    /**
     * Register Plugin Settings
     *
     * @since 1.0.0
     */
    public function register_settings()
    {
        foreach (self::$vars['options'] as $option_group_name => $group) {
            $option_group = self::$vars['prefix'] . $option_group_name;
            //Register the section
            add_settings_section(
                $option_group, //Slug-name to identify the section. Used in the `id` attribute of tags.
                $group['title'], //Formatted title of the section. Shown as the heading for the section.
                array($this, 'display_plugin_setting_section'), //Function that echos out any content at the top of the section (between heading and fields).
                self::$vars['slug'] //The slug-name of the settings page on which to show the section.
            );

            //Register the individual settings in the section
            foreach ($group['options'] as $setting_name => $setting_data) {
                $option_name = self::$vars['prefix'] . $setting_name;
                $input_type = $setting_data['atts']['type'];
                $registration_args = array();
                switch ($input_type) {
                    case 'switch':
                    case 'checkbox':
                        $type = 'integer';
                        $registration_args['sanitize_callback'] = array($this, 'sanitize_setting_bool');
                        break;
                    case 'number':
                        $type = 'number';
                        $registration_args['sanitize_callback'] = array($this, 'sanitize_setting_number');
                        break;
                    case 'text':
                        $type = 'string';
                        if (strpos(Utilities::array_has_key('class', $setting_data['atts']), 'au-color-picker') !== false) {
                            $registration_args['sanitize_callback'] = array($this, 'sanitize_setting_color');
                        } else {
                            $registration_args['sanitize_callback'] = 'sanitize_text_field';
                        }
                        break;
                    default:
                        $type = 'string';
                        $registration_args['sanitize_callback'] = 'sanitize_text_field';
                        break;
                }
                $registration_args['type'] = $type; //Valid values are string, boolean, integer, number, array, and object
                $registration_args['description'] = $setting_name;
                $registration_args['default'] = Utilities::array_has_key('default', $setting_data);

                //Register the setting
                register_setting($option_group, $option_name, $registration_args);

                //Add the field to the admin settings page (excluding the hidden ones)
                if ($input_type != 'hidden') {
                    $atts = array(
                        'type' => $input_type, //Input type
                        'type_option' => 'string', //Option type
                        'name' => $option_name,
                        'id' => $option_name,
                        'default' => $registration_args['default'],
                        'value' => get_option($option_name, $registration_args['default']), //The currently selected value (or default if not selected)
                        'label' => $setting_data['label'],
                        'description' => Utilities::array_has_key('description', $setting_data),
                        'required' => Utilities::array_has_key('required', $setting_data['atts']),
                        'class' => Utilities::array_has_key('class', $setting_data['atts']),
                        'global' => Utilities::array_has_key('global_override', $setting_data) ? strtoupper($option_name) : '', //Name of constant variable should it exist
                        'private' => Utilities::array_has_key('private', $setting_data)
                    );
                    switch ($input_type) {
                        case 'select':
                            $atts['options'] = $setting_data['atts']['options'];
                            break;
                        case 'checkbox':
                        case 'switch':
                            $atts['checked'] = checked(1, $atts['value'], false);
                            $atts['reverse'] = Utilities::array_has_key('reverse', $setting_data['atts']);
                            $atts['yes'] = Utilities::array_has_key('yes', $setting_data['atts'], __('On', 'accessible-reading'));
                            $atts['no'] = Utilities::array_has_key('no', $setting_data['atts'], __('Off', 'accessible-reading'));
                            break;
                        case 'number':
                            $atts['min'] = Utilities::array_has_key('min', $setting_data['atts']);
                            $atts['max'] = Utilities::array_has_key('max', $setting_data['atts']);
                            $atts['step'] = Utilities::array_has_key('step', $setting_data['atts']);
                            //Purposely not breaking here
                        default:
                            $atts['placeholder'] =  esc_attr(Utilities::array_has_key('placeholder', $setting_data['atts']));
                            break;
                    }
                    add_settings_field(
                        $option_name, //ID
                        esc_attr($setting_data['label']), //Title
                        array($this, 'display_plugin_setting'), //Callback (should echo its output)
                        self::$vars['slug'], //Page
                        $option_group, //Section
                        $atts //Attributes
                    );
                }
            }
        }
    }

    /**
     * Sanitize plugin options for boolean fields
     *
     * @since 2.1.0
     * @param array $fields
     */
    public function sanitize_setting_bool($value)
    {
        return $value ? 1 : 0;
    }

    /**
     * Sanitize plugin options for number fields
     *
     * @since 2.1.0
     * @param array $fields
     */
    public function sanitize_setting_number($value)
    {
        return is_numeric($value) ? $value : '';
    }

    /**
     * Sanitize plugin options for color picker fields
     *
     * @since 2.1.0
     * @param array $fields
     */
    public function sanitize_setting_color($value)
    {

        $value = sanitize_text_field($value);
        if ($this->validate_color($value)) {
            return $value;
        }
        return '';
    }

    /**
     * Function that will check if value is a valid HEX color.
     * @since 2.1.0
     */
    private function validate_color($value)
    {
        // if user insert a HEX color with #
        if (preg_match('/^#[a-f0-9]{6}$/i', $value)) {
            return true;
        }
        return false;
    }

    /**
     * Register Plugin Setting Section Callback
     *
     * @since 1.0.0
     */
    public function display_plugin_setting_section()
    {
        //Do nothing
    }

    /**
     * Display plugin setting input in admin dashboard
     *
     * Callback for `add_settings_field()`
     *
     * @since 1.0.0
     * @param array $args
     */
    public function display_plugin_setting($args = array())
    {
        /**
         * Variables that are already escaped:
         * type, name, id, value, label, required, global, private, checked, min, max, step, placeholder
         */
        if ($args['global'] && defined($args['global'])) {
            //Display constant values set in wp-config.php
            if ($args['private']) {
                //Don't reveal if private
                printf(
                    '<input readonly disabled type="password" value="**********" class="%s" />',
                    esc_attr($args['class'])
                );
            } else {
                //Not editable
                printf(
                    '<input readonly disabled type="text" value="%s" class="%s" />',
                    esc_attr(constant($args['global'])),
                    esc_attr($args['class'])
                );
            }
        } else {
            //Render the setting
            $input_value = $args['value'];
            switch ($args['type']) {
                case 'hidden':
                    //Silence is golden
                    break;
                case 'select':
                    printf(
                        '<select id="%s" name="%s" class="%s" data-default=%s"%s />',
                        esc_attr($args['id']),
                        esc_attr($args['name']),
                        esc_attr($args['class']),
                        esc_attr($args['default']),
                        esc_attr($args['required'] ? ' required="required"' : '')
                    );
                    foreach ($args['options'] as $key => $value) {
                        $option_value = esc_attr($key);
                        if (is_array($value)) {
                            $value = $value['label'];
                        }
                        printf(
                            '<option value="%1$s"%3$s>%2$s</option>',
                            esc_html($option_value),
                            esc_attr($value),
                            $input_value == $option_value ? ' selected' : ''
                        );
                    }
                    echo ('</select>');
                    break;
                case 'checkbox':
                    printf(
                        '<input type="%s" id="%s" name="%s" class="%s" data-default="%s" %s />',
                        esc_attr($args['type']),
                        esc_attr($args['id']),
                        esc_attr($args['name']),
                        esc_attr($args['class']),
                        esc_attr($args['default']),
                        esc_attr($args['checked'])
                    );
                    break;
                case 'switch':
                    $value = $args['value'];
                    $reverse = esc_attr($args['reverse']);
                    printf(
                        '<span class="checkbox-switch %8$s">
                                <input type="hidden" id="%1$s" name="%1$s" value="%2$s" data-default="%9$s" />
                                <input class="input-checkbox%7$s" type="checkbox" id="%1$s_check" name="%1$s_check" %3$s />
                                <span class="checkbox-animate">
                                    <span class="checkbox-off">%4$s</span>
                                    <span class="checkbox-on">%5$s</span>
                                </span>
                            </span>
                            <label for="%1$s_check"><span class="note">%6$s</span></label>',
                        $args['name'], //1 - Input name
                        $value, //2 - Input value
                        checked($reverse ? '0' : '1', $value, false), //3 - Checked attribute, if reversed, compare against the opposite value
                        esc_attr($args['no']), //4 - on value
                        esc_attr($args['yes']), //5 - off value
                        $args['label'], //6 - label
                        $reverse ? ' reverse-checkbox' : '', //7 - whether checkbox should be visibly reversed
                        esc_attr($args['class']), //8 - additional classes
                        esc_attr($args['default']) //9 Default value
                    );
                    break;
                case 'number':
                    printf(
                        '<input type="%s" id="%s" name="%s" value="%s" data-default="%s" placeholder="%s" class="%s"%s%s%s%s />',
                        $args['type'],
                        $args['id'],
                        $args['name'],
                        $input_value,
                        esc_attr($args['default']),
                        $args['placeholder'],
                        esc_attr($args['class']),
                        $args['required'] ? ' required="required"' : '',
                        $args['min'] ? sprintf(' min="%s"', $args['min']) : '',
                        $args['max'] ? sprintf(' max="%s"', $args['max']) : '',
                        $args['step'] ? sprintf(' step="%s"', $args['step']) : ''
                    );
                    break;
                default:
                    printf(
                        '<input type="%s" id="%s" name="%s" value="%s" data-default="%s" placeholder="%s" class="%s"%s />',
                        $args['type'],
                        $args['id'],
                        $args['name'],
                        $input_value,
                        $args['default'],
                        $args['placeholder'],
                        esc_attr($args['class']),
                        $args['required'] ? ' required="required"' : ''
                    );
                    break;
            }
        }
        if ($args['description']) {
            printf('<br /><small>%s</small>', $args['description']);
        }
    }

    /**
     * Get Option Key for Settings
     *
     * @since 1.1.0
     * @return string $id With or without a prefix, get the option name and ID
     * @return array an associative array with `id` and `name` properties
     */
    private static function get_key($id)
    {
        $return = array(
            'id' => '',
            'name' => ''
        );
        if (strpos($id, self::$vars['prefix']) === 0) {
            //Prefix is included
            $return['id'] = $id; //No change, keep prefix in ID
            $return['name'] = str_replace(self::$vars['prefix'], '', $id); //Remove prefix from name
        } else {
            //Prefix is not included
            $return['name'] = $id; //No change, no prefix in name
            $return['id'] = self::$vars['prefix'] . $id; //Add prefix to ID
        }
        return $return;
    }

    /**
     * Get Plugin Setting
     *
     * This checks if a constant value was defined to override it and returns that.
     *
     * @since 1.0.0
     * @param string $id Option ID, including prefix
     * @param bool $value_only If true, returns just the value of the setting. Otherwise,
     * it returns an associatve array. Default is true.
     * @return string|array An associative array with the keys `value` and `constant`
     * unless $value_only was true, then it returns just the value.
     */
    public static function get($id, $value_only = true, $group = '')
    {
        $return = array(
            'value' => '',
            'constant' => false
        );
        $setting = self::get_key($id);
        //$const_name = Utilities::array_has_key('global_override', self::$vars['options'][$setting['name']]) ? strtoupper($setting['id']) : '';
        $group = $group ? $group : 'settings';
        $const_name = Utilities::array_has_key('global_override', self::$vars['options'][$group]['options'][$setting['name']]) ? strtoupper($setting['id']) : '';
        if ($const_name && defined($const_name)) {
            //Display the value overriden by the constant value
            $return['value'] = constant($const_name);
            $return['constant'] = true;
        } else {
            //$return['value'] = esc_attr(get_option($setting['id'], esc_attr(self::$vars['options'][$setting['name']]['default'])));
            $return['value'] = get_option($setting['id'], self::$vars['options'][$group]['options'][$setting['name']]['default']);
            if ($group == 'toggle_switch' && !$return['value'] && !is_numeric($return['value']) && !is_bool($return['value'])) {
                //Always have a default value if not set for toggle switch appearance
                $return['value'] = self::$vars['options'][$group]['options'][$setting['name']]['default'];
            }
        }
        //Utilities::debug_log($return, 'Getting Setting [' . $setting['id'] . ']');
        if ($value_only) {
            return $return['value'];
        }
        return $return;
    }

    /**
     * Set Plugin Setting
     *
     * @since 1.1.0
     * @param string $id With or without the prefix
     * @param mixed $value
     * @return bool True on success, false on failure
     */
    public static function set($id, $value)
    {
        $setting = self::get_key($id);
        $updated = update_option($setting['id'], $value);
        // Utilities::debug_log($value, 'The plugin setting [' . $setting['id'] . '] ' . ($updated ? 'updated successfully' : 'failed to be updated! (it is possible that it is because it is the same as the previous value)'));
        return $updated;
    }

    //**** Plugin Management Page ****//

    /**
     * Add Admin Page.
     *
     * Adds the admin page to the WordPress dashboard under "Tools".
     *
     * @since 1.0.0
     */
    public function admin_menu()
    {
        add_management_page(
            self::$vars['name'],
            self::$vars['name'],
            self::$vars['capability_settings'],
            self::$vars['slug'],
            array(&$this, 'admin_page')
        );
    }

    /**
     * Plugin Links
     *
     * Links to display on the plugins page.
     *
     * @since 1.0.0
     * @param array $links
     * @return array A list of links
     */
    public function plugin_links($links)
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            self::$vars['admin_url'],
            __('Settings', 'accessible-reading')
        );
        array_unshift($links, $settings_link);
        return $links;
    }

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
        // Load only on our plugin page (a subpage of "Tools")
        if ($hook == 'tools_page_' . self::$vars['slug']) {
            $jQueryVersion = wp_scripts()->registered['jquery-ui-core']->ver;

            //Plugin Stylesheets
            wp_register_style(
                self::$vars['prefix'] . 'jquery-ui',
                'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $jQueryVersion . '/themes/base/jquery-ui.css'
            );

            wp_register_style(
                self::$vars['prefix'] . 'layout',
                self::$vars['url'] . 'assets/styles/pseudo-bootstrap.css',
                array(),
                filemtime(self::$vars['path'] . 'assets/styles/pseudo-bootstrap.css')
            );
            wp_enqueue_style(
                self::$vars['prefix'] . 'dashboard',
                self::$vars['url'] . 'assets/styles/admin-dashboard.css',
                array(
                    self::$vars['prefix'] . 'jquery-ui', //jQuery UI for accordions
                    'wp-color-picker', //WordPress Color Picker
                    self::$vars['prefix'] . 'layout' //Pseudo bootstrap
                ),
                filemtime(self::$vars['path'] . 'assets/styles/admin-dashboard.css')
            );
            wp_enqueue_style(
                self::$vars['slug'],
                self::$vars['url'] . 'assets/styles/accessible-reading.css',
                array(self::$vars['prefix'] . 'dashboard'),
                filemtime(self::$vars['path'] . 'assets/styles/accessible-reading.css')
            );
            $inline_styles = self::dynamic_toggle_switch_styles();
            if ($inline_styles) {
                wp_add_inline_style(self::$vars['slug'], $inline_styles);
            }

            //Plugin Scripts
            wp_enqueue_script(
                self::$vars['prefix'] . 'dashboard',
                self::$vars['url'] . 'assets/scripts/admin-dashboard.js',
                array(
                    'jquery', //jQuery
                    'jquery-ui-accordion', //jQuery UI for accordions
                    'wp-color-picker' //WordPress Color Picker
                ),
                filemtime(self::$vars['path'] . 'assets/scripts/admin-dashboard.js'),
                true
            );
            wp_localize_script(
                self::$vars['prefix'] . 'dashboard',
                'au_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'text' => array(
                        'preview_on' => __('Disable Accessible Reading', 'accessible-reading'), //Same as default setting
                        'preview_off' => __('Enable Accessible Reading', 'accessible-reading'), //Same as default setting
                        'processing_completed' => __('The bulk data processing has been completed.', 'accessible-reading'),
                        'processing_cancelled' => __('Processing canceled.', 'accessible-reading')
                    )
                )
            );
        }
    }

    /**
     * Get the Toggle Switch Styles
     *
     * @since 2.1.0
     * @return string the inside of a `<style>` tag if custom options were found. Empty string otherwise.
     */
    public static function dynamic_toggle_switch_styles()
    {
        $group = 'toggle_switch';
        $styles = array();
        foreach (array(
            'width',
            'color_on_bg',
            'color_on_ball',
            'color_on_text',
            'color_off_bg',
            'color_off_ball',
            'color_off_text'
        ) as $att) {
            $value = self::get('toggle_switch_' . $att, true, $group);
            if ($value) {
                $styles[] = sprintf(
                    '--au-accessible-reading-toggle-switch-%s:%s%s;',
                    str_replace('_', '-', $att),
                    $value,
                    is_numeric($value) ? 'px' : ''
                );
            }
        }
        if (count($styles)) {
            return sprintf('.accessible-reading-toggle{%s}', implode('', $styles));
        }
        return '';
    }

    /**
     * Display Admin Page
     *
     * HTML markup for the WordPress dashboard admin page for managing this plugin's settings.
     *
     * @since 1.0.0
     */
    public function admin_page()
    {
        //Prevent unauthorized users from viewing the page
        if (!current_user_can(self::$vars['capability_settings'])) {
            return;
        }
        self::get_daily_limit(); //Run once to reset counter if last call was over 24 hours ago
        load_template(self::$vars['path'] . 'templates/dashboard-admin.php', true, array(
            'plugin_settings' => self::$vars,
            'has_api_configured' => !empty(self::get(self::$vars['prefix'] . 'api_key', true)),
            'daily_limit_tracker' => self::get_api_request_tracker(),
            'daily_limit_within' => self::is_within_daily_limit(),
            'is_processing_bulk' => self::is_processing_bulk()
        ));
    }

    /**
     * Set Single Meta Data
     *
     * @since 1.3.0
     * @param int $post_id Post ID
     * @param string $key The meta key to set
     * @param mixed $value The meta value to set
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @return mixed The meta key value that was set
     */
    public static function set_meta($post_id, $key, $value, $post_type = 'post')
    {
        //update_metadata($post_type, $post_id, $this->settings['prefix'] . $key, $value);
        update_post_meta($post_id, self::$vars['prefix'] . $key, $value);
    }

    /**
     * Get Single Meta Data
     *
     * @since 1.3.0
     * @param int $post_id Post ID
     * @param string $key Optional. The meta key to retrieve. By default, returns data for all keys.
     * @param mixed $default Optional. The default value to return if not set. Default is an empty string.
     * @param string $post_type Optional. The post type to retrieve. Default is `post`.
     * @return mixed The value of the meta field or default if it's not explicity false as a bool or integer.
     */
    public static function get_meta($post_id, $key = '', $default = '', $post_type = 'post')
    {
        if (!$post_type || !is_numeric($post_id) || $post_id <= 0) {
            return $default;
        }
        $key = $key ? self::$vars['prefix'] . $key : '';
        $value = get_post_meta($post_id, $key, true);
        //$value = get_metadata($post_type, $post_id, $key, true);
        //If value is set or if it is set to some falsey things on purpose
        if ($value || is_bool($value) || is_numeric($value)) {
            return $value;
        }
        return $default;
    }

    /** Plugin Specific Settings */

    /**
     * Get Allowed Post Types
     *
     * @since 1.3.0
     * @return array a sequential array of post types
     */
    public static function get_post_types()
    {
        $types = self::get('post_types', true);
        if (!$types) {
            $types = self::$vars['options']['settings']['options']['post_types']['default'];
        }
        return explode(',', $types);
    }

    /**
     * Get Bionic Reading Plan
     *
     * @since 1.2.0
     * @param string $plan Optional. Options are `basic` or `pro`. If left empty, it will look it up.
     * @return string `basic` or `pro`
     */
    public static function get_plan($plan = false)
    {
        $plan = $plan ? $plan : self::get('api_plan');
        if (Utilities::array_has_key($plan, self::$vars['options']['settings']['options']['api_plan']['atts']['options'])) {
            return $plan;
        }
        return 'basic'; //Default to the basic plan
    }

    /**
     * Get API Character Processing Limit
     *
     * Based on Bionic Reading plan
     *
     * @since 1.1.0
     * @param string $plan Optional. Options are `basic` or `pro`. If left empty, it will look it up.
     * @return int The maximum character processing limit based on the API plan setting
     */
    public static function get_character_limit($plan = false)
    {
        $plan = self::get_plan($plan);
        $buffer = 50; // add an arbitrary buffer for when we chunk it later, we'll separate by word, not letter
        return self::$vars['options']['settings']['options']['api_plan']['atts']['options'][$plan]['char'] - $buffer;
    }

    /**
     * Get Daily API Request Limit
     *
     * Returns the daily limit that was returned from a recent API request or is based on the configured Bionic Reading plan
     *
     * @since 1.1.0
     * @param string $plan Optional. Options are `basic` or `pro`. If left empty, it will look it up.
     * @return int The daily request limit based on the API plan setting
     */
    public static function get_daily_limit($plan = false)
    {
        //Check if setting was saved in options from API first
        $limit = self::get('daily_limit', true);
        if ($limit) {
            return $limit;
        }
        $plan = self::get_plan($plan);
        return self::$vars['options']['settings']['options']['api_plan']['atts']['options'][$plan]['daily_request'];
    }

    /**
     * Get Daily Limit Tracker
     *
     * @since 1.1.0
     * @return array an associative array with `count` (int), `total` (int), and `remaining` (int) keys
     */
    public static function get_api_request_tracker()
    {
        //Return saved tracker from settings
        $tracker = self::get('daily_limit_tracker', true);
        if ($tracker) {
            $tracker = json_decode(html_entity_decode($tracker), true, 512, JSON_HEX_QUOT);
            if (is_array($tracker)) {
                //Utilities::debug_log($tracker, 'Saved Tracker');
                //For backwards compatibility before version 2.0.0, ensure all the tracking fields are here
                if (Utilities::array_has_key('remaining', $tracker, false) === false || Utilities::array_has_key('total', $tracker, false) === false) {
                    $daily_limit = self::get_daily_limit();
                    return self::update_api_request_tracker($daily_limit - $tracker['count'], $daily_limit);
                }
                return $tracker;
            }
        }
        return self::update_api_request_tracker('reset');
    }

    /**
     * Update Daily Limit Tracker
     *
     * @since 2.0.0
     * @param int $remaining The total number of API requests remaining for today
     * @param int $daily_limit Optional. The total number of API requests allowed for today
     * @param int $ping Optional. 0 if the request is not the result of a ping, 1 if it is.
     * @return array
     */
    public static function update_api_request_tracker($remaining = '', $daily_limit = '', $ping = 0)
    {
        $tracker = array(
            'count' => 0, //Number of requests made
            'total' => 0, //Total number of requests allowed to be made today
            'remaining' => 0, //Number of requests remaining today
            'last_modified' => '', //Date of last ping or reset
            'ping' => $ping
        );
        if ($daily_limit && is_numeric($daily_limit)) {
            $tracker['total'] = intval($daily_limit);
            self::set('daily_limit', $tracker['total']);
        } else {
            $tracker['total'] = self::get_daily_limit();
        }
        if ($ping || $remaining === 'reset') {
            $now = new DateTime('now', new DateTimeZone(wp_timezone_string()));
            $tracker['last_modified'] = $now->format('c'); //ISO 8601 date (ex: 2022-10-09T13:33:59-04:00)
        }
        if ($remaining === 'reset') {
            $tracker['remaining'] = $tracker['total']; //Reset remaining to total amount
        } elseif ($remaining && is_numeric($remaining)) {
            $tracker['remaining'] = intval($remaining);
        }
        $tracker['count'] = $tracker['total'] - $tracker['remaining'];
        //Save it, looks something like {&quot;count&quot;:1,&quot;total&quot;:500,&quot;remaining&quot;:499}
        self::set('daily_limit_tracker', json_encode($tracker, JSON_HEX_QUOT));
        return $tracker;
    }

    /**
     * Check if Within Daily API Request Limit
     *
     * @since 2.0.0
     * @param int $buffer Optional. For automated processes, we add a buffer to not use up all of today's requests.
     * @param int $check Optional. Pass a number of proposed requests to see if you'd still be within the limit.
     * @return bool Returns true if we can make more API requests today. False otherwise.
     */
    public static function is_within_daily_limit($buffer = 0, $check = 0)
    {
        $tracker = self::get_api_request_tracker();
        $date = Utilities::array_has_key('last_modified', $tracker, 0);
        if (!$date || ($date && self::more_than_day($date))) {
            //No date was set, it's never been initialized, reset to zero
            // OR there is a date set and it's been more than a day, reset to zero
            self::update_api_request_tracker('reset');
            $tracker = self::get_api_request_tracker();
        }
        return $tracker['remaining'] - $buffer - $check > 0;
    }

    /**
     * Date Older than 1 Day
     *
     * @since 2.1.1
     * @param string $date ISO 8601 date with timezone data
     * @return bool Returns true if date is older than or equal to $day
     */
    private static function more_than_day($date)
    {
        $dateTime = new DateTime($date); //Date to compare
        $now = new DateTime('-1 days', new DateTimeZone(wp_timezone_string())); //24 hours ago
        return $dateTime->getTimestamp() <= $now->getTimestamp();
    }

    /**
     * Check if post is enabled for processing
     *
     * @since 2.0.0
     * @param $post_id
     * @param array $args Associative array of posted data
     * @return bool True if enabled, false otherwise
     */
    public static function post_enabled($post_id, $args = array())
    {
        if (!Utilities::array_has_key(self::$vars['prefix'] . 'enabled', $args, self::$vars['post_options']['enabled']['default'])) {
            return false;
        }
        return self::get_meta($post_id, 'enabled', self::$vars['post_options']['enabled']['default']);
    }

    /**
     * Check if Processing Bulk Operation
     *
     * @since 1.3.0
     * @return bool If true, either it is actively processing or an event is scheduled to be processed.
     */
    public static function is_processing_bulk()
    {
        return self::get('processing_bulk') || Utilities::next_scheduled(self::$vars['hook']);
    }

    /**
     * AJAX: Check if Processing Bulk Operation
     *
     * A simple pulse check from admin-dashboard.js to see if it is still processing
     *
     * @since 2.0.0
     */
    public function is_processing_bulk_js()
    {
        wp_die(self::is_processing_bulk() ? 'yes' : 'no');
    }
}
