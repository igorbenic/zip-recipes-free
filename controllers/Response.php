<?php

namespace ZRDN;

/**
 * REST Response Helper Class
 *
 * JSON base Restful API for Zip Recipes Operations.
 *
 * PHP version 5.3
 *
 * LICENSE: GPLv3 or later
 * Depends: WP_REST_Response
 *
 * @package    Zip Recipes
 * @author     Mudassar Ali <sahil_bwp@yahoo.com>
 * @copyright  2017 Gezim Hoxha
 * @version 1.0
 * @link https://github.com/hgezim/zip-recipes-plugin Zip Recipes
 * @example  wp-json/zip-recipes/v2/recipe
 */
use WP_REST_Response;
use WP_Http;

class ZRDN_REST_Response {

    /**
     *  REST Response
     *
     * @param $code
     * @param $response
     * @return WP_REST_Response
     */
    public static function response($code, $response) {
        return new WP_REST_Response($response, $code);
    }

    /**
     * Rest Error Response
     *
     * @param $response
     * @param int $code
     * @return WP_REST_Response
     */
    public static function error($response, $code = WP_Http::NOT_FOUND) {
        return self::response($code, array(
                    'code' => $code,
                    'message' => $response
        ));
    }

    /**
     * Rest Success Response
     *
     * @param $response
     * @param int $code
     * @return WP_REST_Response
     */
    public static function success($response, $code = WP_Http::OK) {
        return self::response($code, $response);
    }

}
