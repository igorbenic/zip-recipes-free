const {
  RichText,
  PlainText,
  MediaUpload,
  MediaUploadCheck,
  InspectorControls,
  BlockControls,
  AlignmentToolbar,
} = window.wp.editor;

const {Button, Modal} = window.wp.components;

const TitleAndImage = props => {
  return (
    <div className={props.className}>
      {/* Title and image start --> */}
      <div className="zrdn-columns zrdn-is-mobile">

        <div className="zrdn-column zrdn-is-three-quarters-tablet zrdn-is-two-thirds-mobile">
          <div className="zrdn-field">
            {props.isTitleEditable
              ? <label htmlFor="recipe-title" className="zrdn-label">
                  Title
                </label>
              : ''}
            <div className="zrdn-control" id="title-container">
              {props.isTitleEditable
                ? <input
                    id="recipe-title"
                    name="recipe-title"
                    className="zrdn-input"
                    type="text"
                    value={props.title}
                    onChange={props.onTitleChange}
                    placeholder={'Recipe Title…'}
                  />
                : <h2>{props.title}</h2>}
            </div>
          </div>
        </div>
        <div className="zrdn-column">
          <label className="zrdn-label">Image</label>
          <div className="recipe-image">
            {/* We show image upload control only when editable is false since we cannot show image upload control in 
                in Modal window and editable=true only in modal window.
                See: https://github.com/WordPress/gutenberg/issues/12830
               */}
            {props.isImageEditable
              ? <MediaUploadCheck>
                  <MediaUpload
                    onSelect={props.onImageChange.bind (null, props.recipeId)}
                    allowedTypes="image"
                    render={({open}) =>
                      props.imageUrl
                        ? <div>
                            {props.isFeaturedPostImage
                              ? <span>
                                  Set from Featured Image.
                                </span>
                              : <div>
                                  <Button onClick={open}>
                                    <img
                                      src={props.imageUrl}
                                      alt={'Upload Recipe Image'}
                                    />
                                  </Button>
                                  <span>
                                    Click the image to change it.
                                  </span>
                                </div>}
                          </div>
                        : <Button isDefault={true} onClick={open}>
                            Upload Image
                          </Button>}
                  />
                </MediaUploadCheck>
              : 'To add an image, save this recipe and click Upload Image from the main screen.'}
          </div>
        </div>
      </div>
      {/* Title and image end --> */}
    </div>
  );
};

const Ingredients = props => (
  <div id="ingredients-container" className="zrdn-field">
    <label className="zrdn-label" htmlFor="ingredients">
      Ingredients
    </label>
    {props.editable
      ? <p className="zrdn-help">
          Put each ingredient on a separate line. There is no need to
          use bullets for your ingredients.
          <br />
          You can also create labels, hyperlinks, bold/italic effects
          and even add images!
          <br />
          <a href="https://www.ziprecipes.net/docs/installing/" target="_blank">
            Learn how here
          </a>
        </p>
      : ''}
    <div className="zrdn-control">
      {props.editable
        ? <textarea
            className="zrdn-textarea clean-on-paste"
            name="ingredients"
            onChange={props.onIngredientsChange}
            onPaste={props.onIngredientsPaste}
            id="ingredients"
            value={props.ingredients.join ('\n')}
          />
        : <div>
            {props.ingredients.map (ing => {
              return <div>{ing}</div>;
            })}
          </div>}
    </div>
  </div>
);
const Instructions = props => (
  <div className="zrdn-field">
    <label className="zrdn-label" htmlFor="instructions">
      Instructions
    </label>
    {props.editable
      ? <p className="zrdn-help">
          Press return after each instruction. There is no need to
          number your instructions.
          <br />
          You can also create labels, hyperlinks, bold/italic effects
          and even add images!
          <br />
          <a href="https://www.ziprecipes.net/docs/installing/" target="_blank">
            Learn how here
          </a>
        </p>
      : ''}
    <div className="zrdn-control">
      {props.editable
        ? <textarea
            className="zrdn-textarea clean-on-paste"
            id="instructions"
            onChange={props.onInstructionsChange}
            name="instructions"
            onPaste={props.onInstructionsPaste}
            value={props.instructions.join ('\n')}
          />
        : <div>
            {props.instructions.map (inst => {
              return <div>{inst}</div>;
            })}
          </div>}

    </div>
  </div>
);
const CategoryAndCuisine = props => (
  <div className="zrdn-columns zrdn-is-mobile">
    <div className="zrdn-column">
      <div className="zrdn-field">
        <label htmlFor="category" className="zrdn-label">
          Category
        </label>
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                id="category"
                onChange={props.onCategoryChange}
                placeholder="e.g. appetizer, entree, etc."
                type="text"
                name="category"
                value={props.category}
              />
            : props.category}
        </div>
      </div>
    </div>
    <div className="zrdn-column">
      <div className="zrdn-field">
        <label htmlFor="cuisine" className="zrdn-label">
          Cuisine
        </label>
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                placeholder="e.g. French, Ethiopian, etc."
                onChange={props.onCuisineChange}
                type="text"
                id="cuisine"
                name="cuisine"
                value={props.cuisine}
              />
            : props.cuisine}
        </div>
      </div>
    </div>
  </div>
);
const Description = props => (
  <div className="zrdn-field">
    <label className="zrdn-label" htmlFor="summary">
      Description
    </label>
    <div className="zrdn-control">
      {props.editable
        ? <textarea
            className="zrdn-textarea"
            id="summary"
            name="summary"
            onChange={props.onDescriptionChange}
            value={props.description}
          />
        : props.description}
    </div>
  </div>
);
const PrepAndCookTime = props => (
  <div className="zrdn-columns zrdn-is-tablet">
    <div className="zrdn-column">
      <label htmlFor="prep_hours" className="zrdn-label">
        Prep Time
      </label>
      {props.editable
        ? <div className="zrdn-field zrdn-is-grouped">
            <div>
              <input
                className="zrdn-input zrdn-is-small"
                type="number"
                min="0"
                id="prep_hours"
                onChange={props.onPrepTimeHoursChange}
                name="prep_time_hours"
                value={props.prepTimeHours}
              />
              hours
            </div>
            <div>
              <input
                className="zrdn-input zrdn-is-small"
                type="number"
                min="0"
                id="prep_minutes"
                onChange={props.onPrepTimeMinutesChange}
                name="prep_time_minutes"
                value={props.prepTimeMinutes}
              />
              minutes
            </div>
          </div>
        : <div className="zrdn-control">
            {props.prepTimeHours}:{props.prepTimeMinutes}
          </div>}
    </div>
    <div className="zrdn-column">
      <label htmlFor="cook_hours" className="zrdn-label">
        Cook Time
      </label>
      {props.editable
        ? <div className="zrdn-field zrdn-is-grouped">
            <div>
              <input
                className="zrdn-input zrdn-is-small"
                type="number"
                min="0"
                onChange={props.onCookTimeHoursChange}
                id="cook_hours"
                name="cook_time_hours"
                value={props.cookTimeHours}
              />
              hours
            </div>
            <div>
              <input
                className="zrdn-input zrdn-is-small"
                type="number"
                min="0"
                id="cook_minutes"
                onChange={props.onCookTimeMinutesChange}
                name="cook_time_minutes"
                value={props.cookTimeMinutes}
              />
              minutes
            </div>
          </div>
        : <div className="zrdn-control">
            {props.cookTimeHours}:{props.cookTimeMinutes}
          </div>}
    </div>
  </div>
);
const Notes = props => (
  <div className="zrdn-field">
    <label className="zrdn-label" htmlFor="notes">
      Notes
    </label>
    <div className="zrdn-control">
      {props.editable
        ? <textarea
            className="zrdn-textarea"
            id="notes"
            name="notes"
            onChange={props.onNotesChange}
            value={props.notes}
          />
        : props.notes}
    </div>
  </div>
);
const ServingsAndSize = props => (
    <div className="zrdn-columns zrdn-is-mobile">
    <div className="zrdn-column">
      <div className="zrdn-field">
        <label htmlFor="servings" className="zrdn-label">
          Yields
        </label>
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                id="servings"
                onChange={props.onServingsChange}
                placeholder="e.g. 8 people"
                type="text"
                name="servings"
                value={props.servings}
              />
            : props.servings}
        </div>
      </div>
    </div>
    <div className="zrdn-column">
      <div className="zrdn-field">
        <label htmlFor="servingSize" className="zrdn-label">
          Serving Size
        </label>
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                placeholder="1 slice"
                onChange={props.onServingSizeChange}
                type="text"
                id="servingSize"
                name="servingSize"
                value={props.servingSize}
              />
            : props.servingSize}
        </div>
      </div>
    </div>
  </div>
);

const Calories = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="calories">Calories</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="calories"
                name="calories"
                onChange={props.onCaloriesChange}
                value={props.calories}
              />
            : props.calories}
        </div>
      </div>
    </div>
  </div>
);

const Carbs = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="carbs">Carbs</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="carbs"
                name="carbs"
                onChange={props.onCarbsChange}
                value={props.carbs}
              />
            : props.carbs}
        </div>
      </div>
    </div>
  </div>
);

const Protein = props => (
  <div className="zrdn-field zrdn-is-horizontal zrdn-is-mobile">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="protein">Protein</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="protein"
                name="protein"
                onChange={props.onProteinChange}
                value={props.protein}
              />
            : props.protein}
        </div>
      </div>
    </div>
  </div>
);

const Fiber = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="fiber">Fiber</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="fiber"
                name="fiber"
                onChange={props.onFiberChange}
                value={props.fiber}
              />
            : props.fiber}
        </div>
      </div>
    </div>
  </div>
);

const Sugar = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="sugar">Sugar</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="sugar"
                name="sugar"
                onChange={props.onSugarChange}
                value={props.sugar}
              />
            : props.sugar}
        </div>
      </div>
    </div>
  </div>
);

const Sodium = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="sodium">Sodium</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="sodium"
                name="sodium"
                onChange={props.onSodiumChange}
                value={props.sodium}
              />
            : props.sodium}
        </div>
      </div>
    </div>
  </div>
);
const Fat = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="fat">Fat</label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="fat"
                name="fat"
                onChange={props.onFatChange}
                value={props.fat}
              />
            : props.fat}
        </div>
      </div>
    </div>
  </div>
);
const SaturatedFat = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="saturated_fat">
        Saturated Fat
      </label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="saturated_fat"
                name="saturated_fat"
                onChange={props.onSaturatedFatChange}
                value={props.saturatedFat}
              />
            : props.saturatedFat}
        </div>
      </div>
    </div>
  </div>
);

const TransFat = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="trans_fat">
        Trans. Fat
      </label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="trans_fat"
                name="trans_fat"
                onChange={props.onTransFatChange}
                value={props.transFat}
              />
            : props.transFat}
        </div>
      </div>
    </div>
  </div>
);
const Cholesterol = props => (
  <div className="zrdn-field zrdn-is-horizontal">
    <div className="zrdn-field-label">
      <label className="zrdn-label" for="cholesterol">
        Cholesterol
      </label>
    </div>
    <div className="zrdn-field-body">
      <div className="zrdn-field zrdn-is-narrow">
        <div className="zrdn-control">
          {props.editable
            ? <input
                className="zrdn-input zrdn-is-small"
                type="text"
                id="cholesterol"
                name="cholesterol"
                onChange={props.onCholesterolChange}
                value={props.cholesterol}
              />
            : props.cholesterol}
        </div>
      </div>
    </div>
  </div>
);

export {
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
};
