/**
 *
 * Registering the Zip Recipes block with Gutenberg.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

import * as api from './utils/api';
//
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { SelectControl } = wp.components;
// const { Button } = wp.components;
const { CheckboxControl } = wp.components;
const { TextControl } = wp.components;
const { ColorPicker } = wp.components;
const { withState } = wp.compose;
const { Component } = wp.element;

// const {RichText} = wp.editor;
// const {AlignmentToolbar} = wp.editor;
// const {BlockControls} = wp.editor;

const el = wp.element.createElement;

/**
 *  Set custom Zip Recipes Icon
 */

    class selectGrid extends Component {
        // Method for setting the initial state.
        static getInitialState(attributes) {

            return {
                gridContent : [],
                categories: [],
                category : attributes.category, //needs api refresh
                showTitle : attributes.showTitle, //needs api refresh
                recipesPerPage : attributes.recipesPerPage, //needs api refresh
                loadMoreButton : attributes.loadMoreButton,
                useAjax : attributes.useAjax,
                search : attributes.search,
                layoutMode : attributes.layoutMode, //needs api refresh for image sizes
                animationType : attributes.animationType,
                gapHorizontal : attributes.gapHorizontal,
                gapVertical : attributes.gapVertical,
                size : attributes.size,
                backgroundColor : attributes.backgroundColor,
                color : attributes.color,
                borderColor : attributes.borderColor,
                preview: false,
            };
        }

        // Constructing our component. With super() we are setting everything to 'this'.
        // Now we can access the attributes with this.props.attributes
        constructor() {
            super(...arguments);
            // Maybe we have some settings already. try to load it.
            this.state = this.constructor.getInitialState(this.props.attributes);

            // Bind so we can use 'this' inside the method.
            this.getGrid(this.props.attributes);
            this.getGrid = this.getGrid.bind(this);
            this.setGrid = this.setGrid.bind(this);
            this.applyCss = this.applyCss.bind(this);

            // Bind so we can use 'this' inside the method.
            this.getCategories = this.getCategories.bind(this);
            this.getCategories();

            this.onChangeShowTitle = this.onChangeShowTitle.bind(this);

            this.onChangeSelectAnimationType = this.onChangeSelectAnimationType.bind(this);
            this.onChangeSelectLayoutMode = this.onChangeSelectLayoutMode.bind(this);
            this.onChangeSetGapHorizontal = this.onChangeSetGapHorizontal.bind(this);
            this.onChangeSetGapVertical = this.onChangeSetGapVertical.bind(this);
            this.onChangeSetRecipesPerPage = this.onChangeSetRecipesPerPage.bind(this);
            this.onChangeLoadMoreButton = this.onChangeLoadMoreButton.bind(this);
            this.onChangeUseAjax = this.onChangeUseAjax.bind(this);
            this.onChangeSetSearch = this.onChangeSetSearch.bind(this);
            this.onChangeSelectSize = this.onChangeSelectSize.bind(this);
            this.onChangeSetColor = this.onChangeSetColor.bind(this);
            this.onChangeSetBorderColor = this.onChangeSetBorderColor.bind(this);
            this.onChangeSetBackgroundColor = this.onChangeSetBackgroundColor.bind(this);
            this.onChangeSelectCategory = this.onChangeSelectCategory.bind(this);
        }

        setGrid(args = {}){
            let colsXL=5;
            switch (args.size){
                case 'large':
                    colsXL = 4;
                    break;
                case 'medium':
                    colsXL = 5;
                    break;
                case 'small':
                    colsXL = 6;
                    break;
                default:
                    colsXL = 5;
            }
            let colsL = colsXL>1 ? colsXL-1 : 1;
            let colsM = colsL>1 ? colsL-1 : 1;
            let colsS = colsM>1 ? colsM-1 : 1;

            jQuery('#zrdn-recipe-grid').cubeportfolio({
                filters: '#zrdn-recipe-grid-filters',
                mediaQueries: [{
                    width: 1500,
                    cols: colsXL,
                }, {
                    width: 1100,
                    cols: colsL,
                }, {
                    width: 800,
                    cols: colsM,
                }, {
                    width: 480,
                    cols: colsS,
                    options: {
                        // gapHorizontal: 30,
                        // gapVertical: 10,
                    }
                }],
                defaultFilter: '*',
                animationType: args.animationType,
                layoutMode: args.layoutMode,
                gapVertical: parseInt(args.gapVertical),
                gapHorizontal: parseInt(args.gapHorizontal),
                gridAdjustment: 'responsive',
                caption: 'zoom',//'overlayBottomAlong',
                displayType: 'sequentially',
                displayTypeSpeed: 50,
            });

            this.applyCss(args);

            if (args.showTitle){
                jQuery(".cbp-l-grid-agency-title").show();
                jQuery(".cbp-l-grid-agency-desc").show();
            } else {
                jQuery(".cbp-l-grid-agency-title").hide();
                jQuery(".cbp-l-grid-agency-desc").hide();
            }
            if (args.loadMoreButton){
                jQuery(".cbp-l-loadMore-defaultText").show();
            } else {
                jQuery(".cbp-l-loadMore-defaultText").hide();
            }

            window.use_ajax = args.useAjax;

            if (args.search){
                jQuery(".cbp-search").show();
            } else {
                jQuery(".cbp-search").hide();
            }

        }


        getGrid(args = {}){
            (
                api.getGrid(args).then( ( response ) => {
                    let gridContent = response.data;

                    if( gridContent ) {
                        this.setState( { gridContent } );
                        this.setGrid(args);
                    }
                })
            )
        }

        onChangeShowTitle(checked) {
            this.clearGrid();

            this.setState({showTitle: checked});
            //get new content
            //this.getGrid(this.props.attributes);
            this.props.setAttributes({
                showTitle: checked,
            });
            let args = this.props.attributes;
            args.showTitle = checked;
            this.setGrid(args);


        }

        onChangeLoadMoreButton(checked){

            this.setState({loadMoreButton: checked});
            //get new content
            //this.getGrid(this.props.attributes);
            this.props.setAttributes({
                loadMoreButton: checked,
            });
            let args = this.props.attributes;
            args.loadMoreButton = checked;
            if (args.loadMoreButton){
                jQuery(".cbp-l-loadMore-defaultText").show();
            } else {
                jQuery(".cbp-l-loadMore-defaultText").hide();
            }
        }

        onChangeUseAjax(checked){

            this.setState({useAjax: checked});
            //get new content
            //this.getGrid(this.props.attributes);
            this.props.setAttributes({
                useAjax: checked,
            });
            let args = this.props.attributes;
            args.useAjax = checked;
            window.use_ajax = args.useAjax;

        }

        onChangeSetSearch(checked){

            this.setState({search: checked});
            //get new content
            //this.getGrid(this.props.attributes);
            this.props.setAttributes({
                search: checked,
            });
            let args = this.props.attributes;
            args.search = checked;
            if (args.search){
                jQuery(".cbp-search").show();
            } else {
                jQuery(".cbp-search").hide();
            }
        }

        onChangeSelectAnimationType(value){
            this.clearGrid();

            this.setState({animationType: value});

            // Set the attributes
            this.props.setAttributes({
                animationType: value,
            });
            let args = this.props.attributes;
            args.animationType = value;
            this.setGrid(args);

        }


        onChangeSelectCategory(value){
            this.clearGrid();
            this.setState({category: value});

            // Set the attributes
            this.props.setAttributes({
                category: value,
            });
            let args = this.props.attributes;
            args.category = value;

            this.getGrid(args);
            this.setGrid(args);

        }

        getCategories(args = {}){
            (
                api.getCategories().then( ( response ) => {
                    let categories = response.data;

                    if( categories && 0 !== this.state.category ) {
                        // If we have a selected cat, find that cat and add it.
                        const category = categories.find( ( item ) => { return item.id === this.state.category } );

                        this.setState( { category, categories } );
                    } else {
                        //this.state.recipes = recipe;
                        this.setState({ categories });
                    }
                })
            )
        }

        clearGrid(){
            if (jQuery('#zrdn-recipe-grid').cubeportfolio){
                 jQuery('#zrdn-recipe-grid').cubeportfolio('destroy');
            }
        }


        onChangeSelectLayoutMode(value){
            // Set the state
            this.clearGrid();
            this.setState({layoutMode: value});

            // Set the attributes
            this.props.setAttributes({
                layoutMode: value,
            });
            let args = this.props.attributes;
            args.layoutMode = value;
            this.getGrid(args);
            this.setGrid(args);
        }

        onChangeSelectSize(value){
            // Set the state
            this.clearGrid();
            this.setState({size: value});

            // Set the attributes
            this.props.setAttributes({
                size: value,
            });
            let args = this.props.attributes;
            args.size = value;
            this.setGrid(args);
        }

        onChangeSetGapHorizontal(value){
            // Set the state
            this.clearGrid();
            value = parseInt(value);
            this.setState({gapHorizontal: value});

            // Set the attributes
            this.props.setAttributes({
                gapHorizontal: value,
            });
            let args = this.props.attributes;
            args.gapHorizontal = value;
            this.setGrid(args);

        }

        onChangeSetGapVertical(value){
            // Set the state
            this.clearGrid();
            value = parseInt(value);
            this.setState({gapHorizontal: value});

            // Set the attributes
            this.props.setAttributes({
                gapVertical: value,
            });
            let args = this.props.attributes;
            args.gapVertical = value;
            this.setGrid(args);
        }

        onChangeSetColor(value){
            let color = value.hex;
            this.setState({color: color});

            // Set the attributes
            this.props.setAttributes({
                color: color,
            });
            let args = this.props.attributes;
            args.color = value;
            this.applyCss(args);
        }

        applyCss(args){
            if (jQuery("#zrdn-grid-inline-css").length){
                jQuery("#zrdn-grid-inline-css").remove();
            }

            let css_str = '.zrdn-grid-container .cbp-l-filters-button .cbp-filter-item.cbp-filter-item-active {border-color:'+args.borderColor+'!important;background-color:'+args.backgroundColor+'!important;color:'+args.color+'!important}';
            jQuery('<style id="zrdn-grid-inline-css">')
                .prop("type", "text/css")
                .html(css_str).appendTo("head");
        }

        onChangeSetBackgroundColor(value){
            let color = value.hex;
            this.setState({backgroundColor: color});

            // Set the attributes
            this.props.setAttributes({
                backgroundColor: color,
            });
            let args = this.props.attributes;
            args.backgroundColor = value;
            this.applyCss(args);

        }

        onChangeSetBorderColor(value){
            let color = value.hex;
            this.setState({borderColor: color});

            // Set the attributes
            this.props.setAttributes({
                borderColor: color,
            });

            let args = this.props.attributes;
            args.borderColor = value;
            this.applyCss(args);

        }

        onChangeSetRecipesPerPage(value){
            // Set the state
            this.clearGrid();
            value = parseInt(value);
            this.setState({recipesPerPage: value});

            // Set the attributes
            this.props.setAttributes({
                recipesPerPage: value,
            });
            let args = this.props.attributes;
            args.recipesPerPage = value;
            this.getGrid(args);
            this.setGrid(args);
        }

        render() {
            const { className, attributes: {} = {} } = this.props;
            //build options
            let categoryOptions = [{value: 'all', label: __('All categories')}];
            if (this.state.categories.length > 0) {
                 this.state.categories.forEach((category) => {
                     categoryOptions.push({value: category.id, label: category.name});
                 });
            }
            let animationOptions = [
                {value: 0, label: __('Select animation type', 'zip-recipes')},
                {value: 'quicksand', label: __('Quicksand', 'zip-recipes')},
                {value: 'fadeOut', label: __('fadeOut', 'zip-recipes')},
                {value: 'bounceLeft', label: __('bounceLeft', 'zip-recipes')},
                {value: 'bounceTop', label: __('bounceTop', 'zip-recipes')},
                {value: 'bounceBottom', label: __('bounceBottom', 'zip-recipes')},
                {value: 'moveLeft', label: __('moveLeft', 'zip-recipes')},
                {value: 'slideLeft', label: __('slideLeft', 'zip-recipes')},
                {value: 'fadeOutTop', label: __('fadeOutTop', 'zip-recipes')},
                {value: 'sequentially', label: __('sequentially', 'zip-recipes')},
                {value: 'skew', label: __('skew', 'zip-recipes')},
                {value: 'slideDelay', label: __('slideDelay', 'zip-recipes')},
                {value: 'rotateSides', label: __('rotateSides', 'zip-recipes')},
                {value: 'flipOutDelay', label: __('flipOutDelay', 'zip-recipes')},
                {value: 'flipOut', label: __('flipOut', 'zip-recipes')},
                {value: 'flipOutDelay', label: __('flipOutDelay', 'zip-recipes')},
                {value: 'unfold', label: __('unfold', 'zip-recipes')},
                {value: 'flipOutDelay', label: __('flipOutDelay', 'zip-recipes')},
                {value: 'foldLeft', label: __('foldLeft', 'zip-recipes')},
                {value: 'scaleDown', label: __('scaleDown', 'zip-recipes')},
                {value: 'scaleSides', label: __('scaleSides', 'zip-recipes')},
                {value: 'frontRow', label: __('frontRow', 'zip-recipes')},
                {value: 'flipBottom', label: __('flipBottom', 'zip-recipes')},
                {value: 'rotateRoom', label: __('rotateRoom', 'zip-recipes')},
                ];
            let layoutOptions = [
                {value: 0, label: __('Select layout type', 'zip-recipes')},
                {value: 'grid', label: __('Grid', 'zip-recipes')},
                {value: 'mosaic', label: __('Mosaic', 'zip-recipes')},
            ];
            let sizeOptions = [
                {value: 0, label: __('Select size', 'zip-recipes')},
                {value: 'small', label: __('Small', 'zip-recipes')},
                {value: 'medium', label: __('Medium', 'zip-recipes')},
                {value: 'large', label: __('Large', 'zip-recipes')},
            ];
            let output = __('Loading...', 'zip-recipes');
            let id = 'recipe-grid';

            //load content
            if (this.state.gridContent) {
                output = this.state.gridContent.content;
            }

            //preview
            if (this.props.attributes.preview){
                return(
                    <img src={zrdn.zrdn_grid_preview} />
            );
            }

            return [
                !!this.props.isSelected && (
                <InspectorControls key='inspector'>

                            <CheckboxControl
                                label={__("Show title below the recipes","zip-recipes")}
                            checked={this.props.attributes.showTitle}
                            onChange={this.onChangeShowTitle}
                            />

                            <SelectControl onChange={this.onChangeSelectAnimationType} value={this.props.attributes.animationType} label={__('Animation type', 'zip-recipes')} options={animationOptions} />
                            <SelectControl onChange={this.onChangeSelectLayoutMode} value={this.props.attributes.layoutMode} label={__('Layout type', 'zip-recipes')} options={layoutOptions} />
                            <SelectControl onChange={this.onChangeSelectSize} value={this.props.attributes.size} label={__('Image Size', 'zip-recipes')} options={sizeOptions} />


            <TextControl
            type='number'
            label={__("Number of recipes to load initially","zip-recipes")}
            value={ this.props.attributes.recipesPerPage }
            onChange={this.onChangeSetRecipesPerPage}
            />
            <CheckboxControl
            label={__("Ajax load, use if not all categories are loaded","zip-recipes")}
            checked={this.props.attributes.useAjax}
            onChange={this.onChangeUseAjax}
            />
            <CheckboxControl
            label={__("Show load more button","zip-recipes")}
            checked={this.props.attributes.loadMoreButton}
            onChange={this.onChangeLoadMoreButton}
            />
            <CheckboxControl
            label={__("Show search field","zip-recipes")}
            checked={this.props.attributes.search}
            onChange={this.onChangeSetSearch}
            />
            <TextControl
            type='number'
            label={__("Horizontal gap","zip-recipes")}
            value={ this.props.attributes.gapHorizontal }
            onChange={this.onChangeSetGapHorizontal}
            />

            <TextControl
            type='number'
            label={__("Vertical gap","zip-recipes")}
            value={ this.props.attributes.gapVertical }
            onChange={this.onChangeSetGapVertical}
            />
            <SelectControl onChange={this.onChangeSelectCategory} value={this.props.attributes.category} label={__('Select a category', 'zip-recipes')}
            options={categoryOptions} />
            <ColorPicker
            color={ this.props.attributes.color }
            onChangeComplete={this.onChangeSetColor}
            disableAlpha
            />
            <ColorPicker
            color={ this.props.attributes.backgroundColor }
            onChangeComplete={this.onChangeSetBackgroundColor}
            disableAlpha
            />
            <ColorPicker
            color={ this.props.attributes.borderColor }
            onChangeComplete={this.onChangeSetBorderColor}
            disableAlpha
            />
                    </InspectorControls>),

                 <div key={id} className={className} dangerouslySetInnerHTML={ { __html: output } }></div>
            ]
        }

    }
    //https://developer.wordpress.org/block-editor/components/button/
    //https://developer.wordpress.org/block-editor/components/checkbox-control/

    /**
     * Register: a Gutenberg Block.
     *
     * Registers a new block provided a unique name and an object defining its
     * behavior. Once registered, the block is made editor as an option to any
     * editor interface where blocks are implemented.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/
     * @param  {string}   name     Block name.
     * @param  {Object}   settings Block settings.
     * @return {?WPBlock}          The block, if it has been successfully
     *                             registered; otherwise `undefined`.
     */

    registerBlockType('zip-recipes/recipe-grid-block', {
        title: __ ('Zip Recipes Grid','zip-recipes'),
        example: {
            attributes: {
                'preview' : true,
            },
        },
        description: __ ('Create a recipe grid.', 'zip-recipes'),
        icon: 'screenoptions', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
        category: 'widgets',
        keywords: [
            __('Recipe', 'zip-recipes'),
            __('Zip Recipes', 'zip-recipes'),
            __('Grid', 'zip-recipes'),
            __('Recipes', 'zip-recipes'),
        ],
        //className: 'zrdn-recipe',
        attributes: {
            category: {
                type: 'string',
                default: 'all',
            },
            showTitle: {
                type: 'boolean',
                default: false,
            },
            loadMoreButton: {
                type: 'boolean',
                default: true,
            },
            useAjax: {
                type: 'boolean',
                default: false,
            },
            search: {
                type: 'boolean',
                default: true,
            },
            layoutMode: {
                type: 'string',
                default: 'grid',
            },
            gapHorizontal: {
                type: 'integer',
                default: 0,
            },
            gapVertical: {
                type: 'integer',
                default: 0,
            },
            backgroundColor: {
                type: 'string',
                default: '#545454',
            },
            color: {
                type: 'string',
                default: '#fff',
            },
            borderColor: {
                type: 'string',
                default: '#5d5d5d',
            },
            size: {
                type: 'string',
                default: 'medium',
            },
            animationType: {
                type: 'string',
                default: 'quicksand',
            },

            recipesPerPage: {
                type: 'integer',
                default: 20,
            },
            gridContent: {
                type: 'string',
                source: 'children',
                selector: 'p',
            },
            categoriess: {
                type: 'array',
            },
            preview: {
                type: 'boolean',
                default: false,
            }
        },
        /**s
         * The edit function describes the structure of your block in the context of the editor.
         * This represents what the editor will render when the block is used.
         *
         * The "edit" property must be a valid function.
         *
         * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
         */

        edit:selectGrid,

        /**
         * The save function defines the way in which the different attributes should be combined
         * into the final markup, which is then serialized by Gutenberg into post_content.
         *
         * The "save" property must be specified and must be a valid function.
         *
         * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
         */

        save: function(props) {
            const { attributes: { gapHorizontal, gapVertical, layoutMode, animationType }} = props;
            // Rendering in PHP
            return null;
        },
    });



