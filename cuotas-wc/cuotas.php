<?php
/*
Plugin Name: Planes Bancarios
Plugin URI:  
Description: Este es un plugin para mostrar las cuotas que desees en la pagina del producto y en la card del producto.
Version:     1.0
Author:      Grupo Eon 
Author URI:  http://grupoeon.com.ar
License:     GPL2
*/

// ESTILOS PARA EL PLUGIN 
function load_custom_wp_admin_style()
{
    wp_enqueue_style('custom_wp_admin_css', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('custom_wp_admin_js', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true); // Cargar JS personalizado
}

add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');

// FUNCION PARA EL CALCULO DE CUOTAS 

add_action('woocommerce_after_add_to_cart_button', 'mostrar_cuotas_despues_boton');

function mostrar_cuotas_despues_boton()
{
    global $product; // Accede al producto actual

    // Carga de configuraciones para la primera y segunda promoción bancaria
    $promotions = array(
        'promo1' => array(
            'activar_cuotas' => get_option('activar_cuotas_promo1', 'false'),
            'cant_cuotas' => get_option('cant_cuotas_promo1', 1),
            'text_cuotas' => get_option('text_cuotas_promo1', ''),
            'interes' => get_option('interes_promo1', 0),
            'descuento' => get_option('descuento_promo1', 0),
            'img_cuotas' => get_option('img_cuotas_promo1', ''),
        ),
        'promo2' => array(
            'activar_cuotas' => get_option('activar_cuotas_promo2', 'false'),
            'cant_cuotas' => get_option('cant_cuotas_promo2', 1),
            'text_cuotas' => get_option('text_cuotas_promo2', ''),
            'interes' => get_option('interes_promo2', 0),
            'descuento' => get_option('descuento_promo2', 0),
            'img_cuotas' => get_option('img_cuotas_promo2', ''),
        ),
        'promo3' => array(
            'activar_cuotas' => get_option('activar_cuotas_promo3', 'false'),
            'cant_cuotas' => get_option('cant_cuotas_promo3', 1),
            'text_cuotas' => get_option('text_cuotas_promo3', ''),
            'interes' => get_option('interes_promo3', 0),
            'descuento' => get_option('descuento_promo3', 0),
            'img_cuotas' => get_option('img_cuotas_promo3', ''),
        ),
    );

    foreach ($promotions as $promo_key => $promo) {
        if ($promo['activar_cuotas'] !== 'true') {
            continue; // Si las cuotas no están activadas para esta promoción, saltar
        }

        $cuotas = $promo['cant_cuotas'];
        $text = $promo['text_cuotas'];
        $interes = $promo['interes'];
        $descuento = $promo['descuento'];
        $img_cuotas = $promo['img_cuotas'];
        if ($product->is_type('simple') || $product->is_type('variable')) {
            if ($product->is_type('variable')) {
                // Tomar siempre el precio regular de las variaciones
                $regular_price = (float) $product->get_variation_regular_price(true);
            } else {
                // Tomar siempre el precio regular de productos simples
                $regular_price = (float) $product->get_regular_price();
            }
        
            // Si se aplica un interés
            if (!empty($interes)) {
                $interes_amount = $regular_price * ($interes / 100);
                $regular_price = $regular_price + $interes_amount;
            }
        
            // Si se aplica un descuento
            if (!empty($descuento)) {
                $descuento_amount = $regular_price * ($descuento / 100);
                $regular_price = $regular_price - $descuento_amount;
            }
        
            // Calcular el precio por cuota basado en el precio regular
            $cuotapreciosale = round($regular_price / $cuotas, 2);
            $cuotapreciosale_formatted = number_format($cuotapreciosale, 2, ',', '.');
        
            $img_html = !empty($img_cuotas) ? '<div class="img-log-cuotas" style="width:24px;"><img class="img-cuot" src="' . esc_url($img_cuotas) . '" alt="Imagen de cuotas" style="max-width: 50px;width:100%"></div>' : '';
        
            echo sprintf(
                __('<div class="config-cuotas-%s" style="display: flex; justify-content: center; margin-top: 20px; align-items: center;">%s<span class="texto-cuotas" style="font-size: 15px; color: black;"> %s <span class="cuota-precio"><span class="inner-precio">$%s</span></span></span></div>', 'woocommerce'),
                esc_attr($promo_key),
                $img_html,
                esc_html($text),
                esc_html($cuotapreciosale_formatted)
            );
        }
    }
}

function add_git_commit_page()
{
    add_menu_page(
        'Configuracion de cuotas',
        'Configurar cuotas',
        'manage_options',
        'config-cuotas-page',
        'render_custom_settings_page'
    );
}

add_action('admin_menu', 'add_git_commit_page');

// Page Config
function render_custom_settings_page()
{

    // Procesar el formulario para la primera promoción bancaria
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
    }    // Procesar el formulario para la segunda promoción bancaria

    if (isset($_POST['promo2_interes'])) {
        update_option('interes_promo2', floatval($_POST['promo2_interes']));
        echo '<div class="updated"><p>Interés para Promo 2 guardado!</p></div>';
    }
    if (isset($_POST['promo2_descuento'])) {
        update_option('descuento_promo2', floatval($_POST['promo2_descuento']));
        echo '<div class="updated"><p>Descuento para Promo 2 guardado!</p></div>';
    }
    if (isset($_POST['promo2_cant_cuotas'])) {
        update_option('cant_cuotas_promo2', intval($_POST['promo2_cant_cuotas']));
        echo '<div class="updated"><p>Cantidad de cuotas para Promo 2 guardada!</p></div>';
    }
    if (isset($_POST['promo2_text_cuotas'])) {
        update_option('text_cuotas_promo2', $_POST['promo2_text_cuotas']);
        echo '<div class="updated"><p>Texto de cuotas para Promo 2 guardado!</p></div>';
    }
    if (isset($_POST['promo2_cuotas_status'])) {
        update_option('activar_cuotas_promo2', $_POST['promo2_cuotas_status'] === 'activar' ? 'true' : 'false');
        echo '<div class="updated"><p>Configuración de cuotas para Promo 2 guardada!</p></div>';
    }
    if (isset($_POST['promo2_delete_img_cuotas']) && $_POST['promo2_delete_img_cuotas'] == 1) {
        $img_cuotas = get_option('img_cuotas_promo2');
        if (!empty($img_cuotas)) {
            $upload_dir = wp_upload_dir();
            $img_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $img_cuotas);
            if (file_exists($img_path)) {
                unlink($img_path);
                update_option('img_cuotas_promo2', '');
                echo '<div class="updated"><p>Imagen de Promo 2 eliminada correctamente.</p></div>';
            } else {
                echo '<div class="error"><p>La imagen de Promo 2 no se pudo encontrar.</p></div>';
            }
        }
    }
    if (isset($_FILES['promo2_img_cuotas']) && !empty($_FILES['promo2_img_cuotas']['name'])) {
        $uploaded_file = $_FILES['promo2_img_cuotas'];
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
                update_option('img_cuotas_promo2', $img_url);
                echo '<div class="updated"><p>Imagen de Promo 2 cargada exitosamente!</p></div>';
            } else {
                echo '<div class="error"><p>Hubo un error al cargar la imagen de Promo 2.</p></div>';
            }
        } else {
            echo '<div class="error"><p>Tipo de archivo no permitido para la imagen de Promo 2.</p></div>';
        }
    }
    // Procesar el formulario para la tercera promoción bancaria
    if (isset($_POST['promo3_interes'])) {
        update_option('interes_promo3', floatval($_POST['promo3_interes']));
        echo '<div class="updated"><p>Interés para Promo 2 guardado!</p></div>';
    }
    if (isset($_POST['promo3_descuento'])) {
        update_option('descuento_promo3', floatval($_POST['promo3_descuento']));
        echo '<div class="updated"><p>Descuento para Promo 2 guardado!</p></div>';
    }
    if (isset($_POST['promo3_cant_cuotas'])) {
        update_option('cant_cuotas_promo3', intval($_POST['promo3_cant_cuotas']));
        echo '<div class="updated"><p>Cantidad de cuotas para Promo 2 guardada!</p></div>';
    }
    if (isset($_POST['promo3_text_cuotas'])) {
        update_option('text_cuotas_promo3', $_POST['promo3_text_cuotas']);
        echo '<div class="updated"><p>Texto de cuotas para Promo 2 guardado!</p></div>';
    }
    if (isset($_POST['promo3_cuotas_status'])) {
        update_option('activar_cuotas_promo3', $_POST['promo3_cuotas_status'] === 'activar' ? 'true' : 'false');
        echo '<div class="updated"><p>Configuración de cuotas para Promo 3 guardada!</p></div>';
    }
    if (isset($_POST['promo3_delete_img_cuotas']) && $_POST['promo3_delete_img_cuotas'] == 1) {
        $img_cuotas = get_option('img_cuotas_promo3');
        if (!empty($img_cuotas)) {
            $upload_dir = wp_upload_dir();
            $img_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $img_cuotas);
            if (file_exists($img_path)) {
                unlink($img_path);
                update_option('img_cuotas_promo3', '');
                echo '<div class="updated"><p>Imagen de Promo 3 eliminada correctamente.</p></div>';
            } else {
                echo '<div class="error"><p>La imagen de Promo 3 no se pudo encontrar.</p></div>';
            }
        }
    }
    if (isset($_FILES['promo3_img_cuotas']) && !empty($_FILES['promo3_img_cuotas']['name'])) {
        $uploaded_file = $_FILES['promo3_img_cuotas'];
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
                update_option('img_cuotas_promo3', $img_url);
                echo '<div class="updated"><p>Imagen de Promo 2 cargada exitosamente!</p></div>';
            } else {
                echo '<div class="error"><p>Hubo un error al cargar la imagen de Promo 3.</p></div>';
            }
        } else {
            echo '<div class="error"><p>Tipo de archivo no permitido para la imagen de Promo 3.</p></div>';
        }
    }
    ?>

    <div class="wrap">
        <h1>Configuración de promociones bancarias</h1>
        <!-- Formulario para la primera promoción bancaria -->
        <section class="formularios">
            <div class="forms-cuotas-banc" >
                <h2>Promoción Bancaria 1</h2>
                <form method="post" enctype="multipart/form-data">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activar Cuotas</th>
                            <td>
                                <select name="promo1_cuotas_status">
                                    <option value="activar" <?php selected(get_option('activar_cuotas_promo1', 'false'), 'true'); ?>>Activar</option>
                                    <option value="desactivar" <?php selected(get_option('activar_cuotas_promo1', 'false'), 'false'); ?>>Desactivar</option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Cantidad de Cuotas</th>
                            <td><input type="number" name="promo1_cant_cuotas"
                                    value="<?php echo esc_attr(get_option('cant_cuotas_promo1', 1)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Texto de Cuotas</th>
                            <td><input type="text" name="promo1_text_cuotas"
                                    value="<?php echo esc_attr(get_option('text_cuotas_promo1', '')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Interés (%)</th>
                            <td><input type="number" step="0.01" name="promo1_interes"
                                    value="<?php echo esc_attr(get_option('interes_promo1', 0)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Descuento (%)</th>
                            <td><input type="number" step="0.01" name="promo1_descuento"
                                    value="<?php echo esc_attr(get_option('descuento_promo1', 0)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Imagen de Cuotas</th>
                            <td>
                                <!-- <input type="file" name="promo1_img_cuotas" /> -->
                                <?php
                                $img_cuotas = get_option('img_cuotas_promo1');
                                if (!empty($img_cuotas)) {
                                    echo '<img src="' . esc_url($img_cuotas) . '" style="max-width: 100px;"/>';
                                    ?>
                                    <p><input type="checkbox" name="promo1_delete_img_cuotas" value="1" /> Eliminar Imagen</p>
                                    <?php
                                } else { ?>

                                    <input type="file" name="promo1_img_cuotas" />
                                    <?php
                                }

                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Guardar Cambios - Promo 1'); ?>
                </form>

            </div>
            <div class="forms-cuotas-banc" >
                <h2>Promoción Bancaria 2</h2>
                <form method="post" enctype="multipart/form-data">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activar Cuotas</th>
                            <td>
                                <select name="promo2_cuotas_status">
                                    <option value="activar" <?php selected(get_option('activar_cuotas_promo2', 'false'), 'true'); ?>>Activar</option>
                                    <option value="desactivar" <?php selected(get_option('activar_cuotas_promo2', 'false'), 'false'); ?>>Desactivar</option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Cantidad de Cuotas</th>
                            <td><input type="number" name="promo2_cant_cuotas"
                                    value="<?php echo esc_attr(get_option('cant_cuotas_promo2', 1)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Texto de Cuotas</th>
                            <td><input type="text" name="promo2_text_cuotas"
                                    value="<?php echo esc_attr(get_option('text_cuotas_promo2', '')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Interés (%)</th>
                            <td><input type="number" step="0.01" name="promo2_interes"
                                    value="<?php echo esc_attr(get_option('interes_promo2', 0)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Descuento (%)</th>
                            <td><input type="number" step="0.01" name="promo2_descuento"
                                    value="<?php echo esc_attr(get_option('descuento_promo2', 0)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Imagen de Cuotas</th>
                            <td>
                                <?php
                                $img_cuotas = get_option('img_cuotas_promo2');
                                if (!empty($img_cuotas)) {
                                    echo '<img src="' . esc_url($img_cuotas) . '" style="max-width: 100px;"/>';
                                    ?>
                                    <p><input type="checkbox" name="promo2_delete_img_cuotas" value="1" /> Eliminar Imagen</p>
                                    <?php
                                } else { ?>

                                    <input type="file" name="promo2_img_cuotas" />
                                    <?php
                                }

                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Guardar Cambios - Promo 2'); ?>
                </form>

            </div>
            <div class="forms-cuotas-banc">
                <h2>Promoción Bancaria 3</h2>
                <form method="post" enctype="multipart/form-data">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activar Cuotas</th>
                            <td>
                                <select name="promo3_cuotas_status">
                                    <option value="activar" <?php selected(get_option('activar_cuotas_promo3', 'false'), 'true'); ?>>Activar</option>
                                    <option value="desactivar" <?php selected(get_option('activar_cuotas_promo3', 'false'), 'false'); ?>>Desactivar</option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Cantidad de Cuotas</th>
                            <td><input type="number" name="promo3_cant_cuotas"
                                    value="<?php echo esc_attr(get_option('cant_cuotas_promo3', 1)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Texto de Cuotas</th>
                            <td><input type="text" name="promo3_text_cuotas"
                                    value="<?php echo esc_attr(get_option('text_cuotas_promo3', '')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Interés (%)</th>
                            <td><input type="number" step="0.01" name="promo3_interes"
                                    value="<?php echo esc_attr(get_option('interes_promo3', 0)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Descuento (%)</th>
                            <td><input type="number" step="0.01" name="promo3_descuento"
                                    value="<?php echo esc_attr(get_option('descuento_promo3', 0)); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Imagen de Cuotas</th>
                            <td>
                                <?php
                                $img_cuotas = get_option('img_cuotas_promo3');
                                if (!empty($img_cuotas)) {
                                    echo '<img src="' . esc_url($img_cuotas) . '" style="max-width: 100px;"/>';
                                    ?>
                                    <p><input type="checkbox" name="promo3_delete_img_cuotas" value="1" /> Eliminar Imagen</p>
                                    <?php
                                } else { ?>

                                    <input type="file" name="promo3_img_cuotas" />
                                    <?php
                                }

                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Guardar Cambios - Promo 3'); ?>
                </form>

            </div>
        </section>

        <!-- Formulario para la segunda promoción bancaria -->
    </div>
    <?php
}