<?php
namespace ZRDN;

/**
 * Show a notice with some info
 * @param string $msg
 * @param string $type notice | warning | success
 * @param bool $echo
 * @param bool $include_css
 * @param bool $fadeout
 * @return string|void
 */
function zrdn_notice($msg, $type = 'notice', $echo = true, $include_css=false, $fadeout=false)
{
    if ($msg == '') return;
    $html = "";
    if ($include_css){
        $html .= "<style>
            .zrdn-panel {
              color: #fff;
              background-color: #29b6f6;
              border: 1px solid #29b6f6;
              padding: 10px 15px;
              border-radius: 0.25rem;
              margin: 10px 0;
            }

            .zrdn-panel.zrdn-success {
              background-color: #61ce70;
              border-color: #61ce70;
            }
            .zrdn-panel.zrdn-warning {
              background-color: #f8be2e;
              border-color: #f8be2e;
            }
            </style>";
    }
    $uid = rand (10,10000);
    if ($fadeout) {
        $html .= "
        <script>
            jQuery(document).ready(function ($) {
                console.log('hide alert');
                var zrdn_alert = $('.alert-".$uid."');
                if (zrdn_alert.length) {
                    zrdn_alert.delay(1500).fadeOut(800);
                }
            });
        </script>";
    }
    $html .= '<div class="zrdn-panel alert-'.$uid.' zrdn-' . $type . ' ">' . $msg . '</div>';
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}
