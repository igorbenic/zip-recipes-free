<?php
namespace ZRDN;

class AutomaticNutrition extends PluginBase
{
    const VERSION = "1.0";
    const INDEX_PAGE_ID = "zrdn-automatic-nutrition";
    public $suffix = '';

    function __construct ()
    {
	    $this->suffix = ( defined( 'WP_SCRIPT_DEBUG' ) && WP_SCRIPT_DEBUG ) ? '' : '.min';
	    add_filter('zrdn__db_recipe_columns', array($this, 'recipe_table_columns'), 10, 1);
	    add_filter('zrdn__recipe_field_names', array($this, 'recipe_field_names'), 10, 1);
	    add_action('zrdn_nutrition_fields', array($this, 'add_nutrition_field'));
	    add_action('wp_ajax_zrdn_update_nutrition_generate', array($this, 'update_nutrition_generate'));
	    add_action('wp_ajax_zrdn_update_nutrition_delete', array($this, 'update_nutrition_delete'));
	    add_filter('zrdn_edit_nutrition_fields', array($this, 'add_ingredient_field'), 10, 2);
	    add_filter("zrdn_get_fields", array($this, 'enable_nutrition_field'), 10, 2);
	    add_action('admin_footer', array($this, 'fire_all_recipes_import_ajax'));
	    add_action('wp_ajax_zrdn_import_nutrition_all_recipes', array($this, 'ajax_update_all_recipes'));
    }

	public function enable_nutrition_field($fields, $type){

		if ( $type === 'nutrition' && $this->nutrition_api_allowed() ) {
			$fields['AutomaticNutrition']['disabled'] =  false;
			$fields['nutrition_label_type']['disabled'] =  false;
			$fields['import_nutrition_data_all_recipes']['disabled'] =  false;

			if ( Util::get_option('import_nutrition_data_all_recipes') ) {
			    if ($this->import_completed()) {
				    $fields['import_nutrition_data_all_recipes']['comment'] =  __("Import completed", "zip-recipes");
			    } else {
				    $current_position = get_option('zrdn_import_nutrition_recipes_offset');
				    $count = get_transient('zrdn_nr_of_recipes');

				    $fields['import_nutrition_data_all_recipes']['comment'] =  sprintf(__("Import %s of %s in progress", "zip-recipes"), $current_position, $count);
			    }
			}
			if (get_option('zrdn_nutrition_data_import_completed')) {
				unset($fields['import_nutrition_data_all_recipes']);
			}
		}
		return $fields;
	}

	public function ajax_update_all_recipes(){
		if (!current_user_can('manage_options')) return;

		if (!Util::get_option('import_nutrition_data_all_recipes')) return;
		if ($this->import_completed()) return;

		$this->update_all_recipes();

		$current_position = get_option('zrdn_import_nutrition_recipes_offset');
		$count = get_transient('zrdn_nr_of_recipes');

		$data = array(
			'success' => true,
			'offset' => $current_position,
			'total' => $count,
		);
		$response = json_encode($data);
		header("Content-Type: application/json");
		echo $response;
		exit;

    }

	public function fire_all_recipes_import_ajax(){
        if (!current_user_can('manage_options')) return;

        if (!Util::get_option('import_nutrition_data_all_recipes')) return;
        if ($this->import_completed()) return;
		$count = get_transient('zrdn_nr_of_recipes');

        ?>
        <script>
            jQuery(document).ready(function ($) {
                var updateStr = '<?php printf(__("Import %s of %s in progress", "zip-recipes"), '{offset}', $count);?>'
                    //btn.html('<div class="zrdn-loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
                zrdnImportNutritionData();
	            function zrdnImportNutritionData(){
                    $.ajax({
                        type: "POST",
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        dataType: 'json',
                        data: ({
                            action: 'zrdn_import_nutrition_all_recipes',
                        }),
                        success: function (response) {
                            if (response.success===true) {
                                var str = updateStr.replace('{offset}', response.offset);
                                $('input[name=zrdn_import_nutrition_data_all_recipes]').closest('.field-group').find('.zrdn-comment').html(str);
                            }
                            if (response.offset < response.total){
                                setTimeout(function () {
                                    zrdnImportNutritionData();
                                }, 500);
                            }

                        }
                    });

                }
            });

        </script>
        <?php
    }

    public function getLocale()
    {
        $defaultLocale = 'en';
        $actualLocale = substr(get_locale(), 0, 2);
        if(in_array($actualLocale, array('en'))) {
            return $actualLocale;
        }else {
            return $defaultLocale;
        }
    }

    private function parse_yield($yield){
        $pattern = '/(\b\d+(?:[\.,\'"]\d+)?\b)/i';
        if (preg_match($pattern, $yield, $matches)) {
            return  $matches[1];
        }

        //default to 4.
        return 4;
    }


    public function add_ingredient_field($fields, $recipe){
        unset($fields['nutrition_promo']);
        $added_fields = array(
                array(
                    'type' => 'checkbox',
                    'fieldname' => 'enable_ingredients_alt',
                    'value' => $recipe->enable_ingredients_alt,
                    'label' => __("Use separate ingredients list for nutrition generation", 'zip-recipes'),
                    'help' => __("If you're not using English or Spanish, or you just want to exclude some ingredients from the nutrition generation, you can use this field, which is used for the nutrition data only","zip-recipes"),
                ),
                array(
                    'type' => 'textarea',
                    'fieldname' => 'ingredients_alt',
                    'value' => $recipe->ingredients_alt,
                    'label' => __("Ingredients for nutrition generation", 'zip-recipes'),
                    'condition' => array('enable_ingredients_alt' => true)
                ),
            );
        return array_merge($added_fields, $fields );
    }

    public function update_nutrition_generate(){
        $error = false;
        $result = false;
        $msg = '';

        if (!$error && !current_user_can('edit_posts')) {
            $error = true;
            $msg = __("You do not have edit permissions.","zip-recipes");
        }

        if (!wp_verify_nonce($_POST['nonce'], 'zrdn_update_nutrition')) {
            $error = true;
            $msg = __('An unexpected error has occurred. Please try again', "zip-recipes");
        }

        if (!$error && ! extension_loaded('curl')) {
            $error = true;
            $msg = __("Your PHP is mis-configured. It's missing CURL extension. Please contact your host.","zip-recipes");
        }
	    //Get recipe
	    $recipe_id = intval($_POST['recipe_id']);

	    $recipe = new Recipe($recipe_id);
	    $recipe->load();

	    if (!$recipe) {
		    $error = true;
		    $msg = __('Recipe not found. Please save your recipe first.', "zip-recipes");
	    }

	    //make sure the last changes in the ingredients (which might not be saved yet) are saved before continuing
	    if (isset($_POST["formData"])){
		    $data = $_POST["formData"];
		    foreach ( $data as $field ) {
			    if ( !isset($field['name']) ) {
				    continue;
			    }

			    if ($field['name'] == 'zrdn_ingredients'){
				    $recipe->ingredients = $field['value'];
			    }

			    if ($field['name'] == 'zrdn_enable_ingredients_alt'){
				    $recipe->enable_ingredients_alt = $field['value'];
			    }

			    if ($field['name'] == 'zrdn_ingredients_alt'){
				    $recipe->ingredients_alt = $field['value'];
			    }
		    }
	    }

	    //in case anything was changed, save the ingredients.
	    $recipe->save();

        if (!$error){
	        $msg = $this->get_nutrition_data($recipe);

	        if ($msg) $error = true;

	        if (!$error) {
		        //we reload to make sure we get the new daily percentages calculated
		        $recipe->load();
            }
        }

        if (!$error){
            $msg = __("Successfully generated nutrition data","zip-recipes");
        }

        $data = array(
            'success' => !$error,
            'msg' => $msg,
            'nutrition_data' => $recipe->nutrition_data(),
        );
        $response = json_encode($data);
        header("Content-Type: application/json");
        echo $response;
        exit;
    }

	/**
	 * Chunked update of all recipes
	 */
    public function update_all_recipes(){
        $max = 500;
        if (Util::get_option('import_nutrition_data_all_recipes') && !$this->import_completed() ) {
            $batch = 1;
            //count total
            $count = get_transient('zrdn_nr_of_recipes');
            if (!$count) {
	            $args = array(
		            'number' => -1,
	            );
	            $recipes = Util::get_recipes($args);
	            $count = count($recipes);
	            set_transient('zrdn_nr_of_recipes', $count, HOUR_IN_SECONDS);
            }

	        $offset = get_option('zrdn_import_nutrition_recipes_offset', 0);

	        if ( ($offset >= $max) || ($offset > $count) ) {
                //reset it and stop import
		        update_option('zrdn_import_nutrition_recipes_offset', 0);
	            update_option("zrdn_nutrition_data_import_completed", true);
            } else {
	            $args = array(
		            'offset' => $offset,
		            'number' => $batch,
	            );
	            $recipes = Util::get_recipes($args);

	            foreach ($recipes as $recipe){
		            $recipe = new Recipe($recipe->recipe_id);
		            $this->get_nutrition_data($recipe);
	            }
	            $offset += $batch;

	            update_option('zrdn_import_nutrition_recipes_offset', $offset);
            }

        }
    }

    public function import_completed(){
        return get_option("zrdn_nutrition_data_import_completed");
    }

    public function nutrition_api_allowed(){

	    $sharing_enabled = get_option( 'zrdn_enable_recipe_selling' ) && Util::get_option('recipe_selling_share_all_published');
	    $license_valid = false;
	    if ( defined('ZRDN_PREMIUM') ){
		    $zrdn_license = new licensing();
		    $license_valid = $zrdn_license->license_is_valid();
	    }

	    if ( $license_valid || $sharing_enabled ) {
	        return true;
	    } else {
	        return false;
	    }
    }

	/**
     * Get nutrition data for a recipe
	 * @param Recipe $recipe
	 *
	 * @return bool|string
	 */

    public function get_nutrition_data($recipe){
        $msg = $error = $response = false;


	    if ( !$this->nutrition_api_allowed() ){
		    $error = true;
		    $msg = __('Usage of the Nutrition Generator requires either a valid license, or sharing of all your recipes.', "zip-recipes");
	    }

	    if ( !function_exists('curl_version') ) {
		    $error = true;
		    $msg = __('CURL is not installed on your server, which is needed for this function. Please contact your hosting company to get CURL installed on your server.', "zip-recipes");
        }

	    if (!$error){
		    $endpoint = ZRDN_API_URL . '/v4/recipes/';
		    $ingredients_list = $recipe->enable_ingredients_alt ? $recipe->ingredients_alt : $recipe->ingredients;
		    $ingredients_list = $this->parse_ingredients($ingredients_list);

		    //blog name may be empty
		    $website_name = strlen(get_bloginfo('name'))>0 ? get_bloginfo('name') : get_bloginfo('url');

		    $data = array(
			    "ingredients"=> $ingredients_list,
			    "title"=> $recipe->recipe_title,
			    "servings" => $this->parse_yield($recipe->yield),
			    "servings_unit" => $recipe->serving_size,
			    "language" => $this->getLocale(),
			    "license" => get_option('zrdn_license_key'),
			    "website_url" =>  get_bloginfo('url'),
			    "website_name" => $website_name,
		    );
		    $data = json_encode($data);
		    $ch = curl_init();

		    curl_setopt($ch, CURLOPT_URL, $endpoint);
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    //this appears to be necessary on some servers.
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				    'Content-Type: application/json',
				    'Content-Length: ' . strlen($data))
		    );

		    $result = curl_exec($ch);
		    //{"id":6205,"title":"page title","servings":"1 people","servings_unit":"1","nutrition_label_url":"https://api.ziprecipes.net/media/167c0dca-b52f-4de2-8c67-05b3fe7c1346.png","ingredients":[{"id":60292,"text":"1 ounce chocolate"}],"language":"en","nutrition":{"Energy":"136.08 kcal","Fat":"8.5 g","Saturated":"5.03 g","Monounsaturated":"2.83 g","Polyunsaturated":"0.27 g","Carbs":"18.12 g","Fiber":"1.67 g","Sugars":"15.45 g","Sugars, added":"15.45 g","Protein":"1.19 g","Sodium":"3.12 mg","Calcium":"0.91 %","Magnesium":"32.6 mg","Potassium":"103.48 mg","Iron":"4.93 %","Zinc":"0.46 mg","Phosphorus":"37.42 mg","Thiamin (B1)":"0.02 mg","Riboflavin (B2)":"0.03 mg","Niacin (B3)":"0.12 mg","Vitamin B6":"0.01 mg","Folate equivalent (total)":"3.69 µg","Folate (food)":"3.69 µg","Vitamin E":"0.07 mg","Vitamin K":"1.59 µg"}}

		    $error = ($result === false) ? true : false;
		    if ($error) {
			    $response = curl_error($ch);
			    $msg = sanitize_text_field($response);
		    }

		    curl_close($ch);
		    // generate the response
	    }

	    if (!$error){
	        if (strpos($response, 'Expecting value')!==FALSE){
		        $error = true;
		        $msg = __('The nutrition API responded with an error. Please try again later. If the problem persists, please contact support.','zip-recipes');
	        }
	    }

	    if (!$error) {
		    $result = json_decode($result);
	    }

	    if (!$error) {
		    $resultdata = get_object_vars($result);
		    foreach ($resultdata as $fieldname => $value) {
			    if (!is_array($value) || !isset($value[0]) || !is_string($value[0])) continue;
			    if (strpos($value[0], 'This field may not be blank')!==FALSE) {
				    $error = true;
				    $msg = sprintf(__('The %s field should not be empty','zip-recipes'),$fieldname);
			    }
		    }

	    }

	    if (!$error) {
		    if (isset($result->error)) {
			    $error = true;
			    if (strpos($result->error, 'trouble understanding your recipe')!==FALSE){
				    $msg = __('We had trouble understanding your ingredients. Please try removing some to see if any are not recognized by the API. If needed, you can create a separte API ingredients field, by checking the option below.','zip-recipes');
			    } else {
				    $msg = $result->error;
			    }

		    }
	    }

	    //check response for error messages
	    if (!$error){
		    if (isset($result->error)){
			    $error = true;
			    $msg = $result->error;
		    }
		    if ($result && isset($result->ingredients) && is_array($result->ingredients)){
			    foreach ($result->ingredients as $error_msg){
				    if (isset($error_msg->non_field_errors) && is_array($error_msg->non_field_errors)){
					    $error = true;
					    $msg = __('We had trouble understanding your ingredients. Please try removing some to see if any are not recognized by the API. If needed, you can create a separte API ingredients field, by checking the option below.','zip-recipes');
				    }
			    }
		    }
	    }

	    if (!$error){
		    $image_url = isset($result->nutrition_label_url) ? $result->nutrition_label_url : false;

		    $nutrition_data_object = isset($result->nutrition) ? $result->nutrition : false;
		    $nutrition_data =  (array) $nutrition_data_object;

		    /**
		    [Energy] => 1936.74 kcal
		    [Fat] => 212.7 g
		    [Saturated] => 134.22 g
		    [Trans] => 8.2 g
		    [Monounsaturated] => 55.65 g
		    [Polyunsaturated] => 7.95 g
		    [Carbs] => 16.41 g
		    [Fiber] => 1.48 g
		    [Sugars] => 14.0 g
		    [Sugars, added] => 13.62 g
		    [Protein] => 3.6 g
		    [Cholesterol] => 545.3 mg
		    [Sodium] => 56.12 mg
		    [Calcium] => 7.49 %
		    [Magnesium] => 34.39 mg
		    [Potassium] => 161.03 mg
		    [Iron] => 4.77 %
		    [Zinc] => 0.67 mg
		    [Phosphorus] => 100.51 mg
		    [Vitamin A] => 192.88 %
		    [Thiamin (B1)] => 0.03 mg
		    [Vitamin C] => 0.03 %
		    [Riboflavin (B2)] => 0.12 mg
		    [Niacin (B3)] => 0.22 mg
		    [Vitamin B6] => 0.02 mg
		    [Folate equivalent (total)] => 11.53 µg
		    [Folate (food)] => 11.53 µg
		    [Vitamin B12] => 0.44 µg
		    [Vitamin D] => 3.79 µg
		    [Vitamin E] => 5.89 mg
		    [Vitamin K] => 19.11 µg
		     **/

		    //update recipe data
		    if (isset($nutrition_data['Energy'])) $recipe->calories = $nutrition_data['Energy'];
		    if (isset($nutrition_data['Trans'])) $recipe->trans_fat = $nutrition_data['Trans'];
		    if (isset($nutrition_data['Saturated'])) $recipe->saturated_fat = $nutrition_data['Saturated'];
		    if (isset($nutrition_data['Cholesterol'])) $recipe->cholesterol = $nutrition_data['Cholesterol'];
		    if (isset($nutrition_data['Vitamin C'])) $recipe->vitamin_c = $nutrition_data['Vitamin C'];
		    if (isset($nutrition_data['Fat']))$recipe->fat = $nutrition_data['Fat'];
		    if (isset($nutrition_data['Carbs']))$recipe->carbs = $nutrition_data['Carbs'];
		    if (isset($nutrition_data['Fiber']))$recipe->fiber = $nutrition_data['Fiber'];
		    if (isset($nutrition_data['Sugars']))$recipe->sugar = $nutrition_data['Sugars'];
		    if (isset($nutrition_data['Protein']))$recipe->protein = $nutrition_data['Protein'];
		    if (isset($nutrition_data['Sodium']))$recipe->sodium = $nutrition_data['Sodium'];
		    if (isset($nutrition_data['Calcium']))$recipe->calcium = $nutrition_data['Calcium'];
		    if (isset($nutrition_data['Iron']))$recipe->iron = $nutrition_data['Iron'];
		    if (isset($nutrition_data['Vitamin A'])) $recipe->vitamin_a = $nutrition_data['Vitamin A'];
		    if (isset($nutrition_data['Vitamin C']))$recipe->vitamin_c = $nutrition_data['Vitamin C'];

		    if ( !$image_url ) {
			    $msg = __("Did not receive nutrition image url","zip-recipes");
		    }

		    //if the "image" display method is selected, download the label.
		    if (Util::get_option('nutrition_label_type')==='image') {
			    $attachment_id = $this->download_label($image_url, $recipe->recipe_title);
			    if (!$attachment_id) {
				    $error = true;
				    $msg = __("Could not download nutrition", "zip-recipes");
			    }
			    $recipe->nutrition_label_id = $attachment_id;
		    }
		    $recipe->save();
	    }

	    return $msg;
    }

	/**
	 * Clear nutrition data
	 */

    public function update_nutrition_delete(){
        $error = false;
        $msg = '';

        if (!wp_verify_nonce($_POST['nonce'], 'zrdn_update_nutrition')) {
            $error = true;
            $msg = __('An unexpected error has occurred. Please try again', "zip-recipes");
        }


        //Get recipe
        $recipe_id = intval($_POST['recipe_id']);
        $recipe = new Recipe($recipe_id);
        $recipe->load();

        if (!$recipe) {
            $error = true;
            $msg = __('Recipe not found. Please save your recipe first.', "zip-recipes");
        }

        if (!$error){

            //clear all nutrition data
            $recipe->calories = 0;
            $recipe->fat = 0;
            $recipe->carbs = 0;
            $recipe->fiber = 0;
            $recipe->sugar = 0;
            $recipe->protein = 0;
            $recipe->sodium = 0;
            $recipe->calcium = 0;
            $recipe->iron = 0;
            $recipe->vitamin_a = 0;
            $recipe->vitamin_c = 0;
            $success = false;

            if ($recipe->nutrition_label_id) {
                $success = wp_delete_attachment($recipe->nutrition_label_id,true);
            }else {
                //legacy, old data stored as url instead of id
                if ($recipe->nutrition_label){
                    $attachment_id = attachment_url_to_postid($recipe->nutrition_label);
                    if ($attachment_id){
                        $success = wp_delete_attachment($attachment_id,true);
                    }
                }
            }

            //now clear the image as well from the recipe
            $recipe->nutrition_label = false;
            $recipe->nutrition_label_id = false;

            $recipe->save();

            //we reload to make sure we get the daily percentages recalculated
            $recipe->load();
        }

        if (!$error){
            $msg = __("Successfully cleared nutrition data","zip-recipes");
        }

        $data = array(
            'success' => !$error,
            'msg' => $msg,
            'nutrition_data' => $recipe->nutrition_data(),
        );

        $response = json_encode($data);
        header("Content-Type: application/json");
        echo $response;
        exit;
    }


    /**
     * Download the nutrtition label from the api, and store it in WP media
     * @param string $src
     * @param string $title
     * @return int
     */

    private function download_label($src, $title){
        $attachment_id = false;

	    require_once( ABSPATH . 'wp-admin/includes/file.php' );
	    $uploads    = wp_upload_dir();
	    $upload_dir = $uploads['path'];
	    $upload_url = trailingslashit($uploads['url']);

	    if ( ! file_exists( $upload_dir ) ) {
		    mkdir( $upload_dir );
	    }

	    //set the path
	    $filename_no_ext = sanitize_title($title);
	    $safe_title = sanitize_title($title) .  '.png';
	    $filename_url = $upload_url."/".$safe_title;
	    $filename_dir = $upload_dir."/".$safe_title;

	    //download file
	    $tmpfile = download_url( $src, $timeout = 25 );

	    //check for errors
	    if ( !is_wp_error( $tmpfile ) ) {
		    if ( file_exists( $filename_dir ) ) {
			    unlink( $filename_dir );
		    }

		    //in case the server prevents deletion, we check it again.
		    if ( ! file_exists( $filename_dir ) ) {
			    copy( $tmpfile, $filename_dir );
		    }
		    $file_type = wp_check_filetype(basename( $filename_dir ), null );
		    $args = array(
			    'guid'           => $filename_url,
			    'post_mime_type' => $file_type['type'],
			    'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename_no_ext ),
			    'post_content'   => '',
			    'post_status'    => 'inherit'
		    );

		    $attachment_id = wp_insert_attachment( $args, $filename_dir);
		    $attach_data = wp_generate_attachment_metadata( $attachment_id, $filename_dir );
		    wp_update_attachment_metadata( $attachment_id, $attach_data );
	    }

	    if ( is_string( $tmpfile ) && file_exists( $tmpfile ) ) {
		    unlink( $tmpfile );
	    }

        return $attachment_id;
    }


    /**
     * Clean formatting from ingredients and return in format acceptable for API
     * @param string $ingredients
     * @return array() $ingredients
     */

    public function parse_ingredients($ingredients){

        $image = '/%(\S*)/i';
        $secondary_title = '/^!(.*)/i';
        $link = '/\[([^\]\|\[]*)\|([^\]\|\[]*)\]/i';
        $bold = '/(^|\s)\*([^\s\*][^\*]*[^\s\*]|[^\s\*])\*(\W|$)/i';
        $italic = '/(^|\s)_([^\s_][^_]*[^\s_]|[^\s_])_(\W|$)/i';

        $ingredients = explode(PHP_EOL, $ingredients);
        $ingredients_list = array();
        foreach ($ingredients as $ingredient){
            $ingredient = preg_replace(array($image, $secondary_title, $link, $bold, $italic), array('', '', '$1','$2','$2'),$ingredient );
            $ingredient = trim(strip_tags($ingredient));
            if (empty($ingredient)) continue;
            $ingredients_list[] = array('text' => $ingredient);
        }

        return $ingredients_list;

    }





    /**
     * Add the Nutrition field to the Recipe Editor
     * @param $recipe
     */
    public function add_nutrition_field($recipe){

        ?>
        <script>
            jQuery(document).ready(function ($) {
                var successDiv = $(".zrdn-success.zrdn-nutrition-warning");
                var warningDiv = $(".zrdn-warning.zrdn-nutrition-warning");
                var originalSuccessHtml = successDiv.html();
                var originalWarningHtml = warningDiv.html();

                if ($(".nutrition-label img").length) {
                    $(".nutrition-label img").attr('src',$('input[name=zrdn_nutrition_label').val());
                }

                $(document).on('click', '.nutrition_button', function (e) {

                    e.preventDefault();
                    var btn = $(this);
                    var oldBtnHtml = btn.html();
                    //to make sure any changes in ingredients will get processed, we submit the form
                    var formdata = $("#recipe-settings").serializeArray();

                    btn.prop('disabled', true);
                    btn.html('<div class="zrdn-loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');

                    var action = btn.data('action');
                    var recipe_id = btn.parent().data('recipe_id');
                    var nonce = btn.parent().data('nonce');

                    //reset html to start state
                    successDiv.html(originalSuccessHtml);
                    warningDiv.html(originalWarningHtml);

                    $.ajax({
                        type: "POST",
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        dataType: 'json',
                        data: ({
                            action: 'zrdn_update_nutrition_'+action,
                            recipe_id:recipe_id,
                            formData: formdata,
                            nonce:nonce,
                        }),
                        success: function (response) {
                            btn.prop('disabled', false);
                            btn.html(oldBtnHtml);
                            if (response.success===true) {

                                delay = 1000;

                                //update data in page.
                                //it's already updated in the backend, so only for appearances, and to prevent reverting on save.
                                for (var name in response.nutrition_data) {
                                    if (response.nutrition_data.hasOwnProperty(name)) {
                                        var value = response.nutrition_data[name];
                                        var placeholder = 'zrdn_placeholder_'+name;
                                        if ($('#'+placeholder).length) $('#'+placeholder).html(value);
                                        var fieldname = 'zrdn_'+name;
                                        $('input[name='+fieldname).val(value);

                                        //image
                                        if ($(".nutrition-label img").length) {
                                            $(".nutrition-label img").attr('src',$('input[name=zrdn_nutrition_label').val());
                                        }
                                    }
                                }

                                var alertDiv = successDiv;

                            } else {
                                var alertDiv = warningDiv;
                                delay = 4000;
                            }
                            var alert = alertDiv.html().replace('{zrdn_notice}', response.msg);
                            alertDiv.html(alert).show(100, function(){
                                alertDiv.delay(delay).fadeOut(2000);
                            });
                        }
                    });

                });

            });


        </script>
        <div class="field-group  ">
            <div class="zrdn-label">
                <label for="yield"><?php _e("Automatic Nutrition generation", "zip-recipes")?></label>
            </div>
            <div class="zrdn-field" >
                <?php
                $nutritionlabelclass = '';

                if (!$recipe->recipe_id){
                    echo '<div id="nutrition_save_recipe_first">';
                    _e("To generate the Nutrition data, you should save the recipe first","zip-recipes");
                    echo "</div>";
                    $nutritionlabelclass = 'zrdn-hidden';
                } ?>
                <div id="nutrition_action_buttons" class="<?php echo $nutritionlabelclass?>">
                    <div class="zrdn-hidden zrdn-warning zrdn-nutrition-warning"><?php zrdn_notice('{zrdn_notice}','warning')?></div>
                    <div class="zrdn-hidden zrdn-success zrdn-nutrition-warning"><?php zrdn_notice('{zrdn_notice}','success')?></div>
                    <span data-recipe_id="<?php echo $recipe->recipe_id?>" data-nonce="<?php echo wp_create_nonce('zrdn_update_nutrition')?>">
                        <button class="button nutrition_button" type="button" data-action="generate"><?php echo $recipe->has_nutrition_data ?__("Update","zip-recipes") : __("Generate label", "zip-recipes")?></button>
                        <button class="button nutrition_button" type="button" data-action="delete"><?php _e("Clear label", "zip-recipes")?></button>
                    </span>
                </div>
            </div>
        </div>
        <script src="https://developer.edamam.com/attribution/badge.js"></script>
        <div id="edamam-badge" data-color="white"></div>
        <?php

    }

    /**
     * Adds the field for the URL to the label, img version
     * @param $columns
     * @return mixed
     */

    public function recipe_table_columns($columns) {
        array_push($columns, 'nutrition_label varchar(200)');
        return $columns;
    }



    public function recipe_field_names($fields) {
        array_push($fields, 'nutrition_label');

        return $fields;
    }
}
