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