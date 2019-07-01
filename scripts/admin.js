
function zrdnAddImageHandler(selectImageHandlerFunction)
{
    var image = wp.media({
        title: 'Add Image',
        multiple: false
    }).open().on('select', function()
    {
        var uploaded_image = image.state().get('selection').first();
        // We convert uploaded_image to a JSON object to make accessing it easier
        var imageData = uploaded_image.toJSON();
        selectImageHandlerFunction(imageData);
    });
}