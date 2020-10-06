<?php defined('ABSPATH') or die("you do not have access to this page!"); ?>

<?php
/**
 * Get status link for plugin, depending on installed, or premium availability
 * @param $item
 *
 * @return string
 */

function zrdn_get_status_link($item){
	if (!defined($item['constant_free']) && !defined($item['constant_premium'])) {
		$link = admin_url() . "plugin-install.php?s=".$item['search']."&tab=search&type=term";
		$text = __('Install', 'zip-recipes');
		$status = "<a href=$link>$text</a>";
	} elseif (defined($item['constant_free']) && !defined($item['constant_premium'])) {
		$link = $item['website'];
		$text = __('Upgrade to pro', 'zip-recipes');
		$status = "<a href=$link>$text</a>";
	} elseif (defined($item['constant_premium'])) {
		$status = __("Installed", "zip-recipes");
	}
	return $status;
}

$plugins = array(
	'WPSI' => array(
		'constant_free' => 'wpsi_plugin',
		'constant_premium' => 'wpsi_pro_plugin',
		'website' => 'https://wpsearchinsights.com/pro',
		'search' => 'WP+Search+Insights+really+simple+plugins+searches+complianz',
	),
	'COMPLIANZ' => array(
		'constant_free' => 'cmplz_plugin',
		'constant_premium' => 'cmplz_premium',
		'website' => 'https://complianz.io/pricing',
		'search' => 'complianz',
	),
	'RSSSL' => array(
		'constant_free' => 'rsssl_plugin',
		'constant_premium' => 'rsssl_pro_plugin',
		'website' => 'https://really-simple-ssl.com/pro',
		'search' => 'really-simple-ssl really simple plugins complianz',
	),
);
?>
<div>
	<div class="zrdn-otherplugins-upsell zrdn-wpsi">
		<div class="plugin-color">
			<div class="wpsi-red zrdn-bullet"></div>
		</div>
		<div class="plugin-text">
			<a href="https://wordpress.org/plugins/wp-search-insights/" target="_blank"><?php _e("WP Search Insights - Track searches on your website")?></a>
		</div>
		<div class="plugin-status">
			<?php echo zrdn_get_status_link($plugins['WPSI'])?>
		</div>
	</div>
	<div class="zrdn-otherplugins-upsell zrdn-cmplz">
		<div class="plugin-color">
			<div class="cmplz-blue zrdn-bullet"></div>
		</div>
		<div class="plugin-text">
			<a href="https://wordpress.org/plugins/complianz-gdpr/" target="_blank"><?php _e("Complianz Privacy Suite - Consent Management as it should be ", "zip-recipes")?></a>
		</div>
		<div class="plugin-status">
			<?php echo zrdn_get_status_link($plugins['COMPLIANZ'])?>
		</div>
	</div>
	<div class="zrdn-otherplugins-upsell zrdn-rsssl">
		<div class="plugin-color">
			<div class="rsssl-yellow zrdn-bullet"></div>
		</div>
		<div class="plugin-text"><a href="https://wordpress.org/plugins/zip-recipes/" target="_blank">
				<?php _e("Really Simple SSL - Your Website SSL in one click", "zip-recipes")?></a>
		</div>
		<div class="plugin-status">
			<?php echo zrdn_get_status_link($plugins['RSSSL'])?>
		</div>
	</div>
</div>