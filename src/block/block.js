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
const { Button } = wp.components;
const { CheckboxControl } = wp.components;
const { withState } = wp.compose;
const { Component } = wp.element;

const {RichText} = wp.editor;
const {AlignmentToolbar} = wp.editor;
const {BlockControls} = wp.editor;
// const AlignmentToolbar = editor.AlignmentToolbar;
// const BlockControls = editor.BlockControls;

const el = wp.element.createElement;

/**
 *  Set custom Zip Recipes Icon
 */

const iconEl =
    el('svg', { width: 20, height: 20 ,viewBox : "0 0 64 64"},
        el('path', { className:"cls-4", d: "M17.26-3.5S19-12.79,10.75-13.64C4.69-14.27-13.12-.44-14,20.17c-.33,7.39,1.51,15.66,6.84,24.44C13,77.89,72.43,80.35,59.62,109.75S103.7,87.69,56,67.51c0,0,18.4,4.94,20.74,17.31,0,0,.71-20.34-36.81-40C10.41,29.28,18,7.3,18,7.3Z" } ),
        el('path', { className:"cls-4",d: "M-12.63,11a101.23,101.23,0,0,0-.86,24.91C-12.13,48.14,5.36,70.51,19.88,75s35.5,21.78,30.71,28.89S45,110.33,45,110.33s11.58-3,10.6-20.07S23.13,67.9,14.34,66.12-16.1,53.26-12.63,11Z" } ),
        el('path', { className:"cls-5",d: "M-40.63,66.53c2.51,1.3,5.13,2.71,5.9,3.53,1.5,1.57,7.46,48.5,7.46,48.5L-9.35,92.65,7,84.15s-30.37,46.75-37,51.8-6.76-37.61-6.82-41-1.49-21.2-1.49-21.2c-1-1.74-10.41,1.2-16.12,4.1s-4,.8-4,.8l8.75-5-6.6-2.72a38.65,38.65,0,0,0,2.66-3.28c1.43-2,5.88-9.67,5.88-9.67.87,1.48-3.58,9.1-3.58,9.1l.28-.07c1.93-1.07,4.87-7.44,4.87-7.44.91,2.46-4.86,10-4.86,10l2.89,2.38,7.36-3.31s-1.17-.92-2.53-2" } ),
        el('path', { className:"cls-6",d: "M31.84,44.89S28.43,66.32,24.29,66s-13-5.71-17.83,2.73-19,25.93-19,25.93l9.32,7.42L6.84,89.36l10.23,11.55,25.5,5.94,1.07,17.67,11.6-.38s1.58-42.4-2.14-47.58S38,74.7,38.48,63.36a114.23,114.23,0,0,1,2.28-19Z" } ),
        el('path', { className:"cls-7",d: "M28.62,85.65c8.5,2.41,17.37-3,22.79-10.65C46.76,72,38,73.31,38.48,63.36a114.23,114.23,0,0,1,2.28-19l-8.92.5S28.43,66.32,24.29,66a56.62,56.62,0,0,1-6-1.45C17.92,70.86,19.31,83,28.62,85.65Z" } ),
        el('path', { className:"cls-4",d: "M8.08-.93S-10.54,9.72,3.85,35.43c10.41,18.62,11.2-8.73,13.41-15S17.65.88,17.65.88Z" } ),
        el('path', { className:"cls-8",d: "M8.7,46.37s-4.53-.93-6.37-4.87C-1.25,33.88,5,34.72,6.25,36.13S8.7,46.37,8.7,46.37Z" } ),
        el('path', { className:"cls-9",d: "M6.72,43.48s-3.46-1.67-3.4-4.36,1.57,0,1.57,0l-.21-1.26S7.85,43.13,6.72,43.48Z" } ),
        el('path', { className:"cls-8",d: "M56.88,39.75s4.37-1.51,5.71-5.65c2.58-8-3.51-6.38-4.57-4.82S56.88,39.75,56.88,39.75Z" } ),
        el('path', { className:"cls-9",d: "M58.35,36.38s3.21-2.1,2.82-4.75-1.57.14-1.57.14l0-1.28S57.18,36.18,58.35,36.38Z" } ),
        el('path', { className:"cls-8",d: "M58.37,13.72l-20-11.33L22,8.29S2.53,14.59,4,30.58C4.7,38.26,7,62.16,34.19,56c.21-.05,6.19-1.14,6.15-1.16C67,45.76,58.37,13.72,58.37,13.72Z" } ),
        el('path', { className:"cls-10",d: "M39.21,23.22s4.46-5.1,8.87-4c.28.07.5,0,0-.23-1.29-.57-5-.07-6.56.86-2.4,1.44-4.15,2.72-3.43,3.23S39.21,23.22,39.21,23.22Z" } ),
        el('path', { className:"cls-10",d: "M22,26.53s-6-3.08-9.69-.38c-.24.17-.46.18-.09-.21,1-1,4.63-1.93,6.4-1.64,2.76.46,4.86,1,4.39,1.73S22,26.53,22,26.53Z" } ),
        el('path', { className:"cls-11",d: "M2,33.15Z" } ),
        el('path', { className:"cls-12",d: "M30.29,39.26A2.7,2.7,0,0,0,33,41.6c2.52.15-3.24,1.19-3.33-.49S30.29,39.26,30.29,39.26Z" } ),
        el('path', { className:"cls-13",d: "M25.75,48.43S29,52,32.81,50.84s8.86-4.53,6.6-4.84a18.28,18.28,0,0,0-4.57.06l-2,1.09-3.68.21Z" } ),
        el('path', { className:"cls-14",d: "M26.13,48.37s4.51,2.41,13.21-2.06A5.35,5.35,0,0,0,37,45.84c-.78.19-2.77.91-2.77.91a22.24,22.24,0,0,1-3.6.5A29.32,29.32,0,0,0,26.13,48.37Z" } ),
        el('path', { className:"cls-15",d: "M27,48.3S24.6,50,32.06,50.11c3.95-.45,7.54-2.5,7.47-4.56l1.2-.53s-.59,1.25-1,2.16a7.71,7.71,0,0,1-6.51,5.65C27.1,54,26.4,49.55,26.4,49.55a17.4,17.4,0,0,0-1.57-1.12A16.22,16.22,0,0,1,27,48.3Z" } ),
        el('path', { className:"cls-15",d: "M24.9,48.5a11.69,11.69,0,0,0,4.11-2,3.48,3.48,0,0,1,3.22.14s.72-1.21,1.53-1.32,4.52.71,7.29-.56C41.05,44.77,38.29,47.62,24.9,48.5Z" } ),
        el('path', { className:"cls-16",d: "M28.67,48.17A14.51,14.51,0,0,1,24,47.85S26.2,50.26,31,50.1C31,50.1,23.11,49,28.67,48.17Z" } ),
        el('path', { className:"cls-16",d: "M36.56,46.74s3.8-1,5.35-2.69a11.06,11.06,0,0,1-5.87,5S42.05,45.37,36.56,46.74Z" } ),
        el('path', { className:"cls-14",d: "M31.28,51.41a.38.38,0,0,1-.23.49.39.39,0,0,1-.48-.23.37.37,0,0,1,.22-.48A.38.38,0,0,1,31.28,51.41Z" } ),
        el('path', { className:"cls-14",d: "M19.53,32.76C16,32.83,13,34,11.83,35.17c.07.1.3.6.42.74.95,1.09,3.22,2.75,4.75,3,4.19.53,8.48-3.16,8.42-4,0-.17-1.18-.84-1.26-1.25C23.31,32.74,22.53,32.71,19.53,32.76Z" } ),
        el('path', { className:"cls-17",d: "M16.65,36.55a3.09,3.09,0,1,0,1.09-4.24A3.09,3.09,0,0,0,16.65,36.55Z" } ),
        el('path', { className:"cls-18",d: "M17.45,36.08a2.15,2.15,0,0,0,3,.76,2.17,2.17,0,1,0-3-.76Z" } ),
        el('path', { className:"cls-14",d: "M17.23,36.65a1,1,0,1,0,.35-1.38A1,1,0,0,0,17.23,36.65Z" } ),
        el('path', { className:"cls-18",d: "M14.61,34.62a6,6,0,0,1-3.28.75c-1-.31-1.33-.91-2-1.86,0,0,2.12.69,3,.3,0,0-2-.26-2.19-1.91,0,0,2.39,1.35,3.68,1.18a4.22,4.22,0,0,1-1.59-1.37,10.41,10.41,0,0,0,2.89.49c.75-.1,4.78-2,8.58.89a8.69,8.69,0,0,1,1.64,1.63A11.18,11.18,0,0,0,14.61,34.62Z" } ),
        el('path', { className:"cls-14",d: "M43,28.78c3.12-1.56,6.38-1.89,7.94-1.44,0,.12,0,.68,0,.86-.33,1.4-1.59,3.93-2.85,4.8-3.47,2.43-9,1.15-9.3.4-.06-.16.67-1.29.56-1.69C39.64,30.52,40.31,30.12,43,28.78Z" } ),
        el('path', { className:"cls-17",d: "M47.29,30.81a3.08,3.08,0,1,1-2.9-3.25A3.08,3.08,0,0,1,47.29,30.81Z" } ),
        el('path', { className:"cls-18",d: "M46.38,30.76a2.17,2.17,0,1,1-2-2.29A2.16,2.16,0,0,1,46.38,30.76Z" } ),
        el('path', { className:"cls-14",d: "M46.84,31.16a1,1,0,1,1-1-1.06A1,1,0,0,1,46.84,31.16Z" } ),
        el('path', { className:"cls-18",d: "M48.23,28.15a6,6,0,0,0,3.25-.86c.76-.74.75-1.42.93-2.58,0,0-1.56,1.6-2.56,1.67,0,0,1.63-1.15,1.07-2.71,0,0-1.49,2.31-2.73,2.76a4.28,4.28,0,0,0,.79-2,10.68,10.68,0,0,1-2.34,1.79c-.72.26-5.15.47-7.2,4.76a8.82,8.82,0,0,0-.71,2.21A11.2,11.2,0,0,1,48.23,28.15Z" } ),
        el('path', { className:"cls-4",d: "M6.55.47S7.08,15,15.63,15.63s40-2.62,44.09,12.94c0,0,4.21-36.9-22.51-41.34S6.55.47,6.55.47Z" } ),
        el('path', { className:"cls-19",d: "M23.64,143.23c1.32,0,2.62,0,3.93-.1a9.66,9.66,0,0,1-1.47-6.56c1-7,3.68-15.12,12.9-22.5,3.55-2.84,7.31-5.29,6.25-12.92l6.52-26.27-4.83-2.06s-2,26-12.17,26.33S10.18,95.55,11.33,87.1s8.21-22.51,8.21-22.51l-5.73-.78S6.88,89.1,4.12,93.15-.57,100.44,2.23,107,5.81,123.11-1,134.35a20.21,20.21,0,0,1-3.72,4A84.14,84.14,0,0,0,23.64,143.23Z" } ),
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
                //output = this.state.recipe.content;
                output =this.state.recipeContent.content;
                id = this.props.attributes.id;
            }

            return [
            !!this.props.isSelected && (
                        <InspectorControls key='inspector'>
                            <CheckboxControl
                                label={__("Show only unlinked recipes","zip-recipes")}
                                checked={this.props.attributes.showUnLinkedRecipes}
                                onChange={this.onChangeShowUnLinkedRecipes}
                                />

                                <SelectControl onChange={this.onChangeSelectRecipe} value={this.props.attributes.id} label={__('Select a recipe', 'zip-recipes')}
                                               options={options} />

            <div className="components-base-control"><div className="components-base-control__field">
                <Button isDefault
                disabled={ this.isRecipeSelected() }
                href={this.getEditRecipeURL()}>
                    {__("Edit recipe","zip-recipes")}
                    </Button>
                </div></div><div className="components-base-control"><div className="components-base-control__field">
                            <Button isDefault href={this.getCreateRecipeURL()}>
                                {__("Create and insert recipe","zip-recipes")}
                            </Button>
                </div></div>
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



