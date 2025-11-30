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
            'txTracking' => $tracking_number,
            'btBuscar' => 'Buscar'
        ),
        'method' => 'POST',
        'cookies' => $cookies,
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
    $dom = new DOMDocument();
    @$dom->loadHTML($body_post); // Suprimir advertencias de HTML mal formado
    $xpath = new DOMXPath($dom);

    // Buscar todas las tablas
    $tables = $xpath->query('//table');
    $events = [];
    $raw_html = "";

    if ($tables->length > 0) {
        // Asumimos que la tabla de datos es la que tiene más filas o la última relevante
        // En muchos sistemas ASP.NET, la tabla de datos es un GridView
        foreach ($tables as $table) {
            $rows = $xpath->query('.//tr', $table);
            if ($rows->length > 1) { // Debe tener más de 1 fila (header + data)
                
                // Guardamos el HTML por si acaso falla el parsing
                $raw_html .= $dom->saveHTML($table);

                // Intentar parsear las filas
                foreach ($rows as $index => $row) {
                    if ($index === 0) continue; // Saltar encabezado

                    $cols = $xpath->query('.//td', $row);
                    
                    // El usuario reportó 12 columnas:
                    // 0: Código Volid, 1: Fecha Ingreso, 2: Código Casillero, 3: Peso, 4: Nombre Completo
                    // 5: Estado del Paquete, 6: Envío, 7: Contenido, 8: Tracking, 9: Comentario, 10: Manifiesto, 11: Imagen
                    
                    if ($cols->length >= 10) { // Validamos que tenga al menos las columnas principales
                        $event = [
                            'volid' => trim($cols->item(0)->nodeValue),
                            'date' => trim($cols->item(1)->nodeValue),
                            'locker' => trim($cols->item(2)->nodeValue),
                            'weight' => trim($cols->item(3)->nodeValue),
                            'name' => trim($cols->item(4)->nodeValue),
                            'status' => trim($cols->item(5)->nodeValue),
                            'shipping_type' => trim($cols->item(6)->nodeValue), // Envío
                            'content' => trim($cols->item(7)->nodeValue),
                            'tracking' => trim($cols->item(8)->nodeValue),
                            'comment' => trim($cols->item(9)->nodeValue),
                            'manifest' => ($cols->length > 10) ? trim($cols->item(10)->nodeValue) : '',
                            'image_html' => ($cols->length > 11) ? $dom->saveHTML($cols->item(11)) : '' // Guardamos el HTML de la imagen (probablemente un <a> o <img>)
                        ];
                        $events[] = $event;
                    }
                }
            }
        }
    } else {
         if (strpos($body_post, 'No se encontraron registros') !== false) {
             echo json_encode(["error" => "No se encontraron registros para este número."]);
             wp_die();
        }
    }

    if (empty($events) && empty($raw_html)) {
        echo json_encode(["error" => "No se encontraron datos de rastreo. Verifique el número."]);
        wp_die();
    }

    // 5. Devolver JSON estructurado
    echo json_encode([
        "events" => $events, // Array de objetos {date, status, location, details}
        "html" => $raw_html  // Fallback
    ]);
    wp_die();
}


// 📌 Agregar la acción de AJAX en WordPress
add_action('wp_ajax_get_tracking_info', 'get_tracking_info');
add_action('wp_ajax_nopriv_get_tracking_info', 'get_tracking_info');

