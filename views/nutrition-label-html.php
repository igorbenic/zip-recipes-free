<section class="nutrition-facts">
    <div>
        <header class="nutrition-facts__header">
            <h4 class="nutrition-facts__title"><?php _e( 'Nutrition Facts',
					'zip-recipes' ) ?></h4>
            <h5 class="nutrition-facts__title">
                <span>
                {recipe_title}
                </span>
            </h5>
            <p><?php _e( 'Serves', 'zip-recipes' ) ?>: <span>{yield}</span>
        </header>
        <table class="nutrition-facts__table">
            <thead>
            <tr>
                <th colspan="3" class="small-info">
					<?php _e( 'Amount Per Serving', 'zip-recipes' ) ?>: <div class="nutrition-serving-size">{serving_size}</div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th colspan="2">
                    <strong><?php _e( 'Calories', 'zip-recipes' ) ?></strong>
                </th>
                <td>
                    <span class="zrdn-fieldvalue-calories">{calories}</span>
                </td>
            </tr>
            <tr class="thick-row">
                <td colspan="3" class="small-info">
                    <strong><?php _e( '% Daily Value*', 'zip-recipes' ) ?></strong>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <strong><?php _e( 'Total Fat', 'zip-recipes' ) ?></strong>
                    <span class="zrdn-fieldvalue-fat">{fat}</span>
                </th>
                <td>
                    <strong class="zrdn-fieldvalue-fat_daily">{fat_daily}</strong>
                </td>
            </tr>
            <tr>
                <td class="blank-cell">
                </td>
                <th>
					<?php _e( 'Saturated Fat', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-saturated_fat">{saturated_fat}</span>
                </th>
                <td>
                    <strong class="zrdn-fieldvalue-saturated_fat_daily">{saturated_fat_daily}</strong>
                </td>
            </tr>
            <tr>
                <td class="blank-cell">
                </td>
                <th>
					<?php _e( 'Trans Fat', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-trans_fat">{trans_fat}</span>
                </th>
                <td>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <strong><?php _e( 'Cholesterol', 'zip-recipes' ) ?></strong>
                    <span class="zrdn-fieldvalue-cholesterol">{cholesterol}</span>
                </th>
                <td>
                    <strong class="zrdn-fieldvalue-cholesterol_daily">{cholesterol_daily}</strong>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <strong><?php _e( 'Sodium', 'zip-recipes' ) ?></strong>
                    <span class="zrdn-fieldvalue-sodium">{sodium}</span>
                </th>
                <td>
                    <strong class="zrdn-fieldvalue-sodium_daily">{sodium_daily}</strong>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <strong><?php _e( 'Total Carbohydrate', 'zip-recipes' ) ?></strong>
                    <span class="zrdn-fieldvalue-carbs">{carbs}</span>
                </th>
                <td>
                    <strong class="zrdn-fieldvalue-carbs_daily">{carbs_daily}</strong>
                </td>
            </tr>
            <tr>
                <td class="blank-cell">
                </td>
                <th>
					<?php _e( 'Dietary Fiber', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-fiber">{fiber}</span>
                </th>
                <td>
                    <strong class="zrdn-fieldvalue-fiber_daily">{fiber_daily}</strong>
                </td>
            </tr>
            <tr>
                <td class="blank-cell">
                </td>
                <th>
					<?php _e( 'Sugars', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-sugar">{sugar}</span>
                </th>
                <td>
                </td>
            </tr>
            <tr class="thick-end">
                <th colspan="2">
                    <strong><?php _e( 'Protein', 'zip-recipes' ) ?></strong>
                    <span class="zrdn-fieldvalue-protein">{protein}</span>
                </th>
                <td>
                </td>
            </tr>
            </tbody>
        </table>

        <table class="nutrition-facts__table--grid">
            <tbody>
            <tr>
                <td colspan="2">
					<?php _e( 'Vitamin A', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-vitamin_a">{vitamin_a}</span>
                </td>
                <td>
					<?php _e( 'Vitamin C', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-vitamin_c">{vitamin_c}</span>
                </td>
            </tr>
            <tr class="thin-end">
                <td colspan="2">
					<?php _e( 'Calcium', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-calcium">{calcium}</span>
                </td>
                <td>
					<?php _e( 'Iron', 'zip-recipes' ) ?>
                    <span class="zrdn-fieldvalue-iron">{iron}</span>
                </td>
            </tr>
            </tbody>
        </table>

        <p class="small-info"><?php _e( '* Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.',
				'zip-recipes' ) ?></p>

        <p class="copyright">
            {site_name}
        </p>
    </div>
</section>
