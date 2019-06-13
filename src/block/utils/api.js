import axios from 'axios';

/**
 * Makes a get request to the PostTypes endpoint.
 *
 * @returns {AxiosPromise<any>}
 */

export const getPostTypes = () => axios.get(zrdn.site_url+'/wp-json/wp/v2/types');

/**
 * Makes a get request to the desired post type and builds the query string based on an object.
 *
 * @param {string|boolean} restBase - rest base for the query.
 * @param {object} args
 * @returns {AxiosPromise<any>}
 */
export const getRecipes = () => {
    return axios.get(zrdn.site_url+`/wp-json/zip-recipes/v1/recipes`);
};

export const getRecipe = (recipe_id) => {
    return axios.get(zrdn.site_url+`/wp-json/zip-recipes/v2/recipe/`+recipe_id);
};