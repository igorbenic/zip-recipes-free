<?php
$label_class = apply_filters('zrdn_label_class','zrdn-recipe-label', 'details');

if ($recipe->yield){
	$yield = $recipe->yield;

	preg_match_all('!\d+!', $recipe->yield, $matches);
	if ( isset( $matches[0][0] )) {
	    $yield = $matches[0][0];
    }
    ?>
    <div class="zrdn-details-item">
        <div class="<?php echo $label_class?>">
	        <?php echo apply_filters('zrdn_label', __('Persons', 'zip-recipes'), 'persons')?>
        </div>
	    <div class="zrdn-value zrdn-yield zrdn-element_yield"><?php echo $yield?></div>
    </div>
<?php } ?>

<?php if($recipe->serving_size) { ?>
    <div class="zrdn-details-item zrdn-serving-size">
        <div class="<?php echo $label_class?>">
	        <?php echo apply_filters('zrdn_label', __('Serving Size', 'zip-recipes'), 'serving-size')?>
        </div>
        <div class="zrdn-value zrdn-element_serving_size">{serving_size}</div>
    </div>
<?php } ?>

<?php if($recipe->prep_time_formatted){  ?>
	<div class="zrdn-details-item zrdn-prep-time">
        <div class="<?php echo $label_class?>">
	        <?php echo apply_filters('zrdn_label', __('Prep Time', 'zip-recipes'), 'prep-time')?>
        </div>
        <div class="zrdn-value zrdn-element_prep_time">{prep_time_formatted}</div>
	</div>
<?php } ?>

<?php if($recipe->cook_time_formatted){ ?>
	<div class="zrdn-details-item zrdn-cook-time">
        <div class="<?php echo $label_class?>">
	        <?php echo apply_filters('zrdn_label', __('Cook Time', 'zip-recipes'), 'cook-time')?>
        </div>
        <div class="zrdn-value zrdn-element_cook_time">{cook_time_formatted}</div>
	</div>
<?php } ?>

<?php if($recipe->wait_time_formatted){ ?>
    <div class="zrdn-details-item zrdn-wait-time">
        <div class="<?php echo $label_class?>">
			<?php echo apply_filters('zrdn_label', __('Wait Time', 'zip-recipes'), 'wait-time')?>
        </div>
        <div class="zrdn-value zrdn-element_wait_time">{wait_time_formatted}</div>
    </div>
<?php } ?>

<?php if($recipe->total_time_formatted){ ?>
	<div class="zrdn-details-item zrdn-total-time">
        <div class="<?php echo $label_class?>">
	        <?php echo apply_filters('zrdn_label', __('Total Time', 'zip-recipes'), 'total-time')?>
        </div>
        <div class="zrdn-value zrdn-element_total_time">{total_time_formatted}</div>
	</div>
<?php } ?>






