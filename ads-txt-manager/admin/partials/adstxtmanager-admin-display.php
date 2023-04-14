<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if ( $type == 'permalinks_disabled' ) :?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<div class="notice notice-warning is-dismissible adstxtmanager_activate">
		<p class="adstxtmanager_description">
            <?php _e('<strong>Permalinks must be enabled!</strong> - Ads.txt manager wont work unless permalinks are enabled!', 'adstxtmanager');?>
            <br/><br/>
            <a href="/wp-admin/options-permalink.php" class="button button-primary" ><?php esc_attr_e( 'Enable', 'adstxtmanager' ); ?></a>
        </p>
	</div>
</div>
<?php elseif ( $type == 'no_id' ) :?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<div class="notice notice-warning is-dismissible adstxtmanager_activate">
		<p class="adstxtmanager_description">
            <?php _e('<strong>No Ads.txt Manager ID</strong> - Ads.txt Manager wont work unless you have updated your ID setting:', 'adstxtmanager');?>
            <br/><br/>
            <a href="/wp-admin/options-general.php?page=adstxtmanager-setting-admin" class="button button-primary" ><?php esc_attr_e( 'Update ID', 'adstxtmanager' ); ?></a>
            <!--<a href="<?php /*esc_attr_e( ADSTXT_MANAGER__SITE_LOGIN, 'adstxtmanager' ); */?>" target="_blank">Login</a>
            <a href="<?php /*esc_attr_e( ADSTXT_MANAGER__SITE, 'adstxtmanager' ); */?>" target="_blank">Create Account</a>-->
        </p>
	</div>
</div>
<?php elseif ( $type == 'permalinks_disabled+no_id' ) :?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<div class="notice notice-warning is-dismissible adstxtmanager_activate">
		<p class="adstxtmanager_description">
            <?php _e('<strong>Permalinks must be enabled!</strong> - Ads.txt Manager wont work unless permalinks are enabled!', 'adstxtmanager');?>
            <br/><br/>
            <a href="/wp-admin/options-permalink.php" class="button button-primary" ><?php esc_attr_e( 'Enable', 'adstxtmanager' ); ?></a>
        </p>
	</div>
</div>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<div class="notice notice-warning is-dismissible adstxtmanager_activate">
		<p class="adstxtmanager_description">
            <?php _e('<strong>No Ads.txt Manager ID</strong> - Ads.txt Manager wont work unless you have updated your ID setting:', 'adstxtmanager');?>
            <br/><br/>
            <a href="/wp-admin/options-general.php?page=adstxtmanager-setting-admin" class="button button-primary" ><?php esc_attr_e( 'Update ID', 'adstxtmanager' ); ?></a>
        </p>
	</div>
</div>
<?php endif;?>
