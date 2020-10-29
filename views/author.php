<?php if ($recipe->author) {
    $link_close = $link_open = '';
    if ($recipe->author_id) {
        echo '<div class="zrdn-avatar">';
        echo get_avatar($recipe->author_id, 96, '', $recipe->author, array('extra_attr' => 'longdesc="'.$recipe->author.'"'));
        $url = get_author_posts_url( $recipe->author_id);
        $link_open ='<a href="'.$url.'">';
        $link_close = '</a>';
        echo '</div>';
    }

    $date = get_the_date('', $recipe->post_id); ?>
    <div class="zrdn-date"><?php echo apply_filters('zrdn_recipe_date', $date);?></div>
    <div class="zrdn-author-name"><span class="zrdn-author-by"><?php _e("by", "zip-recipes")?>&nbsp;</span><span class="zrdn-element_author"><?php echo apply_filters('zrdn_recipe_author',$link_open.$recipe->author.$link_close)?></span></div>
<?php } ?>
