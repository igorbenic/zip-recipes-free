
<?php // Add the print button, but not for amp, as the print button requires javascript
//https://github.com/ampproject/amphtml/blob/master/examples/standard-actions.amp.html ?>
<?php if($settings['add_print_button']){ ?>
	<?php if($settings['amp_on']){ ?>
		<button on="tap:AMP.print"><?php _e('Print', 'zip-recipes') ?></button>
	<?php } ?>

	<div class="zrdn-print-link">
		<?php
		if (!isset($settings['print_image']) || !$settings['print_image']) {
			$settings['print_image'] = ZRDN_PLUGIN_URL .'/images/print.png';
		} ?>
		<a title="<?php _e('Print this recipe','zip-recipes') ?>" href="javascript:void(0);" onclick="zlrPrint('zrdn-recipe-container', '<?php echo ZRDN_PLUGIN_URL ?>'); return false" rel="nofollow">
            <img src="{print_image}" alt="<?php _e("Print this recipe", 'zip-recipes')?>" longdesc="<?php _e("Print this recipe", 'zip-recipes')?>">
		</a>
	</div>
<?php } ?>