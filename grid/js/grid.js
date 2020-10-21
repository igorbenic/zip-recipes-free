jQuery(document).ready(function($) {
    $(document).on('click', '#zrdn-reset-layout', function(){
        localStorage.removeItem('zrdnDashboardDefaultsSet');
        window.localStorage.removeItem('zrdn_layout');
        window.localStorage.removeItem('zrdn_toggle_data_id_undefined');
        $('.zrdn-item').each(function() {
            var toggle_id = $(this).data('id');
            localStorage.setItem('zrdn_toggle_data_id_' + toggle_id, 'checked');
        });
        localStorage.removeItem("zrdnFormValues");
        location.reload();
    });

    var grid;
    initGrid();
    function initGrid() {
        // $('#zip-recipes .tab-content.current').show();
       grid = new Muuri('.zrdn-grid', {
            dragEnabled: true,
            dragStartPredicate: function(item, e) {
                return e.target.className === 'zrdn-grid-title';
            },
            dragSortHeuristics: {
                sortInterval: 50,
                minDragDistance: 10,
                minBounceBackAngle: 1
            },
            dragPlaceholder: {
                enabled: false,
                duration: 400,
                createElement: function (item) {
                    return item.getElement().cloneNode(true);
                }
            },
            dragReleaseDuration: 400,
            dragReleaseEasing: 'ease',
            layoutOnInit: true,
        })
        .on('move', function () {
            saveLayout(grid);
        });

        var layout = window.localStorage.getItem('zrdn_layout');
        if (layout) {
            loadLayout(grid, layout);
        } else {
            grid.layout(true);
        }
        // Must save the layout on first load, otherwise filtering the grid won't work on a new install.
        saveLayout(grid);



    }

    function serializeLayout(grid) {
        var itemIds = grid.getItems().map(function (item) {
            return item.getElement().getAttribute('data-id');
        });
        return JSON.stringify(itemIds);
    }

    function saveLayout(grid) {
        var layout = serializeLayout(grid);
        window.localStorage.setItem('zrdn_layout', layout);
    }

    function loadLayout(grid, serializedLayout) {
        var layout = JSON.parse(serializedLayout);

        var currentItems = grid.getItems();
        // // Add or remove the muuri-active class for each checkbox. Class is used in filtering.
        $('.zrdn-item').each(function(){
            var toggle_id = $(this).data('id');
            if ( typeof toggle_id === 'undefined' ) return;

            //this line is important, as it handles changing number of blocks.
            //if the layout has less blocks then there actually are, we add it here. Otherwise it ends up floating over another block
            if (!layout.includes( toggle_id.toString() ) ) layout.push( toggle_id.toString() );

            if (localStorage.getItem("zrdn_toggle_data_id_"+toggle_id) === null) {
                localStorage.setItem('zrdn_toggle_data_id_'+toggle_id, 'checked');
            }
            //Add or remove the active class when the checkbox is checked/unchecked
            if (localStorage.getItem('zrdn_toggle_data_id_'+toggle_id) !== 'checked') {
                $(this).removeClass("muuri-active");
            } else {
                $(this).addClass("muuri-active");
            }
        });

        var currentItemIds = currentItems.map(function (item) {
            return item.getElement().getAttribute('data-id')
        });
        var newItems = [];
        var itemId;
        var itemIndex;
        for (var i = 0; i < layout.length; i++) {
            itemId = layout[i];
            itemIndex = currentItemIds.indexOf(itemId);
            if (itemIndex > -1) {
                newItems.push(currentItems[itemIndex])
            }
        }

        try {
            // Sort and filter the grid
            grid.sort(newItems, {layout: 'instant'});
            grid.filter('.muuri-active');
        }
        catch(err) {
            console.log('error with grid, clear');
            localStorage.removeItem('zrdn_layout');
            localStorage.removeItem('layout');
        }
    }


    // Reload the grid when checkbox value changes
    $('.zrdn-item').each(function(){
        var toggle_id = $(this).data('id');
        // Set defaults for localstorage checkboxes
        if (!localStorage.getItem('zrdn_toggle_data_id_'+toggle_id)) {
            localStorage.setItem('zrdn_toggle_data_id_'+toggle_id, 'checked');
        }

        $('#zrdn_toggle_data_id_'+toggle_id).change(function() {
            if (document.getElementById("zrdn_toggle_data_id_"+toggle_id).checked ) {
                localStorage.setItem('zrdn_toggle_data_id_'+toggle_id, 'checked');
            } else {
               localStorage.setItem('zrdn_toggle_data_id_'+toggle_id, 'unchecked');
            }

            var layout = window.localStorage.getItem('zrdn_layout');
            if (layout) {
                loadLayout(grid, layout);
            } else {
                grid.layout(true);
            }

        });
    });

    /**
     * Show/hide dashboard items
     */

    //Get the window hash for redirect to #settings after settings save
    var tab = window.location.hash.substr(1).replace('#top','');
    $('ul.tabs li').click(function () {
        var tab_id = $(this).attr('data-tab');
        if (tab_id ==='dashboard'){
            $('#zrdn-show-toggles').show();
        } else{
            $('#zrdn-show-toggles').hide();
        }
        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
    });

    var href = $('.tab-'+tab).attr('href');
    if (typeof href !== 'undefined'){
        if (href.indexOf('#'+tab) !== -1 ) {
            $('.tab-'+tab)[0].click();
            window.location.href = href; //causes the browser to refresh and load the requested url
        }
    }

    /**
     * Checkboxes
     */

    // Get grid toggle checkbox values
    var zrdnFormValues = JSON.parse(localStorage.getItem('zrdnFormValues')) || {};
    var checkboxes = $("#zrdn-toggle-dashboard :checkbox");

    // Enable all checkboxes by default to show all grid items. Set localstorage val when set so it only runs once.
    if (localStorage.getItem("zrdnDashboardDefaultsSet") === null) {
        checkboxes.each(function () {
            zrdnFormValues[this.id] = 'checked';
            $("#" + this.id).prop('checked', 'checked' );
        });
        localStorage.setItem("zrdnFormValues", JSON.stringify(zrdnFormValues));
        localStorage.setItem('zrdnDashboardDefaultsSet', 'set');
    }

    // Update storage checkbox value when checkbox value changes
    checkboxes.on("change", function(){
        updateStorage();
    });

    // Get checkbox values on pageload
    $.each(zrdnFormValues, function(key, value) {
        $("#" + key).prop('checked', value);
    });
    //make sure not all checkboxes are empty
    var zrdnHasOneChecked = false;
    checkboxes.each(function () {
        if ( $(this).is(":checked") ) {
            zrdnHasOneChecked = true;
        }
    });

    if (!zrdnHasOneChecked){
        console.log("not one checked, reset");
        $('#zrdn-reset-layout').click();
    }

    // Hide screen options by default
    $("#zrdn-toggle-dashboard").hide();

    // Show/hide screen options on toggle click
    $('#zrdn-show-toggles').click(function(){
        if ($("#zrdn-toggle-dashboard").is(":visible") ){
            $("#zrdn-toggle-dashboard").slideUp();
            $("#zrdn-toggle-arrows").attr('class', 'dashicons dashicons-arrow-down-alt2');
        } else {
            $("#zrdn-toggle-dashboard").slideDown();
            $("#zrdn-toggle-arrows").attr('class', 'dashicons dashicons-arrow-up-alt2');
        }
    });

    function updateStorage(){
        checkboxes.each(function(){
            zrdnFormValues[this.id] = this.checked;
        });
        localStorage.setItem("zrdnFormValues", JSON.stringify(zrdnFormValues));
    }
});
