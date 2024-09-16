<?php // POST PARA PRIMER FORMULARIO
    if (isset($_POST['promo1_interes'])) {
        update_option('interes_promo1', floatval($_POST['promo1_interes']));
        echo '<div class="updated"><p>Interés para Promo 1 guardado!</p></div>';
    }
    if (isset($_POST['promo1_descuento'])) {
        update_option('descuento_promo1', floatval($_POST['promo1_descuento']));
        echo '<div class="updated"><p>Descuento para Promo 1 guardado!</p></div>';
    }
    if (isset($_POST['promo1_cant_cuotas'])) {
        update_option('cant_cuotas_promo1', intval($_POST['promo1_cant_cuotas']));
        echo '<div class="updated"><p>Cantidad de cuotas para Promo 1 guardada!</p></div>';
    }
    if (isset($_POST['promo1_text_cuotas'])) {
        update_option('text_cuotas_promo1', $_POST['promo1_text_cuotas']);
        echo '<div class="updated"><p>Texto de cuotas para Promo 1 guardado!</p></div>';
    }
    if (isset($_POST['promo1_cuotas_status'])) {
        update_option('activar_cuotas_promo1', $_POST['promo1_cuotas_status'] === 'activar' ? 'true' : 'false');
        echo '<div class="updated"><p>Configuración de cuotas para Promo 1 guardada!</p></div>';
    }
    if (isset($_POST['promo1_delete_img_cuotas']) && $_POST['promo1_delete_img_cuotas'] == 1) {
        $img_cuotas = get_option('img_cuotas_promo1');
        if (!empty($img_cuotas)) {
            $upload_dir = wp_upload_dir();
            $img_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $img_cuotas);
            if (file_exists($img_path)) {
                unlink($img_path);
                update_option('img_cuotas_promo1', '');
                echo '<div class="updated"><p>Imagen de Promo 1 eliminada correctamente.</p></div>';
            } else {
                echo '<div class="error"><p>La imagen de Promo 1 no se pudo encontrar.</p></div>';
            }
        }
    }
    if (isset($_FILES['promo1_img_cuotas']) && !empty($_FILES['promo1_img_cuotas']['name'])) {
        $uploaded_file = $_FILES['promo1_img_cuotas'];
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/custom_images/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($uploaded_file['name']);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
                $img_url = $upload_dir['baseurl'] . '/custom_images/' . basename($uploaded_file['name']);
                update_option('img_cuotas_promo1', $img_url);
                echo '<div class="updated"><p>Imagen de Promo 1 cargada exitosamente!</p></div>';
            } else {
                echo '<div class="error"><p>Hubo un error al cargar la imagen de Promo 1.</p></div>';
            }
        } else {
            echo '<div class="error"><p>Tipo de archivo no permitido para la imagen de Promo 1.</p></div>';
        }
    }