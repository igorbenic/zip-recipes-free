<script type="application/ld+json">
    {jsonld}
</script>
<style>
    .zrdn-grid-container .cbp-l-filters-button .cbp-filter-item.cbp-filter-item-active {
        background-color: {backgroundColor};
        color: {color};
        border-color: {borderColor};
    }
</style>
<div class="zrdn-grid-container">

    <div class="clearfix">
        <div id="zrdn-recipe-grid-filters" class="cbp-l-filters-button cbp-l-filters-left">
            {categories}
        </div>

        {search}
        <div class="cbp-search cbp-l-filters-right">
            <input id="zrdn-search-grid" type="text" placeholder="<?php _e("type to search","zip-recipes")?>" autocomplete="off"
                    class="cbp-search-input">
            <div class="cbp-search-icon"></div>
        </div>
        {/search}
    </div>

    <div id="zrdn-recipe-grid" class="cbp">
        {grid-items}
    </div>
    <div id="zrdn-loading" class="cbp"></div>
    {loadmoreButton}
    <div id="zrdn-loadmore" class="cbp-l-loadMore-button">
        <a href="#" class="cbp-l-loadMore-link zrdn-recipe-grid-loadmore">
            <span class="cbp-l-loadMore-defaultText"><?php _e('LOAD MORE','zip-recipes')?> (<span class="zrdn-loadmore-items">{loadmore-items}</span>)</span>
            <span class="cbp-l-loadMore-noMoreLoading"><?php _e('All recipes loaded','zip-recipes')?></span>
        </a>
    </div>
    {/loadmoreButton}

</div>