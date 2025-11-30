$ = jQuery.noConflict();

// $(document).on("click", '[data-toggle="modal"]', function(event) {
//   event.preventDefault();
//   $(this).ekkoLightbox();
// });
jQuery(document).ready(function ($)
{
//Buscador Ajax
  //console.log(buscadorajax);

  $('#busqueda').keyup(function(e){
    var query = $(this).val();

    if (e.which == 8) {
      var txt = $('#busqueda').val().length;
    //console.log(txt);
        if (txt == 0 ){
          var div = document.getElementById('quitar');
          if(div !== null){
              div.remove();
          }
        }

    }else {
      if (query.length > 2){
        //LLamado Ajax
        $.ajax({
          method: 'post',
          url: buscadorajax.adminAjax,
          data:{
                action: 'encontrarPost',
                query: query
          },
          beforeSend: function() {
            if ( $('.results').length ) {
              $('.results').html('<div class="p-4"><h4>Buscando en este momento..</h4></div>');
            }
            else {
              $('#bloque-buscar').append('<div class="results p-4" id="quitar"><h4>Buscando en este momento..</h4></div>');
            }
          },
          success: function(buscadorajax) {
            var $contenedor = $('.results');

            if (buscadorajax.length) {
              var busquedaMarkup = resultadosBusquedaMarkup(buscadorajax);
              $contenedor.html(busquedaMarkup);
            }
            else {
              $contenedor.html('<div class="p-4"><h4>No encontramos resultados :( </h4></div>');
            }
          }

        });
      }
    }

    });



    function resultadosBusquedaMarkup(buscadorajax){
        var markup = '';
        buscadorajax.forEach( function  (buscador)  {
          markup  +=  '<div class="contenedor-buscador">';
          markup  +=  ' <div class="image">';
          if ( buscador.image == false) {
          markup  +=  '   <a href="'+ buscador.link+'"> - </a>';
          }else {
          markup  +=  '   <a href="'+ buscador.link+'"> <img src="'+ buscador.image+'"></a>';
          }
          markup  +=  '</div>';
          markup  +=  '<div class="content">';
          markup  +=  ' <h4><a href="'+ buscador.link+'">"'+buscador.title+'"</a></h4><br>';
          markup  +=  ' <p><span>Publicado el:</sapn>'+buscador.date+'</p>';
          markup  +=  '</div>';
          markup  +=  '</div>';
        });
        markup  +=  '<div class="mas-busqueda">';
        markup +=  '<input type="submit" value="Mostrar todos los resultados" class="btn btn-warning py-2 text-uppercase">';
        markup +=  '</div>';
        return markup;
    }


});
