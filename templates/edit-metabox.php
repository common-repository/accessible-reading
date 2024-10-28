<div class="au-metabox-<?php esc_attr_e($args['plugin_settings']['slug']); ?>">
    <?php if ($args['has_api_configured']) : ?>
        <p><em><?php echo (wp_filter_post_kses(sprintf(
                    __('Your website has used <strong>%s of %s API requests</strong> today.', 'accessible-reading'),
                    esc_attr($args['api_request_count']),
                    esc_attr($args['api_request_limit'])
                ))); ?></em></p>
        <div class="process-dry-run">
            <p class="output"><?php esc_html_e($args['api_request_estimate']); ?></p>
            <input type="hidden" readonly disabled name="post_id" value="<?php esc_attr_e($args['post']['ID']); ?>" />
            <input type="hidden" readonly disabled name="post_type" value="<?php esc_attr_e($args['post']['type']); ?>" />
            <button class="button button-primary" type="submit"><?php _e('Estimate API Usage for this Post', 'accessible-reading'); ?></button>
            <span class="progress-spinner" style="display: none;"><img src="<?php echo (esc_url($args['plugin_settings']['url'])); ?>assets/images/progress.gif" alt="" width="32" height="32" /></span>
        </div>
        <fieldset>
            <?php foreach ($args['fields'] as $name => $field) {
                printf(
                    '<p class="input-field">%s%s</p>',
                    $field['input'],
                    $field['description'] ? sprintf('<span class="note">%s</span>', esc_html($field['description'])) : ''
                );
            } ?>
        </fieldset>
    <?php else : ?>
        <p><strong><?php _e('API Key is not configured!', 'accessible-reading'); ?></strong>&nbsp;<?php _e('Please edit the global settings to add your API key.', 'accessible-reading'); ?></p>
    <?php endif; ?>
    <p class="plugin-settings-link">
        <a href="<?php echo (esc_url($args['plugin_settings']['admin_url'])); ?>#settings"><?php _e('Edit Global Settings', 'accessible-reading'); ?></a>
        <?php if ($args['has_api_configured']) : ?>
            <a href="<?php echo (esc_url($args['plugin_settings']['admin_url'])); ?>#bulk-update"><?php _e('Bulk Update', 'accessible-reading'); ?></a>
        <?php endif; ?>
    </p>
</div>