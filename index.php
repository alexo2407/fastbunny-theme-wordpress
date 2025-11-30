<?php get_header(); ?>

<div class="embed-responsive embed-responsive-16by9">
  <video class="embed-responsive-item" src="<?php echo get_template_directory_uri(); ?>/video/cargo.mp4" autoplay loop muted></video>

  <div class="container">
    <div class="informacion-concierto text-light">
      <div class="contenido-hero contenedor">
        <h1>Rastrea tu paquetería aquí!</h1>
        <form action="#" id="trackingForm">
          <div class="input-container">
            <img src="<?php echo get_template_directory_uri(); ?>/img/lupa.png" class="search-icon">
            <input type="text" id="trackingNumber" name="trackingNumber" class="busqueda" placeholder="Track id">
            <button type="submit" class="btn-buscar">Buscar</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Aumenta el ancho del modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="trackingModalLabel">Resultado de Rastreo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="trackingResult">
        <p>Cargando datos de rastreo...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>




<main class="contenido-principal mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <img src="<?php echo get_template_directory_uri(); ?>/img/quienes-somos.jpg" class="img-fluid" alt="Imagen representativa">
      </div>
      <div class="col-md-6 mt-5 mt-md-0 sobre-festival align-items-center d-flex">
        <div class="contenido">
          <h2>Fast Bunny <span class="bg-light text-dark"> Cargo </span></h2>
          <p>
            Somos una empresa especializada en compras internacionales en Estados Unidos y China, ofreciendo soluciones de transporte tanto aéreo como marítimo para satisfacer las necesidades de sus clientes. Su enfoque está en brindar un servicio rápido, seguro y confiable para el envío de productos desde estos países hacia Nicaragua.
          </p>
        </div>
      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>