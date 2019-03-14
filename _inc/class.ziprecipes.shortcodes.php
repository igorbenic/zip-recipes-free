<?php
/**
 * Created by PhpStorm.
 * User: noilleris
 * Date: 16.09.16
 * Time: 18:15
 */

namespace ZRDN;


class __shortcode {
    function __construct ()
    {
        Util::log("In constructor");
        add_action("zrdn__init_hooks", array($this, 'init_hooks'));
    }

    public function init_hooks()
    {
        Util::log("In init_hooks");

        // Shortcode
        if ( !shortcode_exists( 'ziprecipes' ) ) {
            add_shortcode('ziprecipes', array($this, 'shortcode'));
        }
    }

    public function shortcode($atts) {
        return apply_filters('zrdn__shortcode', '', $atts);
    }
} 