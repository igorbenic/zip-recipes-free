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
const { InspectorControls } = wp.blockEditor;
const { SelectControl } = wp.components;
const { Button } = wp.components;
const { CheckboxControl } = wp.components;
const { PanelBody, PanelRow } = wp.components;

const { withState } = wp.compose;
const { Component } = wp.element;

const {RichText} = wp.blockEditor;
const {AlignmentToolbar} = wp.blockEditor;
const {BlockControls} = wp.blockEditor;
// const AlignmentToolbar = editor.AlignmentToolbar;
// const BlockControls = editor.BlockControls;

const el = wp.element.createElement;

/**
 *  Set custom Zip Recipes Icon
 */


const iconEl =
    el('svg', { width: 20, height: 20 ,viewBox : "0 0 64 64"},

        el('path', { className:"cls-1", d: "M41.89,39.32H39.14c-.75,0-1.71.34-2,2v9.71c-.06.57-.26.58-.32.56h-.1L23.51,39.93a.44.44,0,0,0-.29-.11H18.73a1.22,1.22,0,0,0-.87.34,2.17,2.17,0,0,0-.48,1.56l0,17.45c.14,2.06,1.34,2.37,2,2.37h2.3c1.55-.33,1.87-1.21,1.87-1.89l0-9c0-.64.13-1,.36-1l13.3,11.63a.48.48,0,0,0,.28.1l5.24.15h0c.42,0,1.13-.18,1.28-1.42l0-18.82C44.08,40.65,43.81,39.52,41.89,39.32Z" } ),
        el('path', { className:"cls-1", d: "M41.89,39.32H39.14c-.75,0-1.71.34-2,2v9.71c-.06.57-.26.58-.32.56h-.1L23.51,39.93a.44.44,0,0,0-.29-.11H18.73a1.22,1.22,0,0,0-.87.34,2.17,2.17,0,0,0-.48,1.56l0,17.45c.14,2.06,1.34,2.37,2,2.37h2.3c1.55-.33,1.87-1.21,1.87-1.89l0-9c0-.64.13-1,.36-1l13.3,11.63a.48.48,0,0,0,.28.1l5.24.15h0c.42,0,1.13-.18,1.28-1.42l0-18.82C44.08,40.65,43.81,39.52,41.89,39.32Z" } ),
        el('path', { className:"cls-1", d: "M42.14,29.19l-22.76-.05c-.71,0-1.63.32-2,2v3h0a1.51,1.51,0,0,0,.16.7c.19.62.77,1.35,2.43,1.35l21.65,0h0a2.19,2.19,0,0,0,2.46-2.38V31.38C43.88,29.57,42.91,29.19,42.14,29.19Z" } ),
        el('path', { className:"cls-1", d: "M42.22,18.79l-5,0a1.28,1.28,0,0,1-.8-.2.72.72,0,0,1-.2-.6v-3.8a11.46,11.46,0,0,0-.88-4.24C33.42,6.37,31.23,4,27,4h0a10.74,10.74,0,0,0-4.28.87c-3.62,1.93-5.24,4.76-5.25,9.18l0,9.56a2.06,2.06,0,0,0,.64,1.64,2.55,2.55,0,0,0,1.75.56l22.05,0h0a2.1,2.1,0,0,0,2.25-2V20.7C44.12,20,43.81,19.13,42.22,18.79ZM30,17.69c0,.4-.13.89-1.06,1.07H24.74c-.44,0-.95-.15-1.07-1.29V13a3.33,3.33,0,0,1,.05-.56,2.92,2.92,0,0,1,3-2.33c.71,0,.94,0,1,0A2.68,2.68,0,0,1,30,13Z" } ),
    );

    class selectRecipe extends Component {
        // Method for setting the initial state.
        static getInitialState(id) {

            return {
                recipes: [],
                id: id,
                showUnLinkedRecipes: true,
                recipe: {},
                recipeContent :'',
                hasRecipes: true,
                preview: false,
            };
        }

        // Constructing our component. With super() we are setting everything to 'this'.
        // Now we can access the attributes with this.props.attributes
        constructor() {
            super(...arguments);
            // Maybe we have a previously selected recipe. Try to load it.
            this.state = this.constructor.getInitialState(this.props.attributes.id);

            // Bind so we can use 'this' inside the method.
            this.getRecipes = this.getRecipes.bind(this);
            this.getRecipes();

            this.getRecipeContent(this.props.attributes.id);
            this.getRecipeContent = this.getRecipeContent.bind(this);

            this.onChangeSelectRecipe = this.onChangeSelectRecipe.bind(this);
            this.onChangeShowUnLinkedRecipes = this.onChangeShowUnLinkedRecipes.bind(this);

            this.onChangeShowUnLinkedRecipes(true);
            this.isRecipeSelected = this.isRecipeSelected.bind(this);
        }

        getRecipes(args = {}) {

            (
                api.getRecipes().then( ( response ) => {
                    let recipes = response.data;

                    if( recipes && 0 !== this.state.id ) {
                        // If we have a selected recipe, find that recipe and add it.
                        const recipe = recipes.find( ( item ) => { return item.id === this.state.id } );
                        if (recipes.length === 0) {
                            this.setState({hasRecipes: false});

                            this.props.setAttributes({
                                hasRecipes: false,
                            });
                        }

                        // This is the same as { recipe: recipe, recipes: recipes }
                        //this.state.recipes = recipes;
                        this.setState( { recipe, recipes } );
                    } else {
                        //this.state.recipes = recipe;
                        this.setState({ recipes });
                    }
                })
            )

        }

        getRecipeContent(recipeID){
            (
                api.getRecipe(recipeID).then( ( response ) => {
                    let recipeContent = response.data;
                    if( recipeContent ) {
                        this.setState( { recipeContent } );
                    }
                })
            )
        }

        getEditRecipeURL(args = {}) {
            const recipeID = this.props.attributes.id;
            const postID = wp.data.select("core/editor").getCurrentPostId( );

            //wp-admin/admin.php?page=zrdn-recipes&id=1
            return zrdn.site_url + '/wp-admin/admin.php?page=zrdn-recipes&id='+recipeID+'&post_id='+postID;
        }

        getCreateRecipeURL(args = {}) {
            const postID = wp.data.select("core/editor").getCurrentPostId( );
            const postType = wp.data.select("core/editor").getCurrentPostType();
            return zrdn.site_url + '/wp-admin/?page=zrdn-recipes&action=new&post_id='+postID+'&post_type='+postType;
        }

        onChangeSelectRecipe(value) {

            const recipe = this.state.recipes.find((item) => {
                return item.id === value
            });

            // Set the state
            this.setState({id: value, recipe});

            //get new content
            this.getRecipeContent(value);

            // Set the attributes
            this.props.setAttributes({
                id: value,
            });

        }

        onChangeShowUnLinkedRecipes(checked){
            // Set the state
            this.setState({showUnLinkedRecipes: checked});

            this.props.setAttributes({
                showUnLinkedRecipes: checked,
            });
        }

        isRecipeSelected(args = {}) {
            if (this.props.attributes.id!==0 && this.state.recipe && this.state.recipe.hasOwnProperty('title')) {
                return false;
            }
            return true;
        }

        render() {
            const { className, attributes: {} = {} } = this.props;

            let options = [{value: 0, label: __('Select a recipe', 'zip-recipes')}];
            let output = __('Loading...', 'zip-recipes');
            let id = 'recipe-title';
            let postID = wp.data.select("core/editor").getCurrentPostId( );

            if (!this.props.attributes.hasRecipes){
                output = __('No recipes found. Create a recipe first!', 'zip-recipes');
                id = 'no-recipes';
            }

            //preview
            if (this.props.attributes.preview){
                return(
                    <img src={zrdn.zrdn_recipe_preview} />
            );
            }

            //build options
            if (this.state.recipes.length > 0) {
                if (!this.props.isSelected){
                    output = __('No recipe selected. Click this block to show the recipe controls in the sidebar', 'zip-recipes');
                } else {
                    output =  sprintf(__('%sCreate a new recipe%s, or select a recipe from the dropdownlist', 'zip-recipes'),'<a href="'+this.getCreateRecipeURL()+'">','</a>');
                }
                this.state.recipes.forEach((recipe) => {

                    //show unlinked recipes only, where post_id=0
                    if (this.props.attributes.showUnLinkedRecipes){
                        if  (recipe.post_id==0 || recipe.post_id==postID) {
                            options.push({value: recipe.id, label: recipe.title});
                        }
                    } else {
                        //show all
                        options.push({value: recipe.id, label: recipe.title});
                    }

                });
            }

            //load content
            if (this.props.attributes.id!==0 && this.state.recipe && this.state.recipe.hasOwnProperty('title')) {
                output =this.state.recipeContent.content;
                id = this.props.attributes.id;
            }

            return [
            !!this.props.isSelected && (
                        <InspectorControls key='inspector'>
                    <PanelBody title={ __('Recipe controls', 'zip-recipes' ) }initialOpen={ true } >
                <PanelRow>
                            <CheckboxControl
                                label={__("Show only unlinked recipes","zip-recipes")}
                                checked={this.props.attributes.showUnLinkedRecipes}
                                onChange={this.onChangeShowUnLinkedRecipes}
                                />
                                </PanelRow><PanelRow>
                                <SelectControl onChange={this.onChangeSelectRecipe} value={this.props.attributes.id} label={__('Select a recipe', 'zip-recipes')}
                                               options={options} />
            </PanelRow><PanelRow>

                                <div className="components-base-control">
                                    <div className="components-base-control__field">
                                    <Button isDefault
                                    disabled={ this.isRecipeSelected() }
                                    href={this.getEditRecipeURL()}>
                                        {__("Edit recipe","zip-recipes")}
                                        </Button>
                                    </div>
                                    <div className="components-base-control__field">
                                        <Button isDefault href={this.getCreateRecipeURL()}>
                                            {__("Create and insert recipe","zip-recipes")}
                                        </Button>
                                    </div></div>
            </PanelRow></PanelBody>

            </InspectorControls>
                ),

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

    registerBlockType('zip-recipes/recipe-block', {
        title: __ ('Zip Recipes','zip-recipes'),
        example: {
            attributes: {
                'preview' : false,
            },
        },
        description: __ ('Create a recipe card.', 'zip-recipes'),
        icon: iconEl, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
        category: 'widgets',
        keywords: [
            __('Recipe', 'zip-recipes'),
            __('Zip Recipes', 'zip-recipes'),
            __('Recipes', 'zip-recipes'),
        ],
        //className: 'zrdn-recipe',
        attributes: {
            hasRecipes: {
                type: 'string',
                default: 'false',
            },
            content: {
                type: 'string',
                source: 'children',
                selector: 'p',
            },
            id: {
                type: 'string',
                default: '',
            },
            recipes: {
                type: 'array',
            },
            recipe: {
                type: 'array',
            },
            showUnLinkedRecipes: {
                type: 'boolean',
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

        edit:selectRecipe,

        /**
         * The save function defines the way in which the different attributes should be combined
         * into the final markup, which is then serialized by Gutenberg into post_content.
         *
         * The "save" property must be specified and must be a valid function.
         *
         * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
         */

        save: function() {
            // Rendering in PHP
            return null;
        },
    });



