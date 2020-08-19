<?php
namespace ZRDN;
defined('ABSPATH') or die("you do not have acces to this page!");
class ZRDN_Nutrition_Label_Shortcode
{
    private static $_this;

    function __construct()
    {
        if (isset(self::$_this))
            wp_die(sprintf(__('%s is a singleton class and you cannot create a second instance.', 'zip-recipes'), get_class($this)));

        self::$_this = $this;

        add_shortcode("zrdn-nutrition-label", array($this, "nutrition_label"));
    }

    static function this()
    {
        return self::$_this;
    }

    public function nutrition_label($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $atts = shortcode_atts(['recipe_id' => 0], $atts, $tag);
        $recipe_id = intval($atts['recipe_id']);
        $recipe = new Recipe($recipe_id);
        ob_start();
        echo zrdn_label_markup($recipe, array(), true);

        return ob_get_clean();
    }

}//class closure
$shortcode = new ZRDN_Nutrition_Label_Shortcode();
