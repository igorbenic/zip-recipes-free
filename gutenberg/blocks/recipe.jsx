const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;

const { disableAuthorPromo } = require('./promos');
const {actions} = require ('../store/zip-recipes-store');
const {onCalculateNutrition} = require('./nutrition_calculator');
const {Author} = require ('./author');
const {
  TitleAndImage,
  Ingredients,
  Instructions,
  CategoryAndCuisine,
  Description,
  PrepAndCookTime,
  Notes,
  ServingsAndSize,
  Calories,
  Carbs,
  Protein,
  Fiber,
  Sugar,
  Sodium,
  Fat,
  SaturatedFat,
  TransFat,
  Cholesterol,
} = require ('./components');

const {withState} = wp.compose;

let blocks = window.wp.blocks;
let editor = window.wp.editor;
let i18n = window.wp.i18n;
let element = window.wp.element;
let components = window.wp.components;
let _ = window._;

const {Button, Modal, Icon, Spinner} = wp.components;

var el = wp.element.createElement, Fragment = wp.element.Fragment;

const {data} = wp;
const {registerStore, withSelect, withDispatch, select} = data;

registerBlockType ('zip-recipes/recipe-block', {
  title: __ ('Zip Recipes'),
  description: __ ('Create a recipe card.'),
  icon: {
    src: 'carrot',
    foreground: '#4AB158',
  },
  category: 'widgets',
  keywords: [__ ('Zip Recipes'), __ ('Recipe'), __ ('Recipe Card')],

  attributes: {
    id: {
      type: 'string',
    },
  },
  supports: {
    reusable: false,
    multiple: false,
  },
  edit: withDispatch ((dispatch, ownProps) => {
    const creators = dispatch ('zip-recipes-store');
    const {getCurrentPost} = select ('core/editor');
    const store = select ('zip-recipes-store');
    const noticeActions = dispatch ('core/notices');

    let dispatchMethods = {
      async onRegister (
        endpoint,
        firstName,
        lastName,
        email,
        wpVersion,
        blogUrl
      ) {
        creators.setIsRegistering ();
        await creators.register (
          endpoint,
          firstName,
          lastName,
          email,
          wpVersion,
          blogUrl
        );
        await creators.setRegisteredBackend (firstName, lastName, email);
        creators.setIsRegisteringSuccess ();
      },
      setInitialTitle () {
        creators.setTitle (getCurrentPost ().title);
      },
      onTitleChange({target: {value}}) {
        creators.setTitle (value);
      },
      async onImageChange (id, {url}) {
        creators.setImageUrl (url);

        if (id) {
          // set image url
          try {
            await creators.saveImage (id, url);
          } catch (e) {
            noticeActions.createErrorNotice (
              `Failed to set image on recipe recipe id: ${id}`
            );

            console.log (
              'Failed to set image on recipe recipe id:',
              id,
              '. Error:',
              e
            );
          }
        } else {
          noticeActions.createErrorNotice (
            `No recipe id present. Did you save the recipe?`
          );

          console.log ('Image saved on a recipe that has no id yet.');
        }
      },
      onIngredientsChange({target: {value}}) {
        creators.setIngredients (value);
      },
      /**
       * Handle paste so we can clean up some stuff.
       */
      onIngredientsPaste () {
        window.setTimeout (function () {
          let existingLines = store.getIngredients ();
          let newLines = [];
          for (var i = 0; i < existingLines.length; i++) {
            if (/\S/.test (existingLines[i])) {
              newLines.push ($.trim (existingLines[i]));
            }
          }
          creators.setIngredients (newLines);
        }, 500);
      },
      onInstructionsChange({target: {value}}) {
        creators.setInstructions (value);
      },
      /**
       * Handle paste so we can clean up some stuff.
       */
      onInstructionsPaste () {
        window.setTimeout (function () {
          let existingLines = store.getInstructions ();
          let newLines = [];
          for (var i = 0; i < existingLines.length; i++) {
            if (/\S/.test (existingLines[i])) {
              newLines.push ($.trim (existingLines[i]));
            }
          }
          creators.setInstructions (newLines);
        }, 500);
      },
      onAuthorChange({target: {value}}) {
        creators.setAuthor (value);
      },
      onCategoryChange({target: {value}}) {
        creators.setCategory (value);
      },
      onCuisineChange({target: {value}}) {
        creators.setCuisine (value);
      },
      onDescriptionChange({target: {value}}) {
        creators.setDescription (value);
      },
      onPrepTimeHoursChange({target: {value}}) {
        creators.setPrepTimeHours (value);
      },
      onPrepTimeMinutesChange({target: {value}}) {
        creators.setPrepTimeMinutes (value);
      },
      onCookTimeHoursChange({target: {value}}) {
        creators.setCookTimeHours (value);
      },
      onCookTimeMinutesChange({target: {value}}) {
        creators.setCookTimeMinutes (value);
      },
      onNotesChange({target: {value}}) {
        creators.setNotes (value);
      },
      onServingsChange({target: {value}}) {
        creators.setServings (value);
      },
      onServingSizeChange({target: {value}}) {
        creators.setServingSize (value);
      },
      onCaloriesChange({target: {value}}) {
        creators.setCalories (value);
      },
      onCarbsChange({target: {value}}) {
        creators.setCarbs (value);
      },
      onProteinChange({target: {value}}) {
        creators.setProtein (value);
      },
      onFiberChange({target: {value}}) {
        creators.setFiber (value);
      },
      onSugarChange({target: {value}}) {
        creators.setSugar (value);
      },
      onSodiumChange({target: {value}}) {
        creators.setSodium (value);
      },
      onFatChange({target: {value}}) {
        creators.setFat (value);
      },
      onSaturatedFatChange({target: {value}}) {
        creators.setSaturatedFat (value);
      },
      onTransFatChange({target: {value}}) {
        creators.setTransFat (value);
      },
      onCholesterolChange({target: {value}}) {
        creators.setCholesterol (value);
      },
      onCancel (setState) {
        setState ({isOpen: false});
      },
      async onSave (setAttributes, setState, id) {
        const recipe = {
          post_id: getCurrentPost ().id,
          title: store.getTitle (),
          category: store.getCategory (),
          cuisine: store.getCuisine (),
          description: store.getDescription (),
          author: store.getAuthor (),
          notes: store.getNotes (),
          ingredients: store.getIngredients (),
          instructions: store.getInstructions (),
          image_url: store.getImageUrl (),
          prep_time_hours: store.getPrepTimeHours (),
          prep_time_minutes: store.getPrepTimeMinutes (),
          cook_time_hours: store.getCookTimeHours (),
          cook_time_minutes: store.getCookTimeMinutes (),
          serving_size: store.getServingSize (),
          servings: store.getServings (),
          nutrition_label: store.getNutritionLabelUrl (),
          nutrition_label_attachment_id: store.getNutritionLabelAttachmentId (),
          nutrition: {
            calories: store.getCalories (),
            carbs: store.getCarbs (),
            protein: store.getProtein (),
            fiber: store.getFiber (),
            sugar: store.getSugar (),
            sodium: store.getSodium (),
            fat: store.getFat (),
            saturated_fat: store.getSaturatedFat (),
            trans_fat: store.getTransFat (),
            cholesterol: store.getCholesterol (),
          },
        };
        if (id) {
          // update recipe
          try {
            creators.setRecipeSaving ();
            await creators.saveRecipe ({
              recipe: {...recipe, id: id},
            });
            creators.saveRecipeSuccess ();

            // close modal
            setState ({isOpen: false});
          } catch (e) {
            noticeActions.createErrorNotice (
              `Failed to update recipe id: ${id}`
            );
            console.log ('Failed to update recipe id:', id, '. Error:', e);
          }
        } else {
          // create new recipe
          try {
            creators.setRecipeSaving ();
            let newRecipe = await creators.saveRecipe ({
              recipe: {...recipe}, // we don't have an ID to set here...we wait for server to send one for us back
              create: true,
            });
            creators.saveRecipeSuccess ();

            setAttributes ({
              id: newRecipe.id,
            });

            // close modal
            setState ({isOpen: false});
          } catch (e) {
            noticeActions.createErrorNotice (`Failed to create recipe`);
            console.log ('Failed to create new recipe:', e);
          }
        }
      },
    };

    dispatchMethods.onCalculateNutrition = onCalculateNutrition;

    return dispatchMethods;
  }) (
    withSelect ((select, props) => {
      const store = select ('zip-recipes-store');
      const {getCurrentPost} = select ('core/editor');

      let selected = {
        id: store.getId (),
        recipe: store.getRecipe (props.attributes.id),
        title: store.getTitle (),
        postTitle: getCurrentPost ().title,
        imageUrl: store.getImageUrl (),
        isFeaturedPostImage: store.getIsFeaturedPostImage (),
        category: store.getCategory (),
        cuisine: store.getCuisine (),
        description: store.getDescription (),
        author: store.getAuthor (),
        notes: store.getNotes (),
        ingredients: store.getIngredients (),
        instructions: store.getInstructions (),
        prepTimeHours: store.getPrepTimeHours (),
        prepTimeMinutes: store.getPrepTimeMinutes (),
        cookTimeHours: store.getCookTimeHours (),
        cookTimeMinutes: store.getCookTimeMinutes (),
        servings: store.getServings (),
        servingSize: store.getServingSize (),
        calories: store.getCalories (),
        carbs: store.getCarbs (),
        protein: store.getProtein (),
        fiber: store.getFiber (),
        sugar: store.getSugar (),
        sodium: store.getSodium (),
        fat: store.getFat (),
        saturatedFat: store.getSaturatedFat (),
        transFat: store.getTransFat (),
        cholesterol: store.getCholesterol (),
        isSaving: store.getIsSaving (),
        isFetching: store.getIsFetching (),
        settings: store.getSettings (),
        isRegistering: store.getIsRegistering (),
        nutritionCalculationError: store.getNutritionCalculationError (),
        nutritionLabelUrl: store.getNutritionLabelUrl (),
        isNutritionCalculating: store.getIsNutritionCalculation (),
        promos: store.getPromos (
          store.getSettings ().promos_endpoint,
          store.getSettings ().blog_url
        ),
      };

      if (disableAuthorPromo()) {
        selected.promos.author = '';
      }

      // We don't need to turn off nutrition promo since it only appears if nutrition calculator is not in the plan
      // (i.e. non-Lover plan)
      
      return selected;
    }) (
      withState ({
        isOpen: false,
        firstName: '',
        lastName: '',
        email: '',
        showNutritionFields: false,
        showNutritionPromo: false,
        showAuthorPromo: false,
      }) (props => {
        const renderRegister = () => (
          <div
            style={{
              backgroundColor: 'rgb(246, 243, 251)',
              padding: '20px',
              marginBo: '20px',
            }}
          >
            <h2>Register Zip Recipes Free</h2>
            <small>
              Please register your plugin so we can email you news about updates to Zip Recipes, including tips and tricks on how to use it.
              Registering also helps us troubleshoot any problems you may encounter. When you register, we will
              automatically receive your blog's web address, WordPress version, and names of installed plugins.
            </small>
            <div className="zrdn-columns zrdn-is-mobile">
              <div className="zrdn-column">
                <div className="zrdn-field">
                  <label htmlFor="first-name" className="zrdn-label">
                    First name
                  </label>
                  <div className="zrdn-control">
                    <input
                      className="zrdn-input zrdn-is-small"
                      id="first-name"
                      onChange={({target: {value}}) =>
                        props.setState ({firstName: value})}
                      type="text"
                      name="first-name"
                      value={props.firstName}
                    />
                  </div>
                </div>
              </div>
              <div className="zrdn-column">
                <div className="zrdn-field">
                  <label htmlFor="last-name" className="zrdn-label">
                    Last name
                  </label>
                  <div className="zrdn-control">
                    <input
                      className="zrdn-input zrdn-is-small"
                      onChange={({target: {value}}) =>
                        props.setState ({lastName: value})}
                      type="text"
                      id="last-name"
                      name="last-name"
                      value={props.lastName}
                    />
                  </div>
                </div>
              </div>
            </div>
            <div className="zrdn-columns zrdn-is-mobile">

              <div className="zrdn-column">
                <div className="zrdn-field">
                  <label htmlFor="recipe-title" className="zrdn-label">
                    Email
                  </label>
                  <div className="zrdn-control" id="title-container">
                    <input
                      id="recipe-title"
                      name="recipe-title"
                      className="zrdn-input"
                      type="email"
                      value={props.email}
                      onChange={({target: {value}}) =>
                        props.setState ({email: value})}
                    />
                  </div>
                </div>
              </div>
            </div>
            <div className="zrdn-columns zrdn-is-mobile zrdn-is-pulled-right zrdn-is-clearfix">
              <Button
                isPrimary
                isBusy={props.isRegistering}
                onClick={props.onRegister.bind (
                  null,
                  props.settings.registration_endpoint,
                  props.firstName,
                  props.lastName,
                  props.email,
                  props.settings.wp_version,
                  props.settings.blog_url
                )}
              >
                Register
              </Button>
            </div>
          </div>
        );

        let calculateButtonClasses = props.isNutritionCalculating
          ? 'zrdn-button zrdn-is-primary zrdn-is-loading'
          : 'zrdn-button zrdn-is-primary';

        return (
          <div>
            {props.settings.registered ? '' : renderRegister ()}
            {props.attributes.id
              ? <Button
                  isPrimary
                  isLarge
                  isBusy={props.isFetching}
                  disabled={props.isFetching}
                  onClick={
                    props.isFetching
                      ? () => {}
                      : () => props.setState ({isOpen: true})
                  }
                >
                  {props.isFetching ? 'Loading recipe...' : 'Edit Recipe'}
                </Button>
              : <Button
                  isDefault
                  onClick={() => {
                    props.setState ({isOpen: true});
                    props.setInitialTitle ();
                  }}
                >
                  Create Recipe
                </Button>}
            {!props.isFetching && props.attributes.id
              ? <div>
                  <TitleAndImage
                    title={props.title}
                    recipeId={props.attributes.id}
                    onTitleChange={props.onTitleChange}
                    isTitleEditable={false}
                    isImageEditable={true}
                    onImageChange={props.onImageChange}
                    imageUrl={props.imageUrl}
                    isFeaturedPostImage={props.isFeaturedPostImage}
                  />
                  <Ingredients
                    ingredients={props.ingredients}
                    onIngredientsChange={props.onIngredientsChange}
                    onIngredientsPaste={props.onIngredientsPaste}
                    editable={false}
                  />
                  <Instructions
                    editable={false}
                    onInstructionsChange={props.onInstructionsChange}
                    instructions={props.instructions}
                    onInstructionsPaste={props.onInstructionsPaste}
                  />
                  <Author
                    editable={false}
                    onChange={props.onAuthorChange}
                    selectedAuthor={
                      props.author
                        ? props.author
                        : props.settings.default_author
                    }
                    authors={props.settings.authors}
                  />
                  <CategoryAndCuisine
                    editable={false}
                    onCategoryChange={props.onCategoryChange}
                    category={props.category}
                    onCuisineChange={props.onCuisineChange}
                    cuisine={props.cuisine}
                  />
                  <Description
                    editable={false}
                    onDescriptionChange={props.onDescriptionChange}
                    description={props.description}
                  />
                  <PrepAndCookTime
                    editable={false}
                    onPrepTimeHoursChange={props.onPrepTimeHoursChange}
                    onPrepTimeMinutesChange={props.onPrepTimeMinutesChange}
                    onCookTimeHoursChange={props.onCookTimeHoursChange}
                    onCookTimeMinutesChange={props.onCookTimeMinutesChange}
                    cookTimeHours={props.cookTimeHours}
                    cookTimeMinutes={props.cookTimeMinutes}
                    prepTimeHours={props.prepTimeHours}
                    prepTimeMinutes={props.prepTimeMinutes}
                  />
                  <Notes
                    onNotesChange={props.onNotesChange}
                    notes={props.notes}
                    editable={false}
                  />
                  <ServingsAndSize
                    onServingsChange={props.onServingsChange}
                    servings={props.servings}
                    onServingSizeChange={props.onServingSizeChange}
                    editable={false}
                    servingSize={props.servingSize}
                  />
                  <Calories
                    onCaloriesChange={props.onCaloriesChange}
                    editable={false}
                    calories={props.calories}
                  />
                  <Carbs
                    onCarbsChange={props.onCarbsChange}
                    editable={false}
                    carbs={props.carbs}
                  />
                  <Protein
                    onProteinChange={props.onProteinChange}
                    editable={false}
                    protein={props.protein}
                  />
                  <Fiber
                    onFiberChange={props.onFiberChange}
                    editable={false}
                    fiber={props.fiber}
                  />
                  <Sugar
                    onSugarChange={props.onSugarChange}
                    editable={false}
                    sugar={props.sugar}
                  />
                  <Sodium
                    onSodiumChange={props.onSodiumChange}
                    editable={false}
                    sodium={props.sodium}
                  />
                  <Fat
                    onFatChange={props.onFatChange}
                    editable={false}
                    fat={props.fat}
                  />
                  <SaturatedFat
                    onSaturatedFatChange={props.onSaturatedFatChange}
                    editable={false}
                    saturatedFat={props.saturatedFat}
                  />
                  <TransFat
                    onTransFatChange={props.onTransFatChange}
                    editable={false}
                    transFat={props.transFat}
                  />
                  <Cholesterol
                    onCholesterolChange={props.onCholesterolChange}
                    editable={false}
                    cholesterol={props.cholesterol}
                  />

                </div>
              : ''}

            {props.isOpen
              ? <Modal
                  style={{maxWidth: '780px', height: '100%'}}
                  title={
                    props.attributes.id
                      ? `Edit ${props.title}`
                      : 'Create Recipe'
                  }
                  shouldCloseOnClickOutside={false}
                  shouldCloseOnEsc={false}
                  isDismissable={false}
                  onRequestClose={() => props.setState ({isOpen: false})}
                >
                  <div
                    dangerouslySetInnerHTML={{
                      __html: props.promos.author,
                    }}
                  />
                  <TitleAndImage
                    recipeId={props.attributes.id}
                    title={props.title}
                    onTitleChange={props.onTitleChange}
                    isTitleEditable={true}
                    isImageEditable={false}
                    onImageChange={props.onImageChange}
                    imageUrl={props.imageUrl}
                    isFeaturedPostImage={props.isFeaturedPostImage}
                  />
                  <Ingredients
                    ingredients={props.ingredients}
                    onIngredientsChange={props.onIngredientsChange}
                    onIngredientsPaste={props.onIngredientsPaste}
                    editable={true}
                  />
                  <Instructions
                    editable={true}
                    onInstructionsChange={props.onInstructionsChange}
                    instructions={props.instructions}
                    onInstructionsPaste={props.onInstructionsPaste}
                  />
                  <Author
                    editable={true}
                    onChange={props.onAuthorChange}
                    authors={props.settings.authors}
                    selectedAuthor={
                      props.author
                        ? props.author
                        : props.settings.default_author
                    }
                  />
                  <CategoryAndCuisine
                    editable={true}
                    onCategoryChange={props.onCategoryChange}
                    category={props.category}
                    onCuisineChange={props.onCuisineChange}
                    cuisine={props.cuisine}
                  />
                  <Description
                    editable={true}
                    onDescriptionChange={props.onDescriptionChange}
                    description={props.description}
                  />
                  <PrepAndCookTime
                    editable={true}
                    onPrepTimeHoursChange={props.onPrepTimeHoursChange}
                    onPrepTimeMinutesChange={props.onPrepTimeMinutesChange}
                    onCookTimeHoursChange={props.onCookTimeHoursChange}
                    onCookTimeMinutesChange={props.onCookTimeMinutesChange}
                    cookTimeHours={props.cookTimeHours}
                    cookTimeMinutes={props.cookTimeMinutes}
                    prepTimeHours={props.prepTimeHours}
                    prepTimeMinutes={props.prepTimeMinutes}
                  />
                  <Notes
                    onNotesChange={props.onNotesChange}
                    notes={props.notes}
                    editable={true}
                  />
                  <ServingsAndSize
                    onServingsChange={props.onServingsChange}
                    servings={props.servings}
                    onServingSizeChange={props.onServingSizeChange}
                    editable={true}
                    servingSize={props.servingSize}
                  />
                  <div className="zrdn-columns zrdn-is-mobile zrdn-is-centered">
                    {props.nutritionCalculationError
                      ? <span
                          style={{fontSize: '0.75em'}}
                          className="zrdn-help zrdn-is-danger"
                          dangerouslySetInnerHTML={{
                            __html: props.nutritionCalculationError,
                          }}
                        />
                      : ''}
                  </div>
                  <div className="zrdn-columns zrdn-is-mobile zrdn-is-centered">
                    <div className="zrdn-buttons">
                      {props.showNutritionFields
                        ? ''
                        : <button
                            onClick={() => {
                              props.setState ({showNutritionFields: true});
                            }}
                            className="zrdn-button zrdn-is-white"
                          >
                            Enter Nutrition Data Manually
                          </button>}
                      <button
                        onClick={props.onCalculateNutrition.bind (
                          null,
                          props.setState
                        )}
                        className={calculateButtonClasses}
                      >
                        Automatically Calculate Nutrition
                      </button>
                      {props.showNutritionPromo
                        ? <div
                            dangerouslySetInnerHTML={{
                              __html: props.promos.nutrition,
                            }}
                          />
                        : ''}
                    </div>
                  </div>
                  {props.showNutritionFields || props.nutritionLabelUrl
                    ? <div>
                        {props.nutritionLabelUrl
                          ? <div style={{marginBottom: '10px'}}>
                              <img
                                style={{width: '30px', verticalAlign: 'bottom'}}
                                src={props.settings.success_icon_url}
                              />
                              Nutrition data has been calculated. Nutrition label is attached.
                            </div>
                          : ''}
                        <Calories
                          onCaloriesChange={props.onCaloriesChange}
                          editable={true}
                          calories={props.calories}
                        />
                        <Carbs
                          onCarbsChange={props.onCarbsChange}
                          editable={true}
                          carbs={props.carbs}
                        />
                        <Protein
                          onProteinChange={props.onProteinChange}
                          editable={true}
                          protein={props.protein}
                        />
                        <Fiber
                          onFiberChange={props.onFiberChange}
                          editable={true}
                          fiber={props.fiber}
                        />
                        <Sugar
                          onSugarChange={props.onSugarChange}
                          editable={true}
                          sugar={props.sugar}
                        />
                        <Sodium
                          onSodiumChange={props.onSodiumChange}
                          editable={true}
                          sodium={props.sodium}
                        />
                        <Fat
                          onFatChange={props.onFatChange}
                          editable={true}
                          fat={props.fat}
                        />
                        <SaturatedFat
                          onSaturatedFatChange={props.onSaturatedFatChange}
                          editable={true}
                          saturatedFat={props.saturatedFat}
                        />
                        <TransFat
                          onTransFatChange={props.onTransFatChange}
                          editable={true}
                          transFat={props.transFat}
                        />
                        <Cholesterol
                          onCholesterolChange={props.onCholesterolChange}
                          editable={true}
                          cholesterol={props.cholesterol}
                        />
                      </div>
                    : ''}

                  <div className="bottom-bar">
                    <Button
                      isDefault
                      onClick={props.onCancel.bind (null, props.setState)}
                    >
                      Cancel
                    </Button>

                    <Button
                      isPrimary
                      isLarge
                      isBusy={props.isSaving}
                      onClick={props.onSave.bind (
                        null,
                        props.setAttributes,
                        props.setState,
                        props.attributes.id
                      )}
                    >
                      {props.attributes.id ? 'Update Recipe' : 'Save Recipe'}
                    </Button>
                  </div>
                </Modal>
              : ''}
          </div>
        );
      })
    )
  ),
  save: function (props) {
    return null;
  },
});
