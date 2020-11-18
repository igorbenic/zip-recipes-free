jQuery(document).ready(function($) {
    var grids = [];
    var gridsIndex = [];
    var cloneMap = {};

    var inActiveGrid = new Muuri('.zrdn-grid-inactive', {
        dragEnabled: true,
        dragContainer: document.body,
        dragPlaceholder: {
            enabled: true,
            createElement: (item) => item.getElement().cloneNode(true),
        },
        dragSort: function () {
            return grids;
        },
        dragStartPredicate: function (item, event) {
            if (!event.isFinal && $(event.target).hasClass('zrdn-icon')) return false;
            if (!event.isFinal && $(event.target).hasClass('zrdn-premium')) return false;

            return Muuri.ItemDrag.defaultStartPredicate(item, event);
        }
    })
    .on('layoutEnd', function () {
        syncContainerHeight();
    })
    .on('dragReleaseEnd', function (item, event) {
        hideSettingsBlock($(item._element).find('.zrdn-recipe-block'));
        removeSingleUseBlocks();
        cssForEmptyGrids();
        setSettingsChanged($(item._element));
        inActiveGrid.refreshItems();
        inActiveGrid.layout();

    });
    grids.push(inActiveGrid);

    $(".zrdn-grid-active-element").each(function(){
        var gridIndex = $(this).data('grid_index');
        activateGrid(gridIndex);
    });

    function zrdnRefreshAllGrids(){
        for (var key in grids) {
            if (grids.hasOwnProperty(key)) {
                var grid = grids[key];
                grid.refreshItems();
                grid.layout();
            }
        }
    }



    $(document).on( 'click', '.zrdn-block-icon .zrdn-remove-block', function(){
        var block = $(this).closest('.zrdn-block');
        //if it's a 50 block, remove two
        var curBlockWidth = 100;
        if (block.hasClass('zrdn-block-50')) curBlockWidth = 50;
        if (curBlockWidth !== 100 ){
            var prevBlock = block.prev();
            showBlocksFromColumn(prevBlock);
            prevBlock.remove();
        }
        showBlocksFromColumn(block);
        block.remove();
    });

    $(document).on( 'mouseenter', '.zrdn-block-icon .zrdn-icon', function() {
        var block = $(this).closest('.zrdn-block');
        var newClass = 'zrdn-highlight-grid';
        if ($(this).hasClass('zrdn-remove-block')) newClass = 'zrdn-highlight-grid-warn';
        var curBlockWidth = 100;
        if (block.hasClass('zrdn-block-50')) curBlockWidth = 50;
        block.find('.zrdn-sub-grid .sub-item-content .zrdn-recipe-block').addClass(newClass);
        if (curBlockWidth === 50) {
            block.prev().find('.zrdn-sub-grid .sub-item-content .zrdn-recipe-block').addClass(newClass);
        }
    });
    $(document).on( 'mouseleave', '.zrdn-block-icon .zrdn-icon', function() {
        var block = $(this).closest('.zrdn-block');
        var curBlockWidth = 100;
        var newClass = 'zrdn-highlight-grid';
        if ($(this).hasClass('zrdn-remove-block')) newClass = 'zrdn-highlight-grid-warn';
        if (block.hasClass('zrdn-block-50')) curBlockWidth = 50;
        block.find('.zrdn-sub-grid .sub-item-content .zrdn-recipe-block').removeClass(newClass);
        if (curBlockWidth === 50) {
            block.prev().find('.zrdn-sub-grid .sub-item-content .zrdn-recipe-block').removeClass(newClass);
        }
    });

    $(document).on( 'click', '.zrdn-block-icon .zrdn-move', function() {
        var block = $(this).closest('.zrdn-block');
        var direction = $(this).data('direction');
        var curBlockWidth = 100;
        var prevBlockWidth = 100;
        var nextBlockWidth = 100;

        if (block.hasClass('zrdn-block-50')) {
            curBlockWidth = 50;
        }

        if ( curBlockWidth === 50 ) {
            if ( block.prev().prev().hasClass('zrdn-block-50') ) {
                prevBlockWidth = 50;
            }
            if ( block.next().hasClass('zrdn-block-50') ) {
                nextBlockWidth = 50;
            }
        } else {
            if ( block.prev().hasClass('zrdn-block-50') ) {
                prevBlockWidth = 50;
            }
            if ( block.next().hasClass('zrdn-block-50') ) {
                nextBlockWidth = 50;
            }
        }

        /**
         * #1 block is 100 width, previous also: just move one up
         * #2 block is 50 width. previous 100. move this block, and next one, one place up.
         * #3 block is 100 width, previous 50. move this block two places up.
         */


        if (direction === 'up') {
            if (curBlockWidth === 50 && prevBlockWidth === 100 ) {
                //move this block, and previous one, one place up.
                var prevBlock = block.prev();
                var anchor = block.prev().prev(); //100 block
                prevBlock.insertBefore(anchor);
                block.insertBefore(anchor);
            } else if (curBlockWidth === 100 && prevBlockWidth === 50 ) {
                //move this block two places up.
                var anchor = block.prev().prev(); //50
                block.insertBefore(anchor);
            } else if (curBlockWidth=== 50 && nextBlockWidth === 50 ){
            //move this block, and the previous one, two places up)
                var prevBlock = block.prev();
                var anchor = block.prev().prev().prev();
                prevBlock.insertBefore(anchor);
                block.insertBefore(anchor);
            } else if (curBlockWidth=== 100 && prevBlockWidth === 100 ){
                var prevItem = block.prev();
                block.insertBefore(prevItem);
            }
        } else {
            if (curBlockWidth === 50 && nextBlockWidth === 100 ) {
                //move this block, and previous one, one place down.
                var prevBlock = block.prev();
                var anchor = block.next(); //100 block
                block.insertAfter(anchor);
                prevBlock.insertAfter(anchor);
            }else if (curBlockWidth === 100 && nextBlockWidth === 50 ) {
                //move this block two places down.
                var anchor = block.next().next(); //50
                block.insertAfter(anchor);
            } else if (curBlockWidth=== 50 && nextBlockWidth === 50 ){
                //move this block, and the previous one, two places down
                var prevBlock = block.prev();
                var anchor = block.next().next(); //50 block
                block.insertAfter(anchor);
                prevBlock.insertAfter(anchor);
            } else if (curBlockWidth=== 100 && nextBlockWidth === 100 ){
                var nextItem = block.next();
                block.insertAfter(nextItem);
            }
        }

        showHideIcons();
    });


    /**
    * Insert column
    * */

    $(document).on( 'click', '.zrdn-grid-controls .zrdn-add-block', function(){
        var isEmpty = false;
        var newIndex1 = getHighestIndex()+1;
        var newIndex2 = newIndex1+1;
        var newBlockWidth = $(this).data('width');
        var block = $('.zrdn-active-container .zrdn-block').first();

        //if the template is empty, only invisible blocks are present
        if (block.hasClass('zrdn-block-0')) {
            isEmpty = true;
        }
        var curIndex = block.find('.zrdn-sub-grid').data('grid_index');
        var iconTemplate1 = block.find('.zrdn-block-icon').clone();
        var iconTemplate2 = iconTemplate1.clone();

        /**
         * Copy the grid from this block
         */

        var newGridLeft = block.find('.zrdn-sub-grid').clone();
        newGridLeft.removeClass('zrdn-grid-'+ curIndex);
        newGridLeft.html('');
        var newGridRight = newGridLeft.clone();

        /**
         * Give it the correct index
         */

        newGridLeft.addClass('zrdn-grid-'+newIndex1);
        newGridLeft.data('grid_index', newIndex1);
        newGridLeft.attr('data-grid_index', newIndex1);

        newGridRight.addClass('zrdn-grid-'+newIndex2);
        newGridRight.data('grid_index', newIndex2);
        newGridRight.attr('data-grid_index', newIndex2);

        var newLeftColumn = block.clone();
        var newRightColumn = newLeftColumn.clone();

        newLeftColumn.html(newGridLeft);
        newLeftColumn.prepend(iconTemplate1);

        newRightColumn.html( newGridRight );
        newRightColumn.prepend( iconTemplate2 );

        //if the current block = 100, prepend
        //if the current block = 50, check if it's odd or even, and insert before the

        if ( newBlockWidth === 50 ) {
            newLeftColumn.removeClass('zrdn-block-100').removeClass('zrdn-block-0').addClass('zrdn-block-50');
            newRightColumn.removeClass('zrdn-block-100').removeClass('zrdn-block-0').addClass('zrdn-block-50');

            newLeftColumn.insertBefore(block);
            newRightColumn.insertBefore(block);

            activateGrid(newIndex1);
            activateGrid(newIndex2);
        } else {
            newLeftColumn.removeClass('zrdn-block-50').removeClass('zrdn-block-0').addClass('zrdn-block-100');
            newLeftColumn.insertBefore(block);

            activateGrid(newIndex1);
        }
        zrdnRefreshAllGrids();
        syncContainerHeight();
        cssForEmptyGrids();

    });

    function getHighestIndex(){

        var num = $(".zrdn-grid-active-element").map(function() {
            return $(this).data('grid_index');
        }).get();//get all data values in an array

        return Math.max.apply(Math, num);//find the highest value from them
    }

    /**
     * Activate grid
     * @param gridIndex
     */

    function activateGrid(gridIndex){
        var ActiveGridElement = new Muuri('.zrdn-grid-'+gridIndex, {
            dragEnabled: true,
            dragContainer: document.body,
            dragPlaceholder: {
                enabled: true,
                createElement: (item) => item.getElement().cloneNode(true),
            },
            dragSort: function () {
                return grids;
            },
            dragStartPredicate: function (item, event) {
                if (!event.isFinal && $(event.target).hasClass('zrdn-field-input')) return false;
                if (!event.isFinal && $(event.target).hasClass('zrdn-icon')) return false;
                if (!event.isFinal && $(event.target).hasClass('zrdn-slider')) return false;
                if (!event.isFinal && $(event.target).hasClass('zrdn-tooltip-icon')) return false;

                if (!event.isFinal && $(event.target).prop("tagName") === 'SELECT' ) return false;
                return Muuri.ItemDrag.defaultStartPredicate(item, event);
            }

        })
        .on('layoutEnd', function () {
            syncContainerHeight();
        })
        //clone multiple use elements back to source
            //but not if the source is "activeGrid"
        .on('receive', function (data) {
            var el = data.item._element;
            var source = data.fromGrid._element;
            if ( !$(el).find('[data-single]').data('single') && !$(source).hasClass('zrdn-grid-active-element')) {
                cloneMap[data.item._id] = {
                    item: data.item,
                    grid: data.fromGrid,
                    index: data.fromIndex
                };
            }

        })
        .on('dragReleaseStart', function (item) {
            var cloneData = cloneMap[item._id];
            if (cloneData) {
                delete cloneMap[item._id];
                var clone = cloneData.item.getElement().cloneNode(true);
                cloneData.grid.add(clone, {index: cloneData.index});
                cloneData.grid.show(clone);
            }
        })
        //template grid needs to be rendered again, because css changes, causing height changes.
        .on('dragReleaseEnd', function (item, event) {
            hideSettingsBlock($(item._element).find('.zrdn-recipe-block'));
            removeSingleUseBlocks();
            syncContainerHeight();
            showHideIcons();
            cssForEmptyGrids();
            setSettingsChanged($(item._element));
            ActiveGridElement.refreshItems();
            ActiveGridElement.layout();
        });

        grids.push(ActiveGridElement);
        syncContainerHeight();
        showHideIcons();
        cssForEmptyGrids();

        gridsIndex[gridIndex] = ActiveGridElement;
    }

    /**
    * Some blocks should only be used once. These will get removed from the inactive list once added.
    *
    * */
    removeSingleUseBlocks();
    function removeSingleUseBlocks(){
        //for each active block
        // if it's a "single use" block, remove it from the inactive list
        $("#zrdn-recipe-container [data-blocktype]").each(function(){
            if ($(this).data('single')){
                if (!$(this).closest('.zrdn-grid-item').hasClass('muuri-item-placeholder')){
                    $(".zrdn-grid-inactive ."+$(this).data('blocktype')).hide();
                }
            }
        });

        var detectedBlocktypes = [];
        //if we have duplicate blocks in the inactive list, remove the duplicate
        $(".zrdn-grid-inactive .zrdn-grid-item").each(function(){
            var blocktype = $(this).find('[data-blocktype]').data('blocktype');
            var single = $(this).find('[data-single]').data('single');
            var foundInArray = false;
            if ($(this).is(":visible") && !single && !$(this).closest('.zrdn-grid-item').hasClass('muuri-item-placeholder')) {
                for (var key in detectedBlocktypes) {
                    if (detectedBlocktypes.hasOwnProperty(key)) {
                        if (detectedBlocktypes[key] === blocktype) {
                            foundInArray = true;
                        }
                    }
                }

                if (foundInArray) {
                    $(this).remove();
                } else {
                    detectedBlocktypes.push(blocktype);
                }
            }
        });
        inActiveGrid.refreshItems();
        inActiveGrid.layout();
    }

    function showHideIcons(){
        $("#zrdn-recipe-container .zrdn-block").each(function(){
            var blockType = $(this);

            var blockIndex = blockType.index('.zrdn-block-50')+1;
            if (blockIndex % 2 !== 0) {
                //odd, hide this icon
                blockType.find('.zrdn-block-icon').hide();
            } else {
                blockType.find('.zrdn-block-icon').show();
            }

        });
    }

    function cssForEmptyGrids(){
        $("#zrdn-recipe-container .zrdn-block").each(function(){
            if (!$(this).find('.zrdn-sub-grid .zrdn-grid-item').length) {
                $(this).find('.zrdn-sub-grid').addClass('zrdn-highlight-dragarea');
            } else {
                $(this).find('.zrdn-sub-grid').removeClass('zrdn-highlight-dragarea');
            }

        });
    }

    function setSettingsChanged(obj){
        var parentFieldGroup = obj.closest('.field-group');
        if (parentFieldGroup.length) {
            if (parentFieldGroup.data('reload_on_change') == 1) {
                zrdnSettingsChanged = true;
            }
        }
        var controls = $('.zrdn-active-container .zrdn-grid-controls');
        if (!controls.find('.zrdn-settings-changed').length) {
            controls.prepend('<div class="zrdn-settings-changed">' + zrdn.strings['settings_changed'] + '</div>');
        }
    }

    function resetSettingsChanged(){
        $('.zrdn-active-container .zrdn-grid-controls .zrdn-settings-changed').remove('');
    }

    /**
     * Keep track if template settings were changed
     */
    var zrdnSettingsChanged = false;
    $(document).on("change", "#zrdn-save-template-settings select", function () {
        zrdnSettingsChanged = true;
        zrdnUpdateStyle($(this));
    });
    $(document).on("keyup", "#zrdn-save-template-settings input", function () {
        zrdnSettingsChanged = true;
        zrdnUpdateStyle($(this));
    });
    $(document).on("change", "#zrdn-save-template-settings input", function () {
        //don't track colorpicker here
        if ($(this).hasClass('wp-color-picker')) return;
        zrdnSettingsChanged = true;
        zrdnUpdateStyle($(this));
    });
    $(document).on("change", "#zrdn-save-template-settings input[type=checkbox]", function () {
        zrdnSettingsChanged = true;
        zrdnUpdateStyle($(this));
    });

    /**
     * Keep track of drop down settings changes in template
     */
    $(document).on("change", "#zrdn-recipe-container select", function () {
        setSettingsChanged($(this));
        var listType = $(this).val();
        var listClass = zrdnGetListClass(listType);
        var listElement = zrdnGetListElement(listType);

        var recipeBlock = $(this).closest('.zrdn-recipe-block');
        if (recipeBlock.find('.zrdn-block-wrap').data('blocktype') === 'ingredients' ) {
            listClass += ' zrdn-ingredients-list';
        } else {
            listClass += ' zrdn-ingredients-list';
        }
        listClass += ' zrdn-list';

        var listContainer = recipeBlock.find('.zrdn-list');
        var curTag = listContainer.prop("tagName");

        if (curTag !== listElement ) {
            listContainer.replaceWith($('<'+ listElement +' class="zrdn-list">' + listContainer.html() + '</'+listElement+'>'));
        }

        listContainer = recipeBlock.find('.zrdn-list');
        listContainer.removeClass().addClass(listClass);
    });



    /**
     * reset settings
     */

    $(document).on('click', ".zrdn-reset-template-settings", function(){
        $('input[name=zrdn-reset-template]').val(true);
        $('#zrdn-save-template-settings').submit();
    });

    var saveBtn = $('.zrdn-button-container').clone();
    saveBtn.find('button').addClass('zrdn-template-only-save');
    $('.zrdn-active-container .item-container #zrdn-recipe-container').append(saveBtn);

    /**
     * Save settings
     */
    $(document).on("click",  ".zrdn-save-template-settings", function(){
        var btn = $(".zrdn-save-template-settings");
        btn.prop('disabled', true);
        var btnHtml = btn.html();
        btn.html('<div class="zrdn-loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');

        var templateStructure = [];
        //get template structure in array
        $("#zrdn-recipe-container .zrdn-block").each(function(){
            var recipeBlock = new Object();
            recipeBlock.settings = [];
            if ($(this).hasClass('zrdn-block-50')){
                recipeBlock.type = "block-50";
            } else if($(this).hasClass('zrdn-block-100'))  {
                recipeBlock.type = "block-100";
            } else {
                return;//hidden block, continue to next loop
            }
            var gridActiveElement = $(this).find(".zrdn-grid-active-element");
            var gridIndex = gridActiveElement.data('grid_index');
            var grid = getGridByIndex(gridIndex);
            recipeBlock.blocks = getBlockTypesFromGrid(grid);

            //get settings
            var settingsElement = gridActiveElement.find('.zrdn-recipe-block-settings');
            settingsElement.each(function(){
                var type = $(this).closest('.zrdn-recipe-block').find('[data-blocktype]').data('blocktype');
                var blockIndex;
                for (var key in recipeBlock.blocks) {
                    if (recipeBlock.blocks.hasOwnProperty(key)) {
                        if (recipeBlock.blocks[key].type===type){
                            blockIndex = key;
                        }
                    }
                }
                $(this).find('select').each(function(){
                    var fieldValue = $(this).val();
                    var fieldName = $(this).prop('name');
                    var recipeSetting = new Object();
                    recipeSetting.value = fieldValue;
                    recipeSetting.name = fieldName;
                    recipeBlock.blocks[blockIndex].settings.push(recipeSetting);
                });

                $(this).find('input[type=checkbox]').each(function(){
                    var fieldValue = $(this).is(':checked');
                    var fieldName = $(this).prop('name');
                    var recipeSetting = new Object();
                    recipeSetting.value = fieldValue;
                    recipeSetting.name = fieldName;
                    recipeBlock.blocks[blockIndex].settings.push(recipeSetting);
                });

                $(this).find('input[type=text]').each(function(){
                    var fieldValue = $(this).val();
                    var fieldName = $(this).prop('name');
                    var recipeSetting = new Object();
                    recipeSetting.value = fieldValue;
                    recipeSetting.name = fieldName;
                    recipeBlock.blocks[blockIndex].settings.push(recipeSetting);
                });
            });

            //get grid
            templateStructure.push(recipeBlock);

        });
        if (!templateStructure.length){
            templateStructure = false;
        }

        if ($(this).hasClass('zrdn-template-only-save')) {
            zrdnSettingsChanged = false;
        }

        /**
         * handle banner hide and show
         */

        $.ajax({
            type: "POST",
            url: zrdn.admin_url,
            dataType: 'json',
            data: ({
                nonce: zrdn.nonce,
                template_structure: templateStructure,
                action: 'zrdn_save_template'
            }),
            success: function (response) {

                btn.prop('disabled', false);
                btn.html(btnHtml);
                resetSettingsChanged();

                if (zrdnSettingsChanged){
                    $('#zrdn-save-template-settings').submit();
                }
            }
        });

    });

    function getGridByIndex(index){
        if (gridsIndex.hasOwnProperty(index)) {
            return gridsIndex[index];
        }
        return false;
    }

    $(document).on('click', '.zrdn-recipe-block-settings input[type=checkbox]', function(){
        if ($(this).is(":checked")) {
            $(this).closest('.zrdn-recipe-block').find('.zrdn-block-wrap>.zrdn-recipe-label').hide();
        } else {
            $(this).closest('.zrdn-recipe-block').find('.zrdn-block-wrap>.zrdn-recipe-label').show();
        }
    });

    $(document).on('click', '.zrdn-recipe-block-settings input[name=zrdn_hide_nutrition_text_expl]', function(){
        if ($(this).is(":checked")) {
            $(this).closest('.zrdn-recipe-block').find('.zrdn-block-wrap>.zrdn-text-nutrition-explanation').hide();
        } else {
            $(this).closest('.zrdn-recipe-block').find('.zrdn-block-wrap>.zrdn-text-nutrition-explanation').show();
        }
    });

    function getBlockTypesFromGrid(grid){
        var elements = grid.getItems();
        var blocks = [];
        for (var key in elements) {
            if (elements.hasOwnProperty(key)) {
                var el = elements[key]._element;
                var blockType = $(el).find('[data-blocktype]').data('blocktype');

                var recipeBlockType = new Object();
                recipeBlockType.type = blockType;
                recipeBlockType.settings = [];
                blocks.push(recipeBlockType);
            }
        }
        return blocks;
    }

    function showBlocksFromColumn(block){
        var gridIndex = block.find('.zrdn-grid-active-element').data('grid_index');
        var grid = getGridByIndex(gridIndex);
        var blocks = getBlockTypesFromGrid(grid);
        for (var key in blocks) {
            if (blocks.hasOwnProperty(key)) {
                var blockElement = $('.zrdn-grid-item.'+blocks[key].type);
                blockElement.show();
            }
        }
        zrdnRefreshAllGrids();
    }

    /**
     * Keep container heigh up to date, based on contents
     */

    function syncContainerHeight(){
        //calculate height of grid
        var height = 0;
        var margin = 70;
        var skipNext = false;
        $("#zrdn-recipe-container .zrdn-block").each(function(){
            //don't count hidden blocks
            if ($(this).hasClass('zrdn-block-0')) return;

            //check if this is a half block. if so, skip the next half block. Otherwise half blocks are counted double
            if (skipNext) {
                skipNext =  false;
                return;
            }
            if ($(this).hasClass('zrdn-block-50')) {
                skipNext = true;
            }
            height = height + parseInt($(this).height() ) + margin;
        });
        $(".zrdn-main-container .zrdn-grid .zrdn-active-container").height(height);
        $(".zrdn-main-container .zrdn-grid-inactive").height(height);
    }


    $(document).on('click', '.zrdn-block-settings', function(){
        var recipeBlock = $(this).closest('.zrdn-recipe-block');
        var settings= recipeBlock.find('.zrdn-recipe-block-settings');
        var gridElement = settings.closest('.zrdn-grid-item');

        if (settings.is(":visible")) {
            hideSettingsBlock(recipeBlock);
        } else {
            //first, close all open settings blocks.
            $('.zrdn-recipe-block-settings').each(function(){
                hideSettingsBlock($(this).closest('.zrdn-recipe-block'));
            });
            settings.slideDown();
            recipeBlock.addClass('settings-open');
            var currentHeight = gridElement.height();
            gridElement.data('height', currentHeight );
            gridElement.height(currentHeight + settings.data('settings_height'));
        }

        zrdnRefreshAllGrids();

    });

    function hideSettingsBlock(recipeBlock){
        var settings = recipeBlock.find('.zrdn-recipe-block-settings');
        var gridElement = settings.closest('.zrdn-grid-item');

        if (settings.is(":visible")) {
            settings.hide();
            recipeBlock.removeClass('settings-open');
            var previousHeight = gridElement.data('height');
            gridElement.height(previousHeight);
        }
    }

    //prevent drag on images
    var images = document.querySelectorAll('#zrdn-recipe-container .zrdn-recipe-block-content img');
    for (var i = 0; i < images.length; i++) {
        images[i].addEventListener('dragstart', function (e) {
            e.preventDefault();
        }, false);
    }

    $(document).on('change', 'input[name=color_picker_container]', function(){
        var container_id = $(this).data('hidden-input');
        zrdnSettingsChanged = true;
        $('#' + container_id).val( $(this).val() );
        zrdnUpdateStyle($('#' + container_id));
    });

    $('.zrdn-color-picker').wpColorPicker({
            change:
                function (event, ui) {
                    if (event.hasOwnProperty('originalEvent')) {
                        var container_id = $(event.target).data('hidden-input');
                        $('#' + container_id).val(ui.color.toString());
                        zrdnSettingsChanged = true;
                        zrdnUpdateStyle($('#' + container_id));
                    }
                }
        }
    );



    /**
     * Get list class for certain list style
     * @param list_style
     *
     * @return string
     */

    function zrdnGetListClass(list_style){

        if (list_style==='nobullets' || list_style === 'numbers' || list_style === 'bullets') return list_style;

        var listClass = '';
        if (list_style.indexOf('numbers') !==-1 ) {
            listClass += ' zrdn-numbered';
        }

        if (list_style.indexOf('border') !==-1 ) {
            listClass += ' zrdn-bordered';
        }
        if (list_style.indexOf('circle') !==-1 ) {
            listClass += ' zrdn-round';
        }
        if (list_style.indexOf('square') !==-1 ) {
            listClass += ' zrdn-square';
        }

        if (list_style.indexOf('solid') !==-1 ) {
            listClass += ' zrdn-solid';
        }

        if (list_style.indexOf('bullets') !==-1 ) {
            listClass += ' zrdn-bullets';
        }

        if (list_style.indexOf('counter') !==-1 ) {
            listClass += ' zrdn-counter';
        }

        return listClass;
    }

    function zrdnGetListElement(list_style){
        if (list_style.indexOf('numbers') !==-1 ) {
            return 'OL'
        }
        return 'UL';
    }

    function zrdnUpdateStyle(obj) {
        var fieldName = obj.prop('name').replace('zrdn_', '').replace('_', '-');
        var value = obj.val();

        if (fieldName.indexOf('border')!==-1) {
            if ($.isNumeric(value)) value += 'px';
            $("#zrdn-recipe-container").css(fieldName, value);
        }

        if (fieldName.indexOf('background')!==-1) {
            $("#zrdn-recipe-container").css('background-color', value);
        }

        if (fieldName.indexOf('text')!==-1) {
            $("#zrdn-recipe-container").css('color', value);
            $("#zrdn-recipe-container h2").css('color', value);
            $("#zrdn-recipe-container h3").css('color', value);
            $("#zrdn-recipe-container h4").css('color', value);
        }

        if (fieldName.indexOf('box-shadow')!==-1) {
            if (obj.is(':checked')) {
                $("#zrdn-recipe-container").css('box-shadow', '0 1px 1px rgba(0,0,0,0.12), 0 2px 2px rgba(0,0,0,0.12), 0 4px 4px rgba(0,0,0,0.12), 0 8px 8px rgba(0,0,0,0.12), 0 16px 16px rgba(0,0,0,0.12)' );

            } else {
                $("#zrdn-recipe-container").css('box-shadow','initial');
            }
        }
    }

    //set default hidden or shown based on screensite
    var userScreenWidth = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var tab = $('[data-tab=Settings]');

    // minimize wordpress menu below 1280
    if ( userScreenWidth < 1280 ) {
        if (!jQuery(document.body).hasClass('folded')) {
            jQuery(document.body).addClass('folded');
        }
    }
    
    var settingsColumn = $('.zrdn-item.grid-active[data-id=0]');
    if ( userScreenWidth < 1650 ) {
        settingsColumn.hide();
        tab.removeClass('current');

    }

    $(document).on('click', '.tab-Settings', function(){

        if (settingsColumn.is(":hidden")) {
            settingsColumn.fadeIn();
            tab.addClass('current');
        } else {
            settingsColumn.fadeOut();
            tab.removeClass('current');

        }

    });


});