=== Accessible Reading ===
Contributors: tessawatkinsllc
Donate link: https://just1voice.com/donate/
Tags: bionic reading, accessibility, accessible, neurodiversity, neurodivergent, accommodation, disability, legible reading, wcag, ada, font size, bold font, brain reading, speed reading
Requires at least: 4.6
Tested up to: 6.2
Stable tag: 2.1.1
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Make your website more accessible by offering a reading accommodation to your disabled and neurodivergent readers by adding Bionic Reading® as a font choice!

== Description ==

Your brain reads faster than your eyes! We humans have an internal dictionary of learned words and reading the first few letters can be enough to recognize whole words.

Bionic Reading® is a new method facilitating the reading process by guiding the eyes through text with artificial fixation points. As a result, the reader is only focusing on the highlighted initial letters and lets the bran center complete the word. In a digital world dominated by shallow forms of reading, Bionic Reading® aims to encourage more in-depth reading and understanding of written content.

Learn More about [Bionic Reading®](https://bionic-reading.com/).

This WordPress plugin is not a product of Bionic Reading®. It was developed by an independent and disabled developer that wants to bring more accessibility to the digital space. This plugin uses the official Bionic Reading API to generate accessible text so an [API key](https://aurisecreative.com/accessible-reading-api-key/) is needed after plugin activation. Free and paid plans are available.

== Installation ==

There are three (3) ways to install my plugin: automatically, upload, or manually.

= Install Method 1: Automatic Installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser.

1. Log in to your WordPress dashboard.
1. Navigate to **Plugins > Add New**.
1. Where it says “Keyword” in a dropdown, change it to “Author”
1. In the search form, type “TessaWatkinsLLC” (results may begin populating as you type but my plugins will only show when the full name is there)
1. Once you’ve found my plugin in the search results that appear, click the **Install Now** button and wait for the installation process to complete.
1. Once the installation process is completed, click the **Activate** button to activate my plugin.
1. After this plugin is activated, see below for additional instructions for setup.

= Install Method 2: Upload via WordPress Admin =

This method involves is a little more involved. You don’t need to leave your web browser, but you’ll need to download and then upload the files yourself.

1. [Download my plugin](https://wordpress.org/plugins/accessible-reading/) from WordPress.org; it will be in the form of a zip file.
1. Log in to your WordPress dashboard.
1. Navigate to **Plugins > Add New**.
1. Click the **Upload Plugin** button at the top of the screen.
1. Select the zip file from your local file system that was downloaded in step 1.
1. Click the **Install Now** button and wait for the installation process to complete.
1. Once the installation process is completed, click the **Activate** button to activate my plugin.
1. After this plugin is activated, see below for additional instructions for setup.

= Install Method 3: Manual Installation =

This method is the most involved as it requires you to be familiar with the process of transferring files using an SFTP client.

1. [Download my plugin](https://wordpress.org/plugins/accessible-reading/) from WordPress.org; it will be in the form of a zip file.
1. Unzip the contents; you should have a single folder named `accessible-reading`.
1. Connect to your WordPress server with your favorite SFTP client.
1. Copy folder from step 2 to the `/wp-content/plugins/` folder in your WordPress directory. Once the folder and all of its files are there, installation is complete.
1. Now log in to your WordPress dashboard.
1. Navigate to **Plugins > Installed Plugins**. You should now see my plugin in your list.
1. Click the **Activate** button under my plugin to activate it.
1. After this plugin is installed and activated, see below for additional instructions for setup.

= After Plugin Installation and Activation =

1. Visit [Bionic Reading® on Rapid API](https://aurisecreative.com/accessible-reading-api-key/) to get an API key. Free and paid versions are available.
1. Navigate to **Tools > Accessible Reading** to add the API key to your settings.
1. Optionally modify the fixation and saccade to suit your preferences.
1. Creating a new post or updating an existing one will generate the accessible content for that post. Once a post has accessible content, a toggle button will appear on the frontend for site visitors to use.

== Screenshots ==

1. The frontend of the post page with the accessible reading button toggled on
2. The frontend of the post page with the accessible reading button toggled off
3. Plugin settings screen

== Frequently Asked Questions ==

= Why do I need an API key? =

This plugin uses the [Bionic Reading® API](https://aurisecreative.com/accessible-reading-api-key/) to generate accessible text and it requires each user to have a unique API key.

= I have an API key, how do I use it? =

You can set the API key for the plugin in two ways.

The first way is adding it in the plugin settings. While logged into WordPress, navigate to Tools > Accessible Reading and then under the Settings tab, you'll see a text field for API Key to copy/pase it into. Press the Save All Settings button at the bottom.

The second way is defining the `ACCESSIBLE_READING_API_KEY` constant variable in your `wp-config.php` file and setting it's value to your API key. For multisite installations, this will set all of those sites to use the same API key. Example usage:
`define('ACCESSIBLE_READING_API_KEY','your-api-key-goes-here');`

= What is the `accessible_reading` shortcode and how do I use it?  =

The `accessible_reading` shortcode allows you to add accessible text with a toggle switch anywhere on your WordPress website. To ensure your shortcode's accessible content is automatically generated, a user with `edit post` capabilities must preview the page using the shortcode at least once while you have enough API requests for the day.

To use it, simply wrap the text you want to make accessible with the shortcode while also adding an `id` attribute. For example:
`&#91;accessible_reading id="foo"&#93;This content will become accessible!&#91;/accessible_reading&#93;`

This shortcode will also add the toggle switch to enable/disable the accessible content automatically, so if you're using it in multiple locations and want just one switch, set the `hide_toggle` attribute to `1`  to disable it like this:
`&#91;accessible_reading id="bar" hide_toggle="1"&#93;This content will become accessible but <strong>without showing a toggle!</strong>&#91;/accessible_reading&#93;`

This shortcode saves it's content to a post's meta data, so if you're using it in a global area of your website like your header, footer, or sidebar, you should assign the shortcode a post or page ID to save it once to avoid saving it to every post or page it's visible on like this:
`&#91;accessible_reading id="david" post_id="23"&#93;This content will always save and pull it's accessible content from post ID 23&#91;/accessible_reading&#93;`

If you want to temporarily disable the automatic processing without removing the shortcode, you can set the `disable` attribute to `1` like this:
`&#91;accessible_reading id="bowie" disabled="1"&#93;This content won't be accessible, nor will it have a toggle. <em>Yet</em>.&#91;/accessible_reading&#93;`

= What is the `accessible_reading_toggle` shortcode and how do I use it? =

The `accessible_reading_toggle` shortcode allows you to add a toggle switch anywhere on your WordPress website. To use it, simply place the self-closing shortcode anywhere on your WordPress website. For example:
`&#91;accessible_reading_toggle/&#93;`

= What is the `accessible_reading_content` shortcode and how do I use it? =

The `accessible_reading_content` shortcode allows you to add pre-made accessible content anywhere on your WordPress website that a toggle switch will display when turned on.

To use it, wrap the accessible content with the shortcode. For example:
`&#91;accessible_reading_content&#93;&lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;Lor&lt;/b&gt;em&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;ips&lt;/b&gt;um&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;dol&lt;/b&gt;or&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;s&lt;/b&gt;it&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;am&lt;/b&gt;et.&lt;/span&gt;&#91;/accessible_reading_content&#93;`

You can optionally add additional HTML classes to the outermost wrapper by setting the value to the `classes` attribute. If setting multiple classes, they should be separated by a single space. For example:
`&#91;accessible_reading_content classes="custom-class-1 custom-class-2"&#93;&lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;Lor&lt;/b&gt;em&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;ips&lt;/b&gt;um&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;dol&lt;/b&gt;or&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;s&lt;/b&gt;it&lt;/span&gt; &lt;span class="bionic-w bionic"&gt;&lt;b class="bionic-b bionic"&gt;am&lt;/b&gt;et.&lt;/span&gt;&#91;/accessible_reading_content&#93;`

= What is the `accessible_reading_original_content` shortcode and how do I use it? =

To complement the `accessible_reading_content` shortcode, the `accessible_reading_original_content` shortcode allows you to add the original content that a toggle switch will hide when accessible reading is turned on.

To use it, simply wrap the original content with the shortcode. For example:
`&#91;accessible_reading_original_content&#93;Lorem ipsum dolor sit amet.&#91;/accessible_reading_original_content&#93;`

You can optionally add additional HTML classes to the outermost wrapper by setting the value to the classes attribute. If setting multiple classes, they should be separated by a single space. For example:
`&#91;accessible_reading_original_content classes="custom-class-1 custom-class-2"&#93;Lorem ipsum dolor sit amet.&#91;/accessible_reading_original_content&#93;`

= I'm adding this plugin to an existing website, can I use this to automatically generate content for posts in bulk? =

Yes! In the plugin settings (Tools > Accessible Reading), you'll find the "Bulk Update" tab. There you can configure a bulk update. Starting a dry-run first will tell you how many API requests will be used and approximately how long it will take.

This feature uses the WordPres CRON to reduce your server's load and respects your plan's daily API limit to prevent overages. If your bulk update uses more than what your plan allows, it will automatically schedule to continue the next day, so larger websites may take a few days to complete.

= Is this the official Bionic Reading® WordPress plugin? =

No, this WordPress plugin is not a product of Bionic Reading®. It was developed by an independent and disabled web developer that wants to bring more accessibility to the digital space. However, this plugin uses the official Bionic Reading® API to generate accessible text. If you pay for a premium API plan, that money goes toward the official makers of Bionic Reading®, not this plugin developer. If you want to support this plugin developer, [donations are appreciated](https://just1voice.com/donate)!

== Upgrade Notice ==

= 2.1.1 =

Bug fixes regarding the toggle button appearance settings and preview as well as the API request tracker.

== Changelog ==

= 2.1.1 =

**Release Date: October 9, 2022**

* Fix: clearing out toggle customizations show default settings in preview
* Fix: The default width for the toggle button being set to 10px is fixed
* Fix: API request tracker is now more accurate and resets after 24 hours or a successful API request
* Update: moved the API counter out of the "Bulk Update" tab in settings to under the logo image for better visibility
* Update: renamed the `inc` folder to `includes` for consistency with my other plugins
* Add: introducing the `ACCESSIBLEREADING_VERSION` constant variable

= 2.1.0 =

**Relase Date: September 27, 2022**

* Feature: Backend settings now includes options for customizing the text, colors, and width, of the toggle switch button
* Fix: fixed a bug where it wasn't correctly looking up user capabilities

= 2.0.1 =

**Relase Date: September 26, 2022**

* Fix: Renamed the "lang" folder to "languages" for consistency and updated references in code and plugin files.
* Fix: Addressed spelling errors in readme

= 2.0.0 =

**Relase Date: September 25, 2022**

* Feature: Added ARIA attributes to the frontend toggle switch button to be ADA compliant and better compatible with screen reading devices
* Feature: Added the `accessible_reading` shortcode that requires an `id` attribute to generate accessible content anywhere on the website
* Feature: Added the `accessible_reading_toggle` shortcode to display the toggle button anywhere on the page
* Feature: Added the `accessible_reading_original_content` shortcode to display the original text that gets hidden when accessible content is toggled on anywhere on the page
* Feature: Added the `accessible_reading_content` shortcode to display accessible content that gets displayed when accessible content is toggled on anywhere on the page
* Feature: Added a button on the settings page to disable the automatic toggle button on post content. Only recommended if using the shortcode elsewhere in the template.
* Feature: CSS and JS resources are only loaded on posts and pages that use this feature to improve your site's overall performance
* Feature: Added language translation capabilities (if you'd like to help translate this plugin into more languages, please contact me at tessa@aurisecreative.com)
* Language: Added translation for frontend toggle switch for websites set to German (de-DE)
* Language: Added translation for frontend toggle switch for websites set to Spanish (es-ES) and Mexican Spanish (es-MX)
* Fix: Updated the post-processing code to help prevent content from being divided in the middle of a word or sentence
* Fix: Updated the API request tracker to be more reliable by using the data returned from the most recent request

= 1.3.4 =
**Release Date: September 18, 2022**

* Text: Updated author name and links due to rebranding as AuRise Creative!

= 1.3.3 =
**Release Date: August 17, 2022**

* Fix: Fixed the bug that prevented the "Toggle Switch Default Setting" from saving on the Settings page.

= 1.3.2 =
**Release Date: August 16, 2022**

* Fix: Fixed a fatal error that tried to call a function that did not exist. [Support Ticket](https://wordpress.org/support/topic/activation-not-possible-issue-with-php8/)

= 1.3.1 =
**Release Date: June 23, 2022**

* Fix: set debug flag to false

= 1.3.0 =
**Release Date: June 23, 2022**

* Feature: added a plugin setting to set a default option for the toggle switch
* Feature: added a metabox to manage post-level settings
* Feature: added dropdown in post metabox to set the default option for an individual post
* Feature: added dropdown in post metabox to exclude individual post from processing

= 1.2.0 =
**Release Date: June 17, 2022**

* Feature: Posts with the character count longer than your Bionic Reading® plan's API character limit are processed in chunks, allowing them to be fully processed instead of being cut off
* Feature: The bulk updater has been moved into it's own tab in the WordPress dashboard
* Feature: The bulk updater also displays how many API requests your website has used today
* Feature: The bulk updater also includes a dry run option

= 1.1.0 =
**Release Date: June 16, 2022**

* Feature: added the bulk updater feature! Now you can process your posts in bulk
* Bug: the Bionic Reading® API has a character limit for processing. This will be addressed shortly.

= 1.0.5 =
**Release Date: June 2, 2022**

* Fix: resolved bug in minified javascript file

= 1.0.2 =
**Release Date: June 2, 2022**

* Fix: frontend CSS edits
* Fix: minified CSS & JS assets

= 1.0.1 =
**Release Date: June 1, 2022**

* Major: First release to the public!