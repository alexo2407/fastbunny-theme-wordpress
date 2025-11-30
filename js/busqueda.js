jQuery(document).ready(function ($)
{
//Buscador Ajax
$('.buscar').keyup(function () {
var textobuscar = $('.buscar').val();
    var data = {
        'action': 'get_post_information',
        'textobuscar': textobuscar
    };
    $.post(ajaxurl, data, function(response) {
$('#datafetch').html('<p>'+ response +'</p>');
    });
});
})
