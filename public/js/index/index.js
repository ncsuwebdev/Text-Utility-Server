$('document').ready(function() {
    
    $.fn.colorPicker.defaultColors = $.parseJSON($('textarea#initialColors').val());
    
    $('#backgroundColorSelector, #fontColorSelector').colorPicker();
    
    $('#backgroundColorSelector, #fontColorSelector').attr('name', '');
    
    $('form#logoGenerator').submit(function() {

        $('#fontColor').val(mapColor($('#fontColorSelector').val()));
        $('#backgroundColor').val(mapColor($('#backgroundColorSelector').val()));
        
        var url = $('#apiUrl').val() + '?' + $(this).serialize();

        $('#imgPreview').remove();
        $('#imgUrl').attr('href', url).html(url);
        
        $('#preview').prepend('<img id="imgPreview" src="' + url + '" alt="University Logo"/>');
        
        return false;
    }).submit(); 

    $('#resetButton').click(function() {
        $('form#logoGenerator').get(0).reset();
        $('form#logoGenerator').submit();
    });
});

function mapColor(hexColor) {    
    var colorMap = $.parseJSON($('textarea#initialColors').val());

    hexColor = hexColor.replace(/#/, "").toUpperCase();
    
    var colorKey = '';
    
    $.each(colorMap, function(key, val) {
        if (val == hexColor) {
            colorKey = key;
        }
    });
    
    return colorKey;
}