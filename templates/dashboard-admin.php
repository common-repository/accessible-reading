<?php
if (isset($_GET['settings-updated'])) {
    add_settings_error(
        $args['plugin_settings']['prefix'] . 'messages',
        $args['plugin_settings']['prefix'] . 'message',
        __('Settings Saved!', 'accessible-reading'),
        'success'
    );
}
settings_errors($args['plugin_settings']['prefix'] . 'messages'); ?>
<div class="wrap au-plugin">
    <h1><img src="<?php echo (esc_url($args['plugin_settings']['url'])); ?>assets/images/admin-logo.png" alt="<?php esc_html_e($args['plugin_settings']['name'] . ' ' . __('by AuRise Creative', 'accessible-reading')); ?>" width="293" height="60" /></h1>
    <?php if ($args['is_processing_bulk']) {
        printf(
            '<div class="accessible-reading-bulk-notice notice notice-info"><p><strong>%s</strong> <button class="cancel-button button button-secondary">%s</button></p></div>',
            __('Processing bulk data in the background.', 'accessible-reading'),
            __('Cancel', 'accessible-reading')
        );
    } ?>
    <div class="au-plugin-admin-ui">
        <div class="loading-spinner"><img src="<?php echo (esc_url($args['plugin_settings']['url'])); ?>assets/images/progress.gif" alt="" width="32" height="32" /></div>
        <div class="admin-ui hide">
            <?php if ($args['has_api_configured']) {
                printf(
                    '<p><small>%s</small></p>',
                    sprintf(
                        wp_filter_post_kses("Your website has used <strong>%s of %s API requests</strong> today.", 'accessible-reading'),
                        esc_html($args['daily_limit_tracker']['count']),
                        esc_html($args['daily_limit_tracker']['total'])
                    )
                );
            } ?>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab" id="open-settings" href="#settings"><?php esc_html_e('Settings', 'accessible-reading') ?></a>
                <a class="nav-tab" id="open-text-previews" href="#text-previews"><?php esc_html_e('Preview', 'accessible-reading') ?></a>
                <a class="nav-tab" id="open-bulk-update" href="#bulk-update"><?php esc_html_e('Bulk Update', 'accessible-reading') ?></a>
                <a class="nav-tab" id="open-shortcodes" href="#shortcodes"><?php esc_html_e('Shortcodes', 'accessible-reading'); ?></a>
                <a class="nav-tab" id="open-about" href="#about"><?php esc_html_e('About Bionic Reading®', 'accessible-reading'); ?></a>
            </h2>
            <div id="tab-content" class="container">
                <section id="settings" class="tab">
                    <?php
                    foreach ($args['plugin_settings']['options'] as $option_group_name => $group) {
                        $option_group = $args['plugin_settings']['prefix'] . $option_group_name;
                        echo ('<form method="post" action="options.php">');
                        settings_fields($option_group); //This should match the group name used in register_setting()
                        printf('<fieldset class="%s"><h2>%s</h2>', esc_attr($option_group_name), esc_html($group['title']));
                        if ($option_group_name == 'toggle_switch') {
                            echo ('<div class="preview-toggle"><span class="label">' . __('Preview:') . '</span>' . do_shortcode('[accessible_reading_toggle/]') . '</div>');
                        }
                        echo ('<table class="form-table" role="presentation">');
                        do_settings_fields($args['plugin_settings']['slug'], $option_group);
                        echo ('</table></fieldset>');
                        submit_button(__('Save Settings', 'accessible-reading'));
                        echo ('</form>');
                    } ?>
                </section>
                <section id="text-previews" class="tab">
                    <p><?php _e('Modify the fixation and saccade options below to preview how they will look <small>(this will not count against your request limit)</small>.', 'accessible-reading'); ?></p>
                    <fieldset>
                        <label>
                            <?php _e('Fixation', 'accessible-reading'); ?>
                            <input type="number" class="fixation" value="1" min="1" max="5" step="1" />
                        </label>
                        <label>
                            <?php _e('Saccade', 'accessible-reading'); ?>
                            <input type="number" class="saccade" value="10" min="10" max="50" step="10" />
                        </label>
                    </fieldset>
                    <div class="text-previews">
                        <div class="text-preview fixation-1 saccade-10"><b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>si</b>t <b>ame</b>t, <b>consetet</b>ur <b>sadipsci</b>ng <b>eli</b>tr, <b>se</b>d <b>dia</b>m <b>nonu</b>my <b>eirm</b>od <b>temp</b>or <b>invidu</b>nt <b>u</b>t <b>labo</b>re <b>e</b>t <b>dolo</b>re <b>mag</b>na <b>aliquy</b>am <b>era</b>t, <b>se</b>d <b>dia</b>m <b>volupt</b>ua. <b>A</b>t <b>ver</b>o <b>eo</b>s <b>e</b>t <b>accus</b>am <b>e</b>t <b>jus</b>to <b>du</b>o <b>dolor</b>es <b>e</b>t <b>e</b>a <b>reb</b>um. <b>Ste</b>t <b>cli</b>ta <b>kas</b>d <b>gubergr</b>en, <b>n</b>o <b>se</b>a <b>takima</b>ta <b>sanct</b>us <b>es</b>t <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>si</b>t <b>ame</b>t. <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>si</b>t <b>ame</b>t, <b>consetet</b>ur <b>sadipsci</b>ng <b>eli</b>tr, <b>se</b>d <b>dia</b>m <b>nonu</b>my <b>eirm</b>od <b>temp</b>or <b>invidu</b>nt <b>u</b>t <b>labo</b>re <b>e</b>t <b>dolo</b>re <b>mag</b>na <b>aliquy</b>am <b>era</b>t, <b>se</b>d <b>dia</b>m <b>volupt</b>ua. <b>A</b>t <b>ver</b>o <b>eo</b>s <b>e</b>t <b>accus</b>am <b>e</b>t <b>jus</b>to <b>du</b>o <b>dolor</b>es <b>e</b>t <b>e</b>a <b>reb</b>um. <b>Ste</b>t <b>cli</b>ta <b>kas</b>d <b>gubergr</b>en, <b>n</b>o <b>se</b>a <b>takima</b>ta <b>sanct</b>us <b>es</b>t <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>si</b>t <b>ame</b>t.</div>
                        <div class="text-preview fixation-2 saccade-10 hide"><b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et, <b>consete</b>tur <b>sadipsc</b>ing <b>eli</b>tr, <b>s</b>ed <b>di</b>am <b>nonu</b>my <b>eirm</b>od <b>temp</b>or <b>invid</b>unt <b>u</b>t <b>labo</b>re <b>e</b>t <b>dolo</b>re <b>mag</b>na <b>aliqu</b>yam <b>er</b>at, <b>s</b>ed <b>di</b>am <b>volup</b>tua. <b>A</b>t <b>ve</b>ro <b>e</b>os <b>e</b>t <b>accus</b>am <b>e</b>t <b>jus</b>to <b>d</b>uo <b>dolor</b>es <b>e</b>t <b>e</b>a <b>reb</b>um. <b>St</b>et <b>cli</b>ta <b>ka</b>sd <b>guberg</b>ren, <b>n</b>o <b>s</b>ea <b>takim</b>ata <b>sanct</b>us <b>e</b>st <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et. <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et, <b>consete</b>tur <b>sadipsc</b>ing <b>eli</b>tr, <b>s</b>ed <b>di</b>am <b>nonu</b>my <b>eirm</b>od <b>temp</b>or <b>invid</b>unt <b>u</b>t <b>labo</b>re <b>e</b>t <b>dolo</b>re <b>mag</b>na <b>aliqu</b>yam <b>er</b>at, <b>s</b>ed <b>di</b>am <b>volup</b>tua. <b>A</b>t <b>ve</b>ro <b>e</b>os <b>e</b>t <b>accus</b>am <b>e</b>t <b>jus</b>to <b>d</b>uo <b>dolor</b>es <b>e</b>t <b>e</b>a <b>reb</b>um. <b>St</b>et <b>cli</b>ta <b>ka</b>sd <b>guberg</b>ren, <b>n</b>o <b>s</b>ea <b>takim</b>ata <b>sanct</b>us <b>e</b>st <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et.</div>
                        <div class="text-preview fixation-3 saccade-10 hide"><b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et, <b>conse</b>tetur <b>sadip</b>scing <b>eli</b>tr, <b>s</b>ed <b>di</b>am <b>non</b>umy <b>eir</b>mod <b>tem</b>por <b>invi</b>dunt <b>u</b>t <b>lab</b>ore <b>e</b>t <b>dol</b>ore <b>mag</b>na <b>aliq</b>uyam <b>er</b>at, <b>s</b>ed <b>di</b>am <b>volu</b>ptua. <b>A</b>t <b>ve</b>ro <b>e</b>os <b>e</b>t <b>accu</b>sam <b>e</b>t <b>jus</b>to <b>d</b>uo <b>dolo</b>res <b>e</b>t <b>e</b>a <b>reb</b>um. <b>St</b>et <b>cli</b>ta <b>ka</b>sd <b>guber</b>gren, <b>n</b>o <b>s</b>ea <b>taki</b>mata <b>sanc</b>tus <b>e</b>st <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et. <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et, <b>conse</b>tetur <b>sadip</b>scing <b>eli</b>tr, <b>s</b>ed <b>di</b>am <b>non</b>umy <b>eir</b>mod <b>tem</b>por <b>invi</b>dunt <b>u</b>t <b>lab</b>ore <b>e</b>t <b>dol</b>ore <b>mag</b>na <b>aliq</b>uyam <b>er</b>at, <b>s</b>ed <b>di</b>am <b>volu</b>ptua. <b>A</b>t <b>ve</b>ro <b>e</b>os <b>e</b>t <b>accu</b>sam <b>e</b>t <b>jus</b>to <b>d</b>uo <b>dolo</b>res <b>e</b>t <b>e</b>a <b>reb</b>um. <b>St</b>et <b>cli</b>ta <b>ka</b>sd <b>guber</b>gren, <b>n</b>o <b>s</b>ea <b>taki</b>mata <b>sanc</b>tus <b>e</b>st <b>Lor</b>em <b>ips</b>um <b>dol</b>or <b>s</b>it <b>am</b>et.</div>
                        <div class="text-preview fixation-4 saccade-10 hide"><b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>am</b>et, <b>con</b>setetur <b>sad</b>ipscing <b>el</b>itr, <b>s</b>ed <b>di</b>am <b>no</b>numy <b>ei</b>rmod <b>te</b>mpor <b>inv</b>idunt <b>u</b>t <b>la</b>bore <b>e</b>t <b>do</b>lore <b>ma</b>gna <b>ali</b>quyam <b>er</b>at, <b>s</b>ed <b>di</b>am <b>vol</b>uptua. <b>A</b>t <b>ve</b>ro <b>e</b>os <b>e</b>t <b>ac</b>cusam <b>e</b>t <b>ju</b>sto <b>d</b>uo <b>do</b>lores <b>e</b>t <b>e</b>a <b>re</b>bum. <b>St</b>et <b>cl</b>ita <b>ka</b>sd <b>gub</b>ergren, <b>n</b>o <b>s</b>ea <b>tak</b>imata <b>sa</b>nctus <b>e</b>st <b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>am</b>et. <b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>am</b>et, <b>con</b>setetur <b>sad</b>ipscing <b>el</b>itr, <b>s</b>ed <b>di</b>am <b>no</b>numy <b>ei</b>rmod <b>te</b>mpor <b>inv</b>idunt <b>u</b>t <b>la</b>bore <b>e</b>t <b>do</b>lore <b>ma</b>gna <b>ali</b>quyam <b>er</b>at, <b>s</b>ed <b>di</b>am <b>vol</b>uptua. <b>A</b>t <b>ve</b>ro <b>e</b>os <b>e</b>t <b>ac</b>cusam <b>e</b>t <b>ju</b>sto <b>d</b>uo <b>do</b>lores <b>e</b>t <b>e</b>a <b>re</b>bum. <b>St</b>et <b>cl</b>ita <b>ka</b>sd <b>gub</b>ergren, <b>n</b>o <b>s</b>ea <b>tak</b>imata <b>sa</b>nctus <b>e</b>st <b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>am</b>et.</div>
                        <div class="text-preview fixation-5 saccade-10 hide"><b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>a</b>met, <b>con</b>setetur <b>sad</b>ipscing <b>el</b>itr, <b>s</b>ed <b>d</b>iam <b>no</b>numy <b>ei</b>rmod <b>te</b>mpor <b>in</b>vidunt <b>u</b>t <b>la</b>bore <b>e</b>t <b>do</b>lore <b>ma</b>gna <b>al</b>iquyam <b>e</b>rat, <b>s</b>ed <b>d</b>iam <b>vo</b>luptua. <b>A</b>t <b>v</b>ero <b>e</b>os <b>e</b>t <b>ac</b>cusam <b>e</b>t <b>ju</b>sto <b>d</b>uo <b>do</b>lores <b>e</b>t <b>e</b>a <b>re</b>bum. <b>S</b>tet <b>cl</b>ita <b>k</b>asd <b>gu</b>bergren, <b>n</b>o <b>s</b>ea <b>ta</b>kimata <b>sa</b>nctus <b>e</b>st <b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>a</b>met. <b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>a</b>met, <b>con</b>setetur <b>sad</b>ipscing <b>el</b>itr, <b>s</b>ed <b>d</b>iam <b>no</b>numy <b>ei</b>rmod <b>te</b>mpor <b>in</b>vidunt <b>u</b>t <b>la</b>bore <b>e</b>t <b>do</b>lore <b>ma</b>gna <b>al</b>iquyam <b>e</b>rat, <b>s</b>ed <b>d</b>iam <b>vo</b>luptua. <b>A</b>t <b>v</b>ero <b>e</b>os <b>e</b>t <b>ac</b>cusam <b>e</b>t <b>ju</b>sto <b>d</b>uo <b>do</b>lores <b>e</b>t <b>e</b>a <b>re</b>bum. <b>S</b>tet <b>cl</b>ita <b>k</b>asd <b>gu</b>bergren, <b>n</b>o <b>s</b>ea <b>ta</b>kimata <b>sa</b>nctus <b>e</b>st <b>Lo</b>rem <b>ip</b>sum <b>do</b>lor <b>s</b>it <b>a</b>met.</div>

                        <div class="text-preview fixation-1 saccade-20 hide"><b>Lor</b>em ipsum <b>dol</b>or sit <b>ame</b>t, consetetur <b>sadipsci</b>ng elitr, <b>se</b>d diam <b>nonu</b>my eirmod <b>temp</b>or invidunt <b>u</b>t labore <b>e</b>t dolore <b>mag</b>na aliquyam <b>era</b>t, sed <b>dia</b>m voluptua. <b>A</b>t vero <b>eo</b>s et <b>accus</b>am et <b>jus</b>to duo <b>dolor</b>es et <b>e</b>a rebum. <b>Ste</b>t clita <b>kas</b>d gubergren, <b>n</b>o sea <b>takima</b>ta sanctus <b>es</b>t Lorem <b>ips</b>um dolor <b>si</b>t amet. <b>Lor</b>em ipsum <b>dol</b>or sit <b>ame</b>t, consetetur <b>sadipsci</b>ng elitr, <b>se</b>d diam <b>nonu</b>my eirmod <b>temp</b>or invidunt <b>u</b>t labore <b>e</b>t dolore <b>mag</b>na aliquyam <b>era</b>t, sed <b>dia</b>m voluptua. <b>A</b>t vero <b>eo</b>s et <b>accus</b>am et <b>jus</b>to duo <b>dolor</b>es et <b>e</b>a rebum. <b>Ste</b>t clita <b>kas</b>d gubergren, <b>n</b>o sea <b>takima</b>ta sanctus <b>es</b>t Lorem <b>ips</b>um dolor <b>si</b>t amet.</div>
                        <div class="text-preview fixation-2 saccade-20 hide"><b>Lor</b>em ipsum <b>dol</b>or sit <b>am</b>et, consetetur <b>sadipsc</b>ing elitr, <b>s</b>ed diam <b>nonu</b>my eirmod <b>temp</b>or invidunt <b>u</b>t labore <b>e</b>t dolore <b>mag</b>na aliquyam <b>er</b>at, sed <b>di</b>am voluptua. <b>A</b>t vero <b>e</b>os et <b>accus</b>am et <b>jus</b>to duo <b>dolor</b>es et <b>e</b>a rebum. <b>St</b>et clita <b>ka</b>sd gubergren, <b>n</b>o sea <b>takim</b>ata sanctus <b>e</b>st Lorem <b>ips</b>um dolor <b>s</b>it amet. <b>Lor</b>em ipsum <b>dol</b>or sit <b>am</b>et, consetetur <b>sadipsc</b>ing elitr, <b>s</b>ed diam <b>nonu</b>my eirmod <b>temp</b>or invidunt <b>u</b>t labore <b>e</b>t dolore <b>mag</b>na aliquyam <b>er</b>at, sed <b>di</b>am voluptua. <b>A</b>t vero <b>e</b>os et <b>accus</b>am et <b>jus</b>to duo <b>dolor</b>es et <b>e</b>a rebum. <b>St</b>et clita <b>ka</b>sd gubergren, <b>n</b>o sea <b>takim</b>ata sanctus <b>e</b>st Lorem <b>ips</b>um dolor <b>s</b>it amet.</div>
                        <div class="text-preview fixation-3 saccade-20 hide"><b>Lor</b>em ipsum <b>dol</b>or sit <b>am</b>et, consetetur <b>sadip</b>scing elitr, <b>s</b>ed diam <b>non</b>umy eirmod <b>tem</b>por invidunt <b>u</b>t labore <b>e</b>t dolore <b>mag</b>na aliquyam <b>er</b>at, sed <b>di</b>am voluptua. <b>A</b>t vero <b>e</b>os et <b>accu</b>sam et <b>jus</b>to duo <b>dolo</b>res et <b>e</b>a rebum. <b>St</b>et clita <b>ka</b>sd gubergren, <b>n</b>o sea <b>taki</b>mata sanctus <b>e</b>st Lorem <b>ips</b>um dolor <b>s</b>it amet. <b>Lor</b>em ipsum <b>dol</b>or sit <b>am</b>et, consetetur <b>sadip</b>scing elitr, <b>s</b>ed diam <b>non</b>umy eirmod <b>tem</b>por invidunt <b>u</b>t labore <b>e</b>t dolore <b>mag</b>na aliquyam <b>er</b>at, sed <b>di</b>am voluptua. <b>A</b>t vero <b>e</b>os et <b>accu</b>sam et <b>jus</b>to duo <b>dolo</b>res et <b>e</b>a rebum. <b>St</b>et clita <b>ka</b>sd gubergren, <b>n</b>o sea <b>taki</b>mata sanctus <b>e</b>st Lorem <b>ips</b>um dolor <b>s</b>it amet.</div>
                        <div class="text-preview fixation-4 saccade-20 hide"><b>Lo</b>rem ipsum <b>do</b>lor sit <b>am</b>et, consetetur <b>sad</b>ipscing elitr, <b>s</b>ed diam <b>no</b>numy eirmod <b>te</b>mpor invidunt <b>u</b>t labore <b>e</b>t dolore <b>ma</b>gna aliquyam <b>er</b>at, sed <b>di</b>am voluptua. <b>A</b>t vero <b>e</b>os et <b>ac</b>cusam et <b>ju</b>sto duo <b>do</b>lores et <b>e</b>a rebum. <b>St</b>et clita <b>ka</b>sd gubergren, <b>n</b>o sea <b>tak</b>imata sanctus <b>e</b>st Lorem <b>ip</b>sum dolor <b>s</b>it amet. <b>Lo</b>rem ipsum <b>do</b>lor sit <b>am</b>et, consetetur <b>sad</b>ipscing elitr, <b>s</b>ed diam <b>no</b>numy eirmod <b>te</b>mpor invidunt <b>u</b>t labore <b>e</b>t dolore <b>ma</b>gna aliquyam <b>er</b>at, sed <b>di</b>am voluptua. <b>A</b>t vero <b>e</b>os et <b>ac</b>cusam et <b>ju</b>sto duo <b>do</b>lores et <b>e</b>a rebum. <b>St</b>et clita <b>ka</b>sd gubergren, <b>n</b>o sea <b>tak</b>imata sanctus <b>e</b>st Lorem <b>ip</b>sum dolor <b>s</b>it amet.</div>
                        <div class="text-preview fixation-5 saccade-20 hide"><b>Lo</b>rem ipsum <b>do</b>lor sit <b>a</b>met, consetetur <b>sad</b>ipscing elitr, <b>s</b>ed diam <b>no</b>numy eirmod <b>te</b>mpor invidunt <b>u</b>t labore <b>e</b>t dolore <b>ma</b>gna aliquyam <b>e</b>rat, sed <b>d</b>iam voluptua. <b>A</b>t vero <b>e</b>os et <b>ac</b>cusam et <b>ju</b>sto duo <b>do</b>lores et <b>e</b>a rebum. <b>S</b>tet clita <b>k</b>asd gubergren, <b>n</b>o sea <b>ta</b>kimata sanctus <b>e</b>st Lorem <b>ip</b>sum dolor <b>s</b>it amet. <b>Lo</b>rem ipsum <b>do</b>lor sit <b>a</b>met, consetetur <b>sad</b>ipscing elitr, <b>s</b>ed diam <b>no</b>numy eirmod <b>te</b>mpor invidunt <b>u</b>t labore <b>e</b>t dolore <b>ma</b>gna aliquyam <b>e</b>rat, sed <b>d</b>iam voluptua. <b>A</b>t vero <b>e</b>os et <b>ac</b>cusam et <b>ju</b>sto duo <b>do</b>lores et <b>e</b>a rebum. <b>S</b>tet clita <b>k</b>asd gubergren, <b>n</b>o sea <b>ta</b>kimata sanctus <b>e</b>st Lorem <b>ip</b>sum dolor <b>s</b>it amet.</div>

                        <div class="text-preview fixation-1 saccade-30 hide"><b>Lor</b>em ipsum dolor <b>si</b>t amet, consetetur <b>sadipsci</b>ng elitr, sed <b>dia</b>m nonumy eirmod <b>temp</b>or invidunt ut <b>labo</b>re et dolore <b>mag</b>na aliquyam erat, <b>se</b>d diam voluptua. <b>A</b>t vero eos <b>e</b>t accusam et <b>jus</b>to duo dolores <b>e</b>t ea rebum. <b>Ste</b>t clita kasd <b>gubergr</b>en, no sea <b>takima</b>ta sanctus est <b>Lor</b>em ipsum dolor <b>si</b>t amet. Lorem <b>ips</b>um dolor sit <b>ame</b>t, consetetur sadipscing <b>eli</b>tr, sed diam <b>nonu</b>my eirmod tempor <b>invidu</b>nt ut labore <b>e</b>t dolore magna <b>aliquy</b>am erat, sed <b>dia</b>m voluptua. At <b>ver</b>o eos et <b>accus</b>am et justo <b>du</b>o dolores et <b>e</b>a rebum. Stet <b>cli</b>ta kasd gubergren, <b>n</b>o sea takimata <b>sanct</b>us est Lorem <b>ips</b>um dolor sit <b>ame</b>t.</div>
                        <div class="text-preview fixation-2 saccade-30 hide"><b>Lor</b>em ipsum dolor <b>s</b>it amet, consetetur <b>sadipsc</b>ing elitr, sed <b>di</b>am nonumy eirmod <b>temp</b>or invidunt ut <b>labo</b>re et dolore <b>mag</b>na aliquyam erat, <b>s</b>ed diam voluptua. <b>A</b>t vero eos <b>e</b>t accusam et <b>jus</b>to duo dolores <b>e</b>t ea rebum. <b>St</b>et clita kasd <b>guberg</b>ren, no sea <b>takim</b>ata sanctus est <b>Lor</b>em ipsum dolor <b>s</b>it amet. Lorem <b>ips</b>um dolor sit <b>am</b>et, consetetur sadipscing <b>eli</b>tr, sed diam <b>nonu</b>my eirmod tempor <b>invid</b>unt ut labore <b>e</b>t dolore magna <b>aliqu</b>yam erat, sed <b>di</b>am voluptua. At <b>ve</b>ro eos et <b>accus</b>am et justo <b>d</b>uo dolores et <b>e</b>a rebum. Stet <b>cli</b>ta kasd gubergren, <b>n</b>o sea takimata <b>sanct</b>us est Lorem <b>ips</b>um dolor sit <b>am</b>et.</div>
                        <div class="text-preview fixation-3 saccade-30 hide"><b>Lor</b>em ipsum dolor <b>s</b>it amet, consetetur <b>sadip</b>scing elitr, sed <b>di</b>am nonumy eirmod <b>tem</b>por invidunt ut <b>lab</b>ore et dolore <b>mag</b>na aliquyam erat, <b>s</b>ed diam voluptua. <b>A</b>t vero eos <b>e</b>t accusam et <b>jus</b>to duo dolores <b>e</b>t ea rebum. <b>St</b>et clita kasd <b>guber</b>gren, no sea <b>taki</b>mata sanctus est <b>Lor</b>em ipsum dolor <b>s</b>it amet. Lorem <b>ips</b>um dolor sit <b>am</b>et, consetetur sadipscing <b>eli</b>tr, sed diam <b>non</b>umy eirmod tempor <b>invi</b>dunt ut labore <b>e</b>t dolore magna <b>aliq</b>uyam erat, sed <b>di</b>am voluptua. At <b>ve</b>ro eos et <b>accu</b>sam et justo <b>d</b>uo dolores et <b>e</b>a rebum. Stet <b>cli</b>ta kasd gubergren, <b>n</b>o sea takimata <b>sanc</b>tus est Lorem <b>ips</b>um dolor sit <b>am</b>et.</div>
                        <div class="text-preview fixation-4 saccade-30 hide"><b>Lo</b>rem ipsum dolor <b>s</b>it amet, consetetur <b>sad</b>ipscing elitr, sed <b>di</b>am nonumy eirmod <b>te</b>mpor invidunt ut <b>la</b>bore et dolore <b>ma</b>gna aliquyam erat, <b>s</b>ed diam voluptua. <b>A</b>t vero eos <b>e</b>t accusam et <b>ju</b>sto duo dolores <b>e</b>t ea rebum. <b>St</b>et clita kasd <b>gub</b>ergren, no sea <b>tak</b>imata sanctus est <b>Lo</b>rem ipsum dolor <b>s</b>it amet. Lorem <b>ip</b>sum dolor sit <b>am</b>et, consetetur sadipscing <b>el</b>itr, sed diam <b>no</b>numy eirmod tempor <b>inv</b>idunt ut labore <b>e</b>t dolore magna <b>ali</b>quyam erat, sed <b>di</b>am voluptua. At <b>ve</b>ro eos et <b>ac</b>cusam et justo <b>d</b>uo dolores et <b>e</b>a rebum. Stet <b>cl</b>ita kasd gubergren, <b>n</b>o sea takimata <b>sa</b>nctus est Lorem <b>ip</b>sum dolor sit <b>am</b>et.</div>
                        <div class="text-preview fixation-5 saccade-30 hide"><b>Lo</b>rem ipsum dolor <b>s</b>it amet, consetetur <b>sad</b>ipscing elitr, sed <b>d</b>iam nonumy eirmod <b>te</b>mpor invidunt ut <b>la</b>bore et dolore <b>ma</b>gna aliquyam erat, <b>s</b>ed diam voluptua. <b>A</b>t vero eos <b>e</b>t accusam et <b>ju</b>sto duo dolores <b>e</b>t ea rebum. <b>S</b>tet clita kasd <b>gu</b>bergren, no sea <b>ta</b>kimata sanctus est <b>Lo</b>rem ipsum dolor <b>s</b>it amet. Lorem <b>ip</b>sum dolor sit <b>a</b>met, consetetur sadipscing <b>el</b>itr, sed diam <b>no</b>numy eirmod tempor <b>in</b>vidunt ut labore <b>e</b>t dolore magna <b>al</b>iquyam erat, sed <b>d</b>iam voluptua. At <b>v</b>ero eos et <b>ac</b>cusam et justo <b>d</b>uo dolores et <b>e</b>a rebum. Stet <b>cl</b>ita kasd gubergren, <b>n</b>o sea takimata <b>sa</b>nctus est Lorem <b>ip</b>sum dolor sit <b>a</b>met.</div>

                        <div class="text-preview fixation-1 saccade-40 hide"><b>Lor</b>em ipsum dolor sit <b>ame</b>t, consetetur sadipscing elitr, <b>se</b>d diam nonumy eirmod <b>temp</b>or invidunt ut labore <b>e</b>t dolore magna aliquyam <b>era</b>t, sed diam voluptua. <b>A</b>t vero eos et <b>accus</b>am et justo duo <b>dolor</b>es et ea rebum. <b>Ste</b>t clita kasd gubergren, <b>n</b>o sea takimata sanctus <b>es</b>t Lorem ipsum dolor <b>si</b>t amet. Lorem ipsum <b>dol</b>or sit amet, consetetur <b>sadipsci</b>ng elitr, sed diam <b>nonu</b>my eirmod tempor invidunt <b>u</b>t labore et dolore <b>mag</b>na aliquyam erat, sed <b>dia</b>m voluptua. At vero <b>eo</b>s et accusam et <b>jus</b>to duo dolores et <b>e</b>a rebum. Stet clita <b>kas</b>d gubergren, no sea <b>takima</b>ta sanctus est Lorem <b>ips</b>um dolor sit amet.</div>
                        <div class="text-preview fixation-2 saccade-40 hide"><b>Lor</b>em ipsum dolor sit <b>am</b>et, consetetur sadipscing elitr, <b>s</b>ed diam nonumy eirmod <b>temp</b>or invidunt ut labore <b>e</b>t dolore magna aliquyam <b>er</b>at, sed diam voluptua. <b>A</b>t vero eos et <b>accus</b>am et justo duo <b>dolor</b>es et ea rebum. <b>St</b>et clita kasd gubergren, <b>n</b>o sea takimata sanctus <b>e</b>st Lorem ipsum dolor <b>s</b>it amet. Lorem ipsum <b>dol</b>or sit amet, consetetur <b>sadipsc</b>ing elitr, sed diam <b>nonu</b>my eirmod tempor invidunt <b>u</b>t labore et dolore <b>mag</b>na aliquyam erat, sed <b>di</b>am voluptua. At vero <b>e</b>os et accusam et <b>jus</b>to duo dolores et <b>e</b>a rebum. Stet clita <b>ka</b>sd gubergren, no sea <b>takim</b>ata sanctus est Lorem <b>ips</b>um dolor sit amet.</div>
                        <div class="text-preview fixation-3 saccade-40 hide"><b>Lor</b>em ipsum dolor sit <b>am</b>et, consetetur sadipscing elitr, <b>s</b>ed diam nonumy eirmod <b>tem</b>por invidunt ut labore <b>e</b>t dolore magna aliquyam <b>er</b>at, sed diam voluptua. <b>A</b>t vero eos et <b>accu</b>sam et justo duo <b>dolo</b>res et ea rebum. <b>St</b>et clita kasd gubergren, <b>n</b>o sea takimata sanctus <b>e</b>st Lorem ipsum dolor <b>s</b>it amet. Lorem ipsum <b>dol</b>or sit amet, consetetur <b>sadip</b>scing elitr, sed diam <b>non</b>umy eirmod tempor invidunt <b>u</b>t labore et dolore <b>mag</b>na aliquyam erat, sed <b>di</b>am voluptua. At vero <b>e</b>os et accusam et <b>jus</b>to duo dolores et <b>e</b>a rebum. Stet clita <b>ka</b>sd gubergren, no sea <b>taki</b>mata sanctus est Lorem <b>ips</b>um dolor sit amet.</div>
                        <div class="text-preview fixation-4 saccade-40 hide"><b>Lo</b>rem ipsum dolor sit <b>am</b>et, consetetur sadipscing elitr, <b>s</b>ed diam nonumy eirmod <b>te</b>mpor invidunt ut labore <b>e</b>t dolore magna aliquyam <b>er</b>at, sed diam voluptua. <b>A</b>t vero eos et <b>ac</b>cusam et justo duo <b>do</b>lores et ea rebum. <b>St</b>et clita kasd gubergren, <b>n</b>o sea takimata sanctus <b>e</b>st Lorem ipsum dolor <b>s</b>it amet. Lorem ipsum <b>do</b>lor sit amet, consetetur <b>sad</b>ipscing elitr, sed diam <b>no</b>numy eirmod tempor invidunt <b>u</b>t labore et dolore <b>ma</b>gna aliquyam erat, sed <b>di</b>am voluptua. At vero <b>e</b>os et accusam et <b>ju</b>sto duo dolores et <b>e</b>a rebum. Stet clita <b>ka</b>sd gubergren, no sea <b>tak</b>imata sanctus est Lorem <b>ip</b>sum dolor sit amet.</div>
                        <div class="text-preview fixation-5 saccade-40 hide"><b>Lo</b>rem ipsum dolor sit <b>a</b>met, consetetur sadipscing elitr, <b>s</b>ed diam nonumy eirmod <b>te</b>mpor invidunt ut labore <b>e</b>t dolore magna aliquyam <b>e</b>rat, sed diam voluptua. <b>A</b>t vero eos et <b>ac</b>cusam et justo duo <b>do</b>lores et ea rebum. <b>S</b>tet clita kasd gubergren, <b>n</b>o sea takimata sanctus <b>e</b>st Lorem ipsum dolor <b>s</b>it amet. Lorem ipsum <b>do</b>lor sit amet, consetetur <b>sad</b>ipscing elitr, sed diam <b>no</b>numy eirmod tempor invidunt <b>u</b>t labore et dolore <b>ma</b>gna aliquyam erat, sed <b>d</b>iam voluptua. At vero <b>e</b>os et accusam et <b>ju</b>sto duo dolores et <b>e</b>a rebum. Stet clita <b>k</b>asd gubergren, no sea <b>ta</b>kimata sanctus est Lorem <b>ip</b>sum dolor sit amet.</div>

                        <div class="text-preview fixation-1 saccade-50 hide"><b>Lor</b>em ipsum dolor sit amet, <b>consetet</b>ur sadipscing elitr, sed diam <b>nonu</b>my eirmod tempor invidunt ut <b>labo</b>re et dolore magna aliquyam <b>era</b>t, sed diam voluptua. At <b>ver</b>o eos et accusam et <b>jus</b>to duo dolores et ea <b>reb</b>um. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lor</b>em ipsum dolor sit amet. <b>Lor</b>em ipsum dolor sit amet, <b>consetet</b>ur sadipscing elitr, sed diam <b>nonu</b>my eirmod tempor invidunt ut <b>labo</b>re et dolore magna aliquyam <b>era</b>t, sed diam voluptua. At <b>ver</b>o eos et accusam et <b>jus</b>to duo dolores et ea <b>reb</b>um. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lor</b>em ipsum dolor sit amet.</div>
                        <div class="text-preview fixation-2 saccade-50 hide"><b>Lor</b>em ipsum dolor sit amet, <b>consete</b>tur sadipscing elitr, sed diam <b>nonu</b>my eirmod tempor invidunt ut <b>labo</b>re et dolore magna aliquyam <b>er</b>at, sed diam voluptua. At <b>ve</b>ro eos et accusam et <b>jus</b>to duo dolores et ea <b>reb</b>um. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lor</b>em ipsum dolor sit amet. <b>Lor</b>em ipsum dolor sit amet, <b>consete</b>tur sadipscing elitr, sed diam <b>nonu</b>my eirmod tempor invidunt ut <b>labo</b>re et dolore magna aliquyam <b>er</b>at, sed diam voluptua. At <b>ve</b>ro eos et accusam et <b>jus</b>to duo dolores et ea <b>reb</b>um. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lor</b>em ipsum dolor sit amet.</div>
                        <div class="text-preview fixation-3 saccade-50 hide"><b>Lor</b>em ipsum dolor sit amet, <b>conse</b>tetur sadipscing elitr, sed diam <b>non</b>umy eirmod tempor invidunt ut <b>lab</b>ore et dolore magna aliquyam <b>er</b>at, sed diam voluptua. At <b>ve</b>ro eos et accusam et <b>jus</b>to duo dolores et ea <b>reb</b>um. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lor</b>em ipsum dolor sit amet. <b>Lor</b>em ipsum dolor sit amet, <b>conse</b>tetur sadipscing elitr, sed diam <b>non</b>umy eirmod tempor invidunt ut <b>lab</b>ore et dolore magna aliquyam <b>er</b>at, sed diam voluptua. At <b>ve</b>ro eos et accusam et <b>jus</b>to duo dolores et ea <b>reb</b>um. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lor</b>em ipsum dolor sit amet.</div>
                        <div class="text-preview fixation-4 saccade-50 hide"><b>Lo</b>rem ipsum dolor sit amet, <b>con</b>setetur sadipscing elitr, sed diam <b>no</b>numy eirmod tempor invidunt ut <b>la</b>bore et dolore magna aliquyam <b>er</b>at, sed diam voluptua. At <b>ve</b>ro eos et accusam et <b>ju</b>sto duo dolores et ea <b>re</b>bum. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lo</b>rem ipsum dolor sit amet. <b>Lo</b>rem ipsum dolor sit amet, <b>con</b>setetur sadipscing elitr, sed diam <b>no</b>numy eirmod tempor invidunt ut <b>la</b>bore et dolore magna aliquyam <b>er</b>at, sed diam voluptua. At <b>ve</b>ro eos et accusam et <b>ju</b>sto duo dolores et ea <b>re</b>bum. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lo</b>rem ipsum dolor sit amet.</div>
                        <div class="text-preview fixation-5 saccade-50 hide"><b>Lo</b>rem ipsum dolor sit amet, <b>con</b>setetur sadipscing elitr, sed diam <b>no</b>numy eirmod tempor invidunt ut <b>la</b>bore et dolore magna aliquyam <b>e</b>rat, sed diam voluptua. At <b>v</b>ero eos et accusam et <b>ju</b>sto duo dolores et ea <b>re</b>bum. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lo</b>rem ipsum dolor sit amet. <b>Lo</b>rem ipsum dolor sit amet, <b>con</b>setetur sadipscing elitr, sed diam <b>no</b>numy eirmod tempor invidunt ut <b>la</b>bore et dolore magna aliquyam <b>e</b>rat, sed diam voluptua. At <b>v</b>ero eos et accusam et <b>ju</b>sto duo dolores et ea <b>re</b>bum. Stet clita kasd gubergren, <b>n</b>o sea takimata sanctus est <b>Lo</b>rem ipsum dolor sit amet.</div>
                    </div>
                </section>
                <section id="bulk-update" class="tab">
                    <form id="accessible-reading-bulk-update">
                        <fieldset>
                            <h2><?php esc_html_e('Bulk Update', 'accessible-reading'); ?></h2>
                            <?php if ($args['has_api_configured']) {
                                printf(
                                    '<p>%s</p>',
                                    __("This feature uses WordPress CRON events to reduce your server's load and respects your plan's daily API limit to prevent overages.", 'accessible-reading')
                                );
                            } else {
                                printf('<p>%s</p>', esc_html("Once you have your API key, you can run a bulk updater on all of your posts. This feature uses WordPress CRON events to reduce your server's load and respects your plan's daily API limit to prevent overages.", 'accessible-reading'));
                            } ?>
                            <p><?php  ?></p>
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Batch Limit', 'accessible-reading'); ?></th>
                                        <td><input name="batch_limit" type="number" value="100" placeholder="" required="required" min="1" max="20000" step="1"><br><small><?php esc_html_e('This is how many posts will be processed at a time. Use a smaller number to reduce the load on your server or avoid server timeouts. This means more batches will be scheduled, but less posts processed per batch so each one does not take as long.', 'accessible-reading'); ?></small></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Force Generate', 'accessible-reading'); ?></th>
                                        <td>
                                            <span class="checkbox-switch">
                                                <input class="input-checkbox" type="checkbox" name="force_generate" />
                                                <span class="checkbox-animate">
                                                    <span class="checkbox-off"><?php esc_html_e('Off', 'accessible-reading'); ?></span>
                                                    <span class="checkbox-on"><?php esc_html_e('On', 'accessible-reading'); ?></span>
                                                </span>
                                            </span>
                                            <br><small><?php esc_html_e('If enabled, this will overwrite all previously generated accessible content. Otherwise, it will just generate content for posts that were missing accessible content.', 'accessible-reading'); ?></small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Dry Run', 'accessible-reading'); ?></th>
                                        <td>
                                            <span class="checkbox-switch">
                                                <input class="input-checkbox" type="checkbox" name="estimate" checked />
                                                <span class="checkbox-animate">
                                                    <span class="checkbox-off"><?php esc_html_e('Off', 'accessible-reading'); ?></span>
                                                    <span class="checkbox-on"><?php esc_html_e('On', 'accessible-reading'); ?></span>
                                                </span>
                                            </span>
                                            <br><small><?php esc_html_e('If enabled, pressing the start button will only process your posts to determine how many API requests will need to be made and roughly how long it will take to complete based on your plan and these settings. If disabled, it will process your posts using the API.', 'accessible-reading'); ?></small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="buttons">
                                <button type="submit" class="button button-primary"><?php _e('Start Bulk Update', 'accessible-reading'); ?></button>
                                <span class="progress-spinner hide"><img src="<?php echo (esc_url($args['plugin_settings']['url'])); ?>assets/images/progress.gif" alt="" width="32" height="32" /></span>
                            </div>
                            <div class="form-response-output hide"></div>
                        </fieldset>
                    </form>
                </section>
                <section id="shortcodes" class="tab">
                    <p><?php _e('This plugin provides four (4) shortcodes so you can add accessible reading anywhere on your website!', 'accessible-reading'); ?>
                    <div class="au-accordion">
                        <div>
                            <h3 class="au-accordion-item-title"><?php _e('Display automatically generated accessible content anywhere!', 'accessible-reading'); ?></h3>
                            <div>
                                <?php echo (sprintf(
                                    __("The %s shortcode allows you to add accessible text with a toggle switch anywhere on your WordPress website. To ensure your shortcode's accessible content is automatically generated, a user with %s capabilities must preview the page using the shortcode at least once while you have enough API requests for the day.", 'accessible-reading'),
                                    '<code>accessible_reading</code>',
                                    '<code>edit_post</code>'
                                )); ?><br /><br />
                                <?php printf(__("To use it, simply wrap the text you want to make accessible with the shortcode while also adding an <code>%s</code> attribute. For example:", 'accessible-reading'), 'id'); ?>
                                <pre>[accessible_reading id="foo"]<?php _e('This content will become accessible!', 'accessible-reading'); ?>[/accessible_reading]</pre><br />
                                <?php echo (sprintf(
                                    __("This shortcode will also add the toggle switch to enable/disable the accessible content automatically, so if you're using it in multiple locations and want just one switch, set the %s attribute to %s to disable it like this:", 'accessible-reading'),
                                    '<code>hide_toggle</code>',
                                    '<code>1</code>'
                                )); ?>
                                <pre>[accessible_reading id="bar" hide_toggle="1"]<?php _e("This content will become accessible but &lt;strong&gt;without showing a toggle!&lt;/strong&gt;", 'accessible-reading'); ?>[/accessible_reading]</pre>

                                <?php echo (sprintf(
                                    __("This shortcode saves it's content to a post's meta data, so if you're using it in a global area of your website like your header, footer, or sidebar, you should assign the shortcode a post or page ID to save it once to avoid saving it to every post or page it's visible on by setting the %s attribute like this:", 'accessible-reading'),
                                    '<code>post_id</code>'
                                )); ?>
                                <pre>[accessible_reading id="david" post_id="23"]<?php printf(__("This content will always save and pull it's accessible content from post ID %s", 'accessible-reading'), '23'); ?>[/accessible_reading]</pre>

                                <?php echo (sprintf(
                                    __("If you want to temporarily disable the automatic processing without removing the shortcode, you can set the %s attribute to %s like this:", 'accessible-reading'),
                                    '<code>disabled</code>',
                                    '<code>1</code>'
                                )); ?>
                                <pre>[accessible_reading id="bowie" disabled="1"]<?php _e("This content won't be accessible, nor will it have a toggle. &lt;em&gt;Yet&lt;/em&gt;.", 'accessible-reading'); ?>[/accessible_reading]</pre>
                            </div>
                        </div>
                        <div>
                            <h3 class="au-accordion-item-title"><?php _e('Display the toggle switch anywhere', 'accessible-reading'); ?></h3>
                            <div>
                                <?php echo (sprintf(
                                    __("The %s shortcode shortcode allows you to add a toggle switch anywhere on your WordPress website. To use it, simply copy paste this self-closing shortcode:", 'accessible-reading'),
                                    '<code>accessible_reading_toggle</code>'
                                )); ?>
                                <pre>[accessible_reading_toggle/]</pre>
                            </div>
                        </div>
                        <div>
                            <h3 class="au-accordion-item-title"><?php _e('Display pre-made accessible content when toggled on', 'accessible-reading'); ?></h3>
                            <div>
                                <?php echo (sprintf(
                                    __("The %s shortcode allows you to add pre-made accessible content anywhere on your WordPress website that a toggle switch will display when turned on.", 'accessible-reading'),
                                    '<code>accessible_reading_content</code>'
                                )); ?><br /><br />
                                <?php printf(__("To use it, simply wrap the accessible content with the shortcode. For example:", 'accessible-reading'), 'id'); ?>
                                <pre>[accessible_reading_content]<?php esc_html_e(htmlentities('<span class="bionic-w bionic"><b class="bionic-b bionic">Lor</b>em</span> <span class="bionic-w bionic"><b class="bionic-b bionic">ips</b>um</span> <span class="bionic-w bionic"><b class="bionic-b bionic">dol</b>or</span> <span class="bionic-w bionic"><b class="bionic-b bionic">s</b>it</span> <span class="bionic-w bionic"><b class="bionic-b bionic">am</b>et.</span>')); ?>[/accessible_reading_content]</pre>
                                <?php echo (sprintf(
                                    __("You can optionally add additional HTML classes to the outermost wrapper by setting the value to the %s attribute. If setting multiple classes, they should be separated by a single space. For example:", 'accessible-reading'),
                                    '<code>classes</code>'
                                )); ?>
                                <pre>[accessible_reading_content classes="custom-class-1 custom-class-2"]<?php esc_html_e(htmlentities('<span class="bionic-w bionic"><b class="bionic-b bionic">Lor</b>em</span> <span class="bionic-w bionic"><b class="bionic-b bionic">ips</b>um</span> <span class="bionic-w bionic"><b class="bionic-b bionic">dol</b>or</span> <span class="bionic-w bionic"><b class="bionic-b bionic">s</b>it</span> <span class="bionic-w bionic"><b class="bionic-b bionic">am</b>et.</span>')); ?>[/accessible_reading_content]</pre>
                                <?php _e('You can generate accessible content for this shortcode using the official <a href="https://app.bionic-reading.com/" target="_blank" rel="noopener noreferrer">Bionic Reading<sup>&reg;</sup> web application</a>.', 'accessible-reading'); ?>
                            </div>
                        </div>
                        <div>
                            <h3 class="au-accordion-item-title"><?php _e('Hide original content when toggled on', 'accessible-reading'); ?></h3>
                            <div>
                                <?php echo (sprintf(
                                    __("To complement the %s shortcode, the %s shortcode allows you to add the original content that a toggle switch will hide when accessible reading is turned on.", 'accessible-reading'),
                                    '<code>accessible_reading_content</code>',
                                    '<code>accessible_reading_original</code>'
                                )); ?><br /><br />
                                <?php printf(__("To use it, simply wrap the original content with the shortcode. For example:", 'accessible-reading'), 'id'); ?>
                                <pre>[accessible_reading_original]Lorem ipsum dolor sit amet.[/accessible_reading_original]</pre>
                                <?php echo (sprintf(
                                    __("You can optionally add additional HTML classes to the outermost wrapper by setting the value to the %s attribute. If setting multiple classes, they should be separated by a single space. For example:", 'accessible-reading'),
                                    '<code>classes</code>'
                                )); ?>
                                <pre>[accessible_reading_original classes="custom-class-1 custom-class-2"]Lorem ipsum dolor sit amet.[/accessible_reading_original]</pre>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="about" class="tab">
                    <h2><?php _e('Your brain reads faster than your eyes!', 'accessible-reading'); ?></h2>
                    <p><?php _e('We have an internal dictionary of learned words and reading the first few letters can be enough to recognize whole words.', 'accessible-reading'); ?></p>
                    <p><?php _e('Bionic Reading® is a new method facilitating the reading process by guiding the eyes through text with artificial fixation points. As a result, the reader is only focusing on the highlighted initial letters and lets the bran center complete the word. In a digital world dominated by shallow forms of reading, Bionic Reading aims to encourage more in-depth reading and understanding of written content.', 'accessible-reading'); ?></p>
                    <p><a href="<?php echo (esc_url(sprintf(
                                    'https://aurisecreative.com/click/?utm_source=%s&utm_medium=website&utm_campaign=wordpress-plugin&utm_content=%s&utm_term=',
                                    str_replace(array('https://', 'http://'), '', home_url()), //UTM Source
                                    $args['plugin_settings']['slug']
                                ) . 'learn-more-about-bionic-reading&redirect=' . urlencode('https://bionic-reading.com/'))); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Learn More about Bionic Reading', 'accessible-reading'); ?></a></p>
                    <p><small><?php _e('This WordPress plugin is not a product of <strong>Bionic Reading<sup>&reg;</sup></strong>. It was developed by an independent and disabled developer, <strong>Tessa Watkins of AuRise Creative</strong>, that wants to bring more accessibility to the digital space.', 'accessible-reading'); ?></small></p>
                </section>
            </div>
        </div>
    </div>
    <?php load_template($args['plugin_settings']['path'] . 'templates/dashboard-support.php', true, $args['plugin_settings']); ?>
</div>