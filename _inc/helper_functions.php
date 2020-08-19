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
