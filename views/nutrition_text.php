<?php if ( $recipe->has_nutrition_data ){ ?>

	<?php if ( $recipe->calories ) {
	    $calories = $recipe->calories;
		preg_match_all('/(^.*?)\./', $recipe->calories, $matches);
		if (isset($matches[1][0])) {
			$calories = $matches[1][0];
        }
	    ?>
        <div class="zrdn-text-nutrition-item calories">
            <div class="zrdn-nutrition-title"><?php _e( 'Calories', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($calories) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->calories_daily) ?></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->fat ) { ?>
        <div class="zrdn-text-nutrition-item fat">
            <div class="zrdn-nutrition-title"><?php _e( 'Fat', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->fat) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->fat_daily) ?></div>
        </div>
	<?php } ?>

    <?php if ( $recipe->saturated_fat ) { ?>
        <div class="zrdn-text-nutrition-item saturated-fat">
            <div class="zrdn-nutrition-title"><?php _e( 'Saturated', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->saturated_fat) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->saturated_fat_daily) ?></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->carbs ) { ?>
        <div class="zrdn-text-nutrition-item carbs">
            <div class="zrdn-nutrition-title"><?php _e( 'Carbs', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->carbs) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->carbs_daily) ?></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->protein ) { ?>
        <div class="zrdn-text-nutrition-item protein">
            <div class="zrdn-nutrition-title"><?php _e( 'Protein', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->protein) ?></div>
            <div class="zrdn-nutrition-percentage"></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->fiber ) { ?>
        <div class="zrdn-text-nutrition-item fiber">
            <div class="zrdn-nutrition-title"><?php _e( 'Fiber', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->fiber) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->fiber_daily) ?></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->sugar ) { ?>
        <div class="zrdn-text-nutrition-item sugar">
            <div class="zrdn-nutrition-title"><?php _e( 'Sugar', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->sugar) ?></div>
            <div class="zrdn-nutrition-percentage"></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->sodium ) { ?>
        <div class="zrdn-text-nutrition-item sodium">
            <div class="zrdn-nutrition-title"><?php _e( 'Sodium', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->sodium) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->sodium_daily) ?></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->trans_fat ) { ?>
        <div class="zrdn-text-nutrition-item trans_fat">
            <div class="zrdn-nutrition-title"><?php _e( 'Trans fat', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->trans_fat) ?></div>
            <div class="zrdn-nutrition-percentage"></div>
        </div>
	<?php } ?>

	<?php if ( $recipe->cholesterol ) { ?>
        <div class="zrdn-text-nutrition-item cholesterol">
            <div class="zrdn-nutrition-title"><?php _e( 'Cholesterol', 'zip-recipes' ) ?></div>
            <div class="zrdn-nutrition-value"><?php echo \ZRDN\Util::minimal_number($recipe->cholesterol) ?></div>
            <div class="zrdn-nutrition-percentage"><?php echo \ZRDN\Util::minimal_number($recipe->cholesterol_daily) ?></div>
        </div>
	<?php } ?>

    <?php
	$class = '';
    if ( isset($settings['hide_nutrition_text_expl']) && $settings['hide_nutrition_text_expl']) $class='zrdn-hidden'?>
    <div class="zrdn-text-nutrition-explanation <?php echo $class?>">
    <?php _e( 'Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.',
        'zip-recipes' ) ?>
    </div>
<?php }
