<?php $label_class = apply_filters('zrdn_label_class','zrdn-recipe-label', 'category');?>
<?php if($recipe->cuisine){ ?>
    <?php do_action("zrdn_before_element", "cuisine")?>
    <span class="<?php echo $label_class?> zrdn-cuisine-label"><?php echo apply_filters('zrdn_label', __('Cuisine', 'zip-recipes'), 'cuisine')?></span>
    <span>
        <span class="zrdn-cuisine zrdn-element_cuisine">{cuisine}</span>
    </span>
<?php } ?>
	<div class="zrdn-cuisine-category-divider"></div>
<?php
if (is_array($recipe->categories) && count($recipe->categories)>0){
	do_action("zrdn_before_element", 'category');
	?>
    <span class="<?php echo $label_class?> zrdn-category-label"><?php echo apply_filters('zrdn_label', __('Category', 'zip-recipes'), 'category')?></span>
    <span class="zrdn-element_category">
	<?php
	foreach ($recipe->categories as $category_id ) {
        $cat = get_category($category_id);
        ?>
        <a class="zrdn-category-item" href="<?php echo get_category_link($category_id)?>"><?php echo $cat->name?></a>
        <?php
    }
	?></span><?php
}

 if((!is_array($recipe->categories) || count($recipe->categories)==0) && $recipe->category){
	 do_action("zrdn_before_element", 'category');
	 ?>
    <span class="<?php echo $label_class?> zrdn-category-label"><?php echo apply_filters('zrdn_label', __('Category', 'zip-recipes'), 'category')?></span>
    <span class="zrdn-element_category">

	 <?php
	 $categories = explode(",", $recipe->category);
 	 foreach ($categories as $category){ ?>
	    <span class="zrdn-category-item"><?php echo $category?></span>
    <?php } ?>
    </span>
<?php } ?>



