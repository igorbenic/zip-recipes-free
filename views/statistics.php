<?php
use ZRDN\Recipe;
use ZRDN\Util;
defined( 'ABSPATH' ) or die();
if (\ZRDN\zrdn_use_rdb_api()) {
    $active_recipes = zrdn_get_nr_of_recipes_by_sharing_status('approved');
} else {
    $active_recipes = 0;
}

$recipes_needs_improvement = zrdn_get_nr_of_recipes_by_sharing_status('needs_improvement');
$recipes_waiting = zrdn_get_nr_of_recipes_by_sharing_status('waiting_approval');
$recipes_rejected = zrdn_get_nr_of_recipes_by_sharing_status('declined');
$recipes_disabled = zrdn_get_nr_of_recipes_by_sharing_status('not_activated');
$estimated_revenue = intval($active_recipes + $recipes_waiting + $recipes_needs_improvement + $recipes_disabled -1 );//we subtract one for demo recipe. then intval to make sure it's positive

//get most populair recipes
$recipe_args = array(
    'order_by' => 'hits',
    'order' => 'DESC',
    'post_status' => 'publish',
    'number' => 1,
);
$most_popular_recipe = Util::get_recipes( $recipe_args );
$most_popular_url = esc_url( admin_url( "admin.php?page=zrdn-recipes&id=". $most_popular_recipe[0]->recipe_id ."") );

function zrdn_get_nr_of_recipes_by_sharing_status( $sharing_status ){
    global $wpdb;
    $recipes_table = $wpdb->prefix . Recipe::TABLE_NAME;
	$demo_recipe_id = get_option('zrdn_demo_recipe_id');

	$query = "SELECT count(*) as nr_of_recipes FROM $recipes_table where recipe_id != $demo_recipe_id AND post_id != 0 AND zip_sharing_status = '" . $sharing_status ."'";
    $result = $wpdb->get_results($query);
    $rows = $result[0];
    $count = $rows->nr_of_recipes;

    return $count;
}

?>

<div class="statistics-container statistics_revenue">
    <div class="statistics-container__header">
        <p><?php _e("Potential revenue", "zip-recipes")?></p>
        <p><b>$<?php printf(__("%s,-/month", "zip-recipes"), $estimated_revenue) ?></b></p>
    </div>
    <div class="statistics-container__content">
        <div class="statistics-status-display">
            <div class="zrdn-bullet green"></div>
            <div class="statistics-status-display__text">
                <p><?php _e("Active", "zip-recipes") ?></p>
                <a href="<?php echo esc_url( admin_url( "admin.php?page=zrdn-recipes" ) )  ?>"><?php echo sprintf(_n("%s recipe", "%s recipes", $active_recipes, "zip-recipes"), $active_recipes) ?></a>
            </div>
        </div>
        <div class="statistics-status-display">
            <div class="zrdn-bullet green"></div>
            <div class="statistics-status-display__text">
                <p><?php _e("Most popular", "zip-recipes") ?></p>
                <a href="<?php echo $most_popular_url ?>"><?php _e("View post", "zip-recipes") ?></a>
            </div>
        </div>
    </div>
</div>

<div class="statistics-container statistics_warning">
    <div class="statistics-container__header">
        <p>
            <?php _e("Warning", "zip-recipes")?>
            <span class="zrdn-tooltip-top tooltip-right" data-zrdn-tooltip="<?php _e("These recipes need your attention before they can be approved.", "zip-recipes")?>">
                <span class="zrdn-tooltip-icon dashicons dashicons-editor-help"></span>
            </span>
        </p>
    </div>
    <div class="statistics-container__content">
        <div class="statistics-status-display">
            <div class="zrdn-bullet orange"></div>
            <div class="statistics-status-display__text">
                <p><?php _e("Needs improvement", "zip-recipes") ?></p>
                <a href="<?php echo esc_url( admin_url( "admin.php?page=zrdn-recipes" ) )  ?>"><?php echo sprintf(_n("%s recipe", "%s recipes", $recipes_needs_improvement, "zip-recipes"), $recipes_needs_improvement) ?></a>
            </div>
        </div>
        <div class="statistics-status-display">
            <div class="zrdn-bullet orange"></div>
            <div class="statistics-status-display__text">
                <p><?php _e("Waiting", "zip-recipes") ?></p>
                <a href="<?php echo esc_url( admin_url( "admin.php?page=zrdn-recipes" ) )  ?>"><?php echo sprintf(_n("%s recipe", "%s recipes", $recipes_waiting, "zip-recipes"), $recipes_waiting) ?></a>
            </div>
        </div>
    </div>
</div>

<div class="statistics-container statistics_action_required">
    <div class="statistics-container__header">
            <p>
                <?php _e("Action required", "zip-recipes")?>
                <span class="zrdn-tooltip-top tooltip-right" data-zrdn-tooltip="<?php _e("These recipes are either disabled by you or rejected by our partner. Please revise if necessary.", "zip-recipes")?>">
                    <span class="zrdn-tooltip-icon dashicons dashicons-editor-help"></span>
                </span>
            </p>

    </div>
    <div class="statistics-container__content">
        <div class="statistics-status-display">
            <div class="zrdn-bullet red"></div>
            <div class="statistics-status-display__text">
                <p><?php _e("Rejected", "zip-recipes") ?></p>
                <a href="<?php echo esc_url( admin_url( "admin.php?page=zrdn-recipes" ) )  ?>"><?php echo sprintf(_n("%s recipe", "%s recipes", $recipes_rejected, "zip-recipes"), $recipes_rejected) ?></a>
            </div>
        </div>
        <div class="statistics-status-display">
            <div class="zrdn-bullet red"></div>
            <div class="statistics-status-display__text">
                <p><?php _e("Disabled", "zip-recipes") ?></p>
                <a href="<?php echo esc_url( admin_url( "admin.php?page=zrdn-recipes" ) )  ?>"><?php echo sprintf(_n("%s recipe", "%s recipes", $recipes_disabled, "zip-recipes"), $recipes_disabled) ?></a>
            </div>
        </div>
    </div>
</div>