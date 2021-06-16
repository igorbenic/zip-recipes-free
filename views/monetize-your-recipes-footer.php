<?php use ZRDN\Recipe;
$status = \ZRDN\zrdn_use_rdb_api() ? 'active' : 'disabled';
$status_text = \ZRDN\zrdn_use_rdb_api() ? __('Active', 'zip-recipes') : __('Disabled', 'zip-recipes');

wp_nonce_field('zrdn_save', 'zrdn_nonce');
?>
<div class="zrdn-save-button">
    <div class="zrdn-button-border">
        <input class="button button-primary" type="submit" name="zrdn-save" value="<?php _e("Save", 'zip-recipes') ?>">
    </div>
    <div class="zrdn-footer-status">
        <div class="zrdn-icon zrdn-bullet <?php echo $status ?>"></div>
        <span><?php echo $status_text ?></span>
    </div>
</div>