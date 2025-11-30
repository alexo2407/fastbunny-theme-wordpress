<?php
function fast_bunny_cargo_scripts() {
    // Encolar estilos correctamente
    wp_enqueue_style( 'bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css', array(), '4.1.0' );
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Montserrat:300,400,700,900', array(), null );
    wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css') );

    // Encolar scripts correctamente
    wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), null, true );
    wp_enqueue_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js', array(), null, true );
    wp_enqueue_script( 'bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js', array(), null, true );
        // Cargar Bootstrap Bundle JS (incluye Popper)
        wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), '5.3.0', true );

    // wp_enqueue_script( 'jquery-countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), null, true );
    //  wp_enqueue_script( 'app-js', get_template_directory_uri() . '/js/app.js', array('jquery'), null, true );
}
add_action( 'wp_enqueue_scripts', 'fast_bunny_cargo_scripts' );

function agregar_favicon_links() {
    $theme_uri = get_template_directory_uri();
    echo '<link rel="apple-touch-icon" sizes="180x180" href="' . $theme_uri . '/favicon_io/apple-touch-icon.png">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . $theme_uri . '/favicon_io/favicon-32x32.png">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="16x16" href="' . $theme_uri . '/favicon_io/favicon-16x16.png">' . "\n";
    echo '<link rel="manifest" href="' . $theme_uri . '/favicon_io/site.webmanifest">' . "\n";
}
add_action('wp_head', 'agregar_favicon_links');


function agregar_estilos_personalizados() {
    ?>
    <style>
      

    </style>
    <?php
}
add_action('wp_head', 'agregar_estilos_personalizados');

function enqueue_ajax_tracking_script() {
    wp_enqueue_script(
        'tracking-script',
        get_template_directory_uri() . '/js/init.js', // Ruta del archivo
        array('jquery'), // Depende de jQuery
        null,
        true // Cargar en el footer
    );

    // Pasar la URL de admin-ajax.php a JavaScript
    wp_localize_script('tracking-script', 'tracking_ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_tracking_script');

function get_tracking_info() {
    if (!isset($_POST['trackingNumber'])) {
        echo json_encode(["error" => "Número de guía no proporcionado."]);
        wp_die();
    }

    $tracking_number = sanitize_text_field($_POST['trackingNumber']);
    $url = "https://portalvolexpress.com/frmtrack.aspx";

    // 1. Realizar petición GET para obtener cookies y valores de sesión (ViewState)
    $response_get = wp_remote_get($url, array('sslverify' => false));

    if (is_wp_error($response_get)) {
        echo json_encode(["error" => "Error al conectar con el servicio de rastreo (Paso 1)."]);
        wp_die();
    }

    $body_get = wp_remote_retrieve_body($response_get);
    $cookies = wp_remote_retrieve_cookies($response_get);

    // 2. Extraer valores ocultos necesarios para ASP.NET (__VIEWSTATE, etc.)
    $viewstate = '';
    $viewstategenerator = '';
    $eventvalidation = '';

    if (preg_match('/id="__VIEWSTATE" value="(.*?)"/', $body_get, $matches)) {
        $viewstate = $matches[1];
    }
    if (preg_match('/id="__VIEWSTATEGENERATOR" value="(.*?)"/', $body_get, $matches)) {
        $viewstategenerator = $matches[1];
    }
    if (preg_match('/id="__EVENTVALIDATION" value="(.*?)"/', $body_get, $matches)) {
        $eventvalidation = $matches[1];
    }

    // 3. Realizar petición POST con los datos del formulario y cookies
    $args = array(
        'body' => array(
            '__VIEWSTATE' => $viewstate,
            '__VIEWSTATEGENERATOR' => $viewstategenerator,
            '__EVENTVALIDATION' => $eventvalidation,
            'txTracking' => $tracking_number, // Nombre del input en el nuevo sitio
            'btBuscar' => 'Buscar'            // Nombre del botón submit
        ),
        'method' => 'POST',
        'cookies' => $cookies, // Importante: mantener la sesión
        'sslverify' => false,
        'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36' 
        )
    );

    $response_post = wp_remote_post($url, $args);

    if (is_wp_error($response_post)) {
        echo json_encode(["error" => "Error al obtener la información de rastreo (Paso 2)."]);
        wp_die();
    }

    $body_post = wp_remote_retrieve_body($response_post);

    // 4. Procesar el HTML de respuesta
    // Buscamos las tablas o el contenedor de resultados. 
    // En este sitio parece que los resultados están dentro de divs con clase "GridDock" o tablas generadas.
    
    // Extraer tablas
    preg_match_all('/<table.*?>.*?<\/table>/s', $body_post, $tables);

    $filtered_html = "";
    
    if (!empty($tables[0])) {
        foreach ($tables[0] as $table) {
            // Filtrar tablas vacías o irrelevantes si es necesario
            // Por ahora concatenamos todas las tablas encontradas que tengan contenido visible
            if (strip_tags($table) != "") {
                 $filtered_html .= $table;
            }
        }
    } else {
        // Si no hay tablas, intentamos buscar mensajes de error o contenedores específicos
        // A veces ASP.NET devuelve el error en un span o div
        if (strpos($body_post, 'No se encontraron registros') !== false) {
             echo json_encode(["error" => "No se encontraron registros para este número."]);
             wp_die();
        }
    }

    if (empty($filtered_html)) {
        // Fallback: Si no encontramos tablas, devolver un mensaje genérico o parte del body para debug
        // echo json_encode(["html" => "No se pudo interpretar la respuesta del servidor externo."]);
        // Para depuración, podríamos devolver una parte del body:
        // echo json_encode(["html" => substr(strip_tags($body_post), 0, 500)]);
        echo json_encode(["error" => "No se encontraron datos de rastreo. Verifique el número."]);
        wp_die();
    }

    // 5. Limpieza y corrección de estilos/imágenes
    // Convertir rutas relativas a absolutas para que se vean bien
    $base_url = "https://portalvolexpress.com/";
    $filtered_html = str_replace('src="', 'src="' . $base_url, $filtered_html);
    $filtered_html = str_replace('href="', 'href="' . $base_url, $filtered_html);
    
    // Corregir dobles protocolos si ocurren (ej: https://portalvolexpress.com/http://...)
    $filtered_html = str_replace($base_url . 'http', 'http', $filtered_html);
    $filtered_html = str_replace($base_url . 'https', 'https', $filtered_html);

    // Opcional: Inyectar algo de CSS para que la tabla se vea bien en el modal de Bootstrap
    $custom_css = '<style>table { width: 100%; max-width: 100%; margin-bottom: 1rem; background-color: transparent; } th, td { padding: 0.75rem; vertical-align: top; border-top: 1px solid #dee2e6; } </style>';

    echo json_encode(["html" => $custom_css . $filtered_html]);
    wp_die();
}


// 📌 Agregar la acción de AJAX en WordPress
add_action('wp_ajax_get_tracking_info', 'get_tracking_info');
add_action('wp_ajax_nopriv_get_tracking_info', 'get_tracking_info');

