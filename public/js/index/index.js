var baseUrl = '';

$('document').ready(function() {
    
    baseUrl = $('#baseUrl').val();
    
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
        
        $.get(
            baseUrl + '/index/validate', 
            $(this).serialize(),
            function(data) {
                if (data.rc == 1) {
                    var wcagResult = $('#wcagResult');
                    var headerResult = $('#headerResult');
                    
                    wcagResult.removeClass();
                    headerResult.removeClass();
                    
                    if (data.isValidColorContrast == 'yes') {
                        wcagResult.html('<b>Yes!</b>  These color combinations meet the WCAG2 AA specification.');
                        wcagResult.addClass('pass');
                    } else {
                        wcagResult.html('<b>No!</b>  These color combinations fail to meet the WCAG2 AA specification.');
                        wcagResult.addClass('fail');
                    }
                    
                    if (data.isValidWebsiteHeader == 'yes') {
                        headerResult.html('<b>Yes!</b>  These color combinations are allowed to be used as website headers.');
                        headerResult.addClass('pass');
                    } else {
                        headerResult.html('<b>No!</b>  These color combinations are not allowed to be used as website headers.');
                        headerResult.addClass('fail');
                    }                    
                }
            },
            "json"
        );
        
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