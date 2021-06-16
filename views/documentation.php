<?php

$items = array(
    1 => array(
        'content' => __("Everything about monetizing your recipes", "zip-recipes"),
        'link'    => 'https://ziprecipes.net/everything-about-monetizing-your-recipes/',
    ),
    2 => array(
        'content' => __("Everything about monetizing your recipes", "zip-recipes"),
        'link'    => 'https://ziprecipes.net/everything-about-monetizing-your-recipes/',
    ),
    3 => array(
        'content' => __("Everything about monetizing your recipes", "zip-recipes"),
        'link'    => 'https://ziprecipes.net/everything-about-monetizing-your-recipes/',
    ),
    4 => array(
        'content' => __("Everything about monetizing your recipes", "zip-recipes"),
        'link'    => 'https://ziprecipes.net/everything-about-monetizing-your-recipes/',
    ),
    5 => array(
        'content' => __("Everything about monetizing your recipes", "zip-recipes"),
        'link'    => 'https://ziprecipes.net/everything-about-monetizing-your-recipes/',
    ),
    6 => array(
        'content' => __("Everything about monetizing your recipes", "zip-recipes"),
        'link'    => 'https://ziprecipes.net/everything-about-monetizing-your-recipes/',
    ),
);
$see_all =
$container = '<div class="zrdn-documentation-element"><a href="{link}" target="_blank"><div class="zrdn-bullet"></div><div class="zrdn-documentation-content">{content}</div></a></div>';
$output = '<div class="zrdn-documentation-container">';

foreach ($items as $item) {
    $output .= str_replace(array(
        '{link}',
        '{content}',
    ), array(
        $item['link'],
        $item['content'],
    ), $container);
}
$output .= '</div><div class="zrdn-documentation-footer zrdn-save-button"><a href="">'.__("See all", "zip-recipes").'</a> </div>';
echo $output;


