jQuery(document).ready(function ($) {
    'use strict';
    var zrdnGrid = $('#zrdn-recipe-grid');
    window.use_ajax = zrdn_grid_settings.use_ajax;

    // init cubeportfolio
    zrdnGridInit();
    //recalculate every 1500 ms
    var zip_recalc = setInterval(zrdnRecalculate, 500);
    function zrdnRecalculate() {
        zrdnGrid.cubeportfolio('layout');
        zrdnRecalculateFilterCounts();
    }
//zrdn-loadmore-items
    $.fn.donetyping = function(callback){
        var _this = $(this);
        var x_timer;
        _this.keyup(function (){
            clearTimeout(x_timer);
            x_timer = setTimeout(clear_timer, 1000);
        });

        function clear_timer(){
            clearTimeout(x_timer);
            callback.call(_this);
        }
    };

    function zrdnGridInit() {
        $('#zrdn-recipe-grid').cubeportfolio({
            filters: '#zrdn-recipe-grid-filters',
            mediaQueries: [{
                width: 1500,
                cols: zrdn_grid_settings.colsXL,
            }, {
                width: 1100,
                cols: zrdn_grid_settings.colsL,
            }, {
                width: 800,
                cols: zrdn_grid_settings.colsM,
            }, {
                width: 480,
                cols: zrdn_grid_settings.colsS,
                options: {
                    // gapHorizontal: 30,
                    // gapVertical: 10,
                }
            }],
            defaultFilter: '*',
            animationType: zrdn_grid_settings.animationType,
            layoutMode: zrdn_grid_settings.layoutMode,
            gapVertical: parseInt(zrdn_grid_settings.gapVertical),
            gapHorizontal: parseInt(zrdn_grid_settings.gapHorizontal),
            gridAdjustment: 'responsive',
            caption: 'zoom',//'soverlayBottomAlong',
            displayType: 'sequentially',
            displayTypeSpeed: 50,
        });
        var loadMoreDiv = $('.zrdn-loadmore-items');
        var currentLoadMore = loadMoreDiv.html();
        if (currentLoadMore<=0) {
            // $('.cbp-l-loadMore-noMoreLoading').show();
            if ($('.cbp-l-loadMore-defaultText').length) $('.cbp-l-loadMore-defaultText').hide();
        }
        zrdnRecalculateFilterCounts();

    }

    /**
     *
     */

    function zrdnRecalculateFilterCounts(){
        if(window.use_ajax && typeof zrdn_grid_settings.category_counts !== 'undefined'){
            var cats = zrdn_grid_settings.category_counts;
            for (var key in cats) {
                if (cats.hasOwnProperty(key)) {
                    $("[data-filter='"+key+"'] .cbp-filter-counter").html(cats[key]);
                }
            }
        }
    }


    var zrdnRecipeIndexData = {};
    zrdnRecipeIndexData["search"] = '';
    zrdnRecipeIndexData["page"] = 0;
    zrdnRecipeIndexData["category"] = zrdn_grid_settings.category;
    var zrdnAjaxActive = false;

    //load more
    $(document).on('click', '.zrdn-recipe-grid-loadmore', function(e){
        e.preventDefault();
        zrdnRecipeIndexData["page"] = zrdnRecipeIndexData["page"]+1;
        zrdn_get_grid();
    });

    if ($('#zrdn-search-grid').length) {
        $('#zrdn-search-grid').donetyping(function (callback) {
            //your code goes here.
            zrdnRecipeIndexData["search"] = $('#zrdn-search-grid').val();
            zrdnRecipeIndexData["page"] = 0;
            zrdn_get_grid();
        });
    }
    if (window.use_ajax && $('.cbp-filter-item').length) {

        $(document).on('click', '.cbp-filter-item', function(e){
            e.preventDefault();
            zrdnRecipeIndexData["search"] = $('#zrdn-search-grid').val();
            zrdnRecipeIndexData["category"] = $(this).data('filter');
            zrdnRecipeIndexData["page"] = 0;

            zrdn_get_grid();

            var loadMoreDiv = $('.zrdn-loadmore-items');
            var currentLoadMore = zrdn_grid_settings.category_counts[zrdnRecipeIndexData["category"]];
            loadMoreDiv.html(currentLoadMore);
        });
    }

    function zrdn_get_grid() {
        if (zrdnAjaxActive) return;
        $('#zrdn-search-grid').blur();
        $('#zrdn-loading').show();

        zrdnAjaxActive = true;
        $.ajax({
            type: "GET",
            url: zrdn_grid_settings.url,
            dataType: 'json',
            data: ({
                action: 'zrdn_grid_load_more',
                category: zrdnRecipeIndexData["category"],
                search: zrdnRecipeIndexData["search"],
                showTitle: zrdn_grid_settings.showTitle,
                layoutMode: zrdn_grid_settings.layoutMode,
                recipesPerPage: zrdn_grid_settings.recipesPerPage,
                page: zrdnRecipeIndexData["page"],
            }),
            success: function (response) {
                var loadMoreDiv = $('.zrdn-loadmore-items');
                var currentLoadMore = loadMoreDiv.html();
                currentLoadMore -= response.count;

                loadMoreDiv.html(currentLoadMore);

                if (currentLoadMore<=0) {
                    //$('.cbp-l-loadMore-noMoreLoading').show();
                    if ($('.cbp-l-loadMore-defaultText').length) $('.cbp-l-loadMore-defaultText').hide();
                }

                if (zrdnRecipeIndexData["page"] === 0) {
                    zrdnGrid.cubeportfolio('remove', $('.cbp-item'), function(){
                        zrdnGrid.cubeportfolio('append', response.html)
                    });
                } else {
                    zrdnGrid.cubeportfolio('append', response.html);
                }

                $('#zrdn-loading').hide();

                //set focus back to input when user was typing
                if (zrdnRecipeIndexData["search"].length) {
                    $('#zrdn-search-grid').focus();
                }
                zrdnAjaxActive = false;
                $('#zrdn-search-grid').attr('disabled',false);
                zrdnRecalculateFilterCounts();
            }
        });

    }

});

