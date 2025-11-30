<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php wp_title('|', true, 'right'); ?></title>


  <!--    Cargando fuentes y estilos-->

  <?php wp_head(); ?>


</head>

<body data-spy="scroll" data-target="#navegacion-principal" data-offset="10">
<nav id="navegacion-principal" class="navbar navbar-expand-lg navbar-light bg-transparent fixed-top">
  <div class="container">
    <a class="navbar-brand text-light" href="<?php echo home_url(); ?>">
      <img src="<?php echo get_template_directory_uri(); ?>/img/1000w/logo fast bunny.png" alt="Logo Fast Bunny" style="width: 200px;">
    </a>
    <!-- Botón para dispositivos pequeños -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" 
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Menú colapsable -->
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="#lineup">Quienes Somos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#galeria">Servicios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#pie">Contacto</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
