import axios from 'axios';

export const getGrid = (args) => {
    var html = axios.get(zrdn.site_url+`/wp-json/zip-recipes/v1/grid/`, {
        params: args
    });
    return html;
};


export const getCategories = (args) => {
    return axios.get(zrdn.site_url+`/wp-json/zip-recipes/v1/categories/`);
};