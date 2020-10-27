<?php if ( empty($recipe->nested_ingredients) && !$recipe->preview ) return;?>
<?php $label_class = apply_filters('zrdn_label_class','zrdn-recipe-label zrdn-ingredients-label', 'ingredients'); ?>
<h3 class="<?php echo $label_class?>">
    <?php echo apply_filters('zrdn_label', __('Ingredients', 'zip-recipes') , 'ingredients' )?>
</h3>

<?php do_action('zrdn_before_ingredients', $recipe, $settings);?>

<?php
$list_style = $settings['ingredients_list_type'];
$list_type = \ZRDN\Util::get_list_type($list_style);
$list_class = \ZRDN\Util::get_list_class($list_style);
if ($recipe->preview) {
    $recipe->nested_ingredients[] =
        array(
            array(
            'type' => 'default',
            'content' => '{ingredients_value}'
        ),
    );
}
echo '<' . $list_type . ' class="zrdn-list zrdn-ingredients-list '.$list_class.' zrdn-element_ingredients">';
foreach ( $recipe->nested_ingredients as $ingredient_lines ) {
    foreach ($ingredient_lines as $ingredient) {
        if ( $ingredient['type'] == 'image' ) { ?>
            <img class="<?php if($settings['hide_print_image']) echo "zrdn-hide-print" ?>" src="<?php echo $ingredient['attributes']['url']; ?>"
            <?php if($ingredient['attributes']['srcset']){ ?> srcset="<?php echo $ingredient['attributes']['srcset']?>"<?php } ?>
            <?php if($ingredient['attributes']['sizes']){?> sizes="<?php echo $ingredient['attributes']['sizes'] ?>"<?php } ?>
            <?php if($ingredient['attributes']['title']){ ?> alt="<?php echo $ingredient['attributes']['title'] ?>" <?php } ?>
            />
        <?php } elseif ( $ingredient['type'] == 'subtitle' ) { ?>

            <?php echo '</' . $list_type . '>';?>
            <h4 class="zrdn-subtitle">
                <?php echo $ingredient['content'] ?>
            </h4>
            <?php echo '<' . $list_type . ' class="zrdn-list zrdn-ingredients-list  '.$list_class.'">';?>

        <?php } else { ?>

            <li><?php echo $ingredient['content'] ?></li>

        <?php } ?>
    <?php } ?>
<?php } ?>
<?php echo '</' . $list_type . '>';?>


