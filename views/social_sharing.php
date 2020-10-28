<h4 class="zrdn-recipe-label zrdn-social-sharing-label"><?php _e("Share on social", "zip-recipes")?></h4>
<?php
$url = get_permalink();
$title = $recipe->recipe_title;

$type = \ZRDN\Util::get_option('social_icon_type');

$services = array(
    array(
        'name' => 'facebook',
        'action' => 'href="#" onClick="zrdnPopupCenter(\'https://www.facebook.com/share.php?u='.$url.'&width&layout=standard&action=share&show_faces=true&share=true&height=80\',\'Facebook\',600, 500); return false;"',
        'color' => '#3b5998',
    ),

//    array(
//        'name' => 'instagram',
//        'action' => "",
//        'color' => '#000000',
//    ),

	array(
		'name' => 'pinterest',
		'action' => "href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;https://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'",
		'color' => '#E60023',
	),

    array(
        'name' => 'reddit',
        'action' => 'href="#" onClick="zrdnPopupCenter(\'http://www.reddit.com/submit?url='.$url.'&title='.esc_html($title).'\',\'Reddit\',600,500); return false;"',
        'color' => '#E60023',
    ),

    array(
        'name' => 'twitter',
        'action' => 'href="#" onClick="zrdnPopupCenter(\'https://twitter.com/share?url='.$url.'\',\'Twitter\',600,500); return false;"',
        'color' => '#1da1f2',
    ),

	array(
		'name' => 'whatsapp',
		'action' => 'href="whatsapp://send?text='.$url.'" data-action="/share/whatsapp/share"',
		'color' => '#25d366',
	),

);
?>
<script>
    function zrdnPopupCenter(url, title, w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;
        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
        if (window.focus) {newWindow.focus();}
    }
</script>
<div class="zrdn-social-container">
    <?php foreach ($services as $service ) {?>
        <a class="zrdn-share <?php echo $service['name']?>"
           <?php echo $service['action']?>>
            <?php
            $color = $service['color'];
            if ( $type !== 'logo' ) $color = isset($settings['primary_color']) ? $settings['primary_color'] : '#000000';
            echo \ZRDN\Util::get_social_svg($service['name'], $type, $color);
            ?>
        </a>
    <?php } ?>
</div>