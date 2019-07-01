<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2017-06-08
 * Time: 17:08
 */

namespace ZRDN;

/**
 * Remove blank values from JSON-LD. It looks through nested arrays and considers them to be blank if
 *  the all of their keys start with @. E.g.: @type, @context.
 * @param $arr array in JSON LD format.
 * @return array
 */
function clean_jsonld($arr) {
    $cleaned_crap = array_reduce(array_keys($arr), function ($acc, $key) use ($arr) {
        $value = $arr[$key];
        if (is_array($value)) {
            $cleaned_array = clean_jsonld($value);
            // add array if it has keys that don't start with @
            $array_has_data = count(array_filter(array_keys($cleaned_array), function ($elem) {
                    return substr($elem, 0, 1) !== '@';
                })) > 0;

            if ($array_has_data) {
                $acc[$key] = $cleaned_array;
            }
        }
        else {
            if ($value !== "") {
                $acc[$key] = $value;
            }
        }

        return $acc;
    }, array());

    return $cleaned_crap;
}

/**
 * @param string $msg
 * @param string $type notice | warning | success
 * @param bool $hide
 * @param bool $echo
 * @return string|void
 */
function zrdn_notice($msg, $type = 'notice', $echo = true, $include_css=false, $fadeout=false)
{
    if ($msg == '') return;
    $html = "";
    if ($include_css){
        $html .= "<style>
            .zrdn-panel {
              color: #383d41;
              background-color: #e2e3e5;
              border: 1px solid #d6d8db;
              padding: 10px 15px;
              border-radius: 0.25rem;
              margin: 10px 0;
            }
            .zrdn-panel.zrdn-notice {
              background-color: #d9edf7;
              border-color: #bcdff1;
              color: #31708f;
            }
            .zrdn-panel.zrdn-success {
              background-color: #dff0d8;
              border-color: #d0e9c6;
              color: #3c763d;
            }
            .zrdn-panel.zrdn-warning {
              color: #856404;
              background-color: #fff3cd;
              border-color: #ffeeba;
            }
            </style>";
    }
    $uid = time();
    if ($fadeout) {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                var alert = $('.alert-<?php echo $uid?>');
                if (alert.length) {
                    alert.delay(1500).fadeOut(800);
                }
            });
        </script>
        <?php
    }
    $html .= '<div class="zrdn-panel alert-'.$uid.' zrdn-' . $type . ' ">' . $msg . '</div>';
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}
