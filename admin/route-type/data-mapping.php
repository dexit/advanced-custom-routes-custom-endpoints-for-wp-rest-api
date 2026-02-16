<?php

//Creating Data Mapping - Meta box
function cd_acr_add_data_mapping_meta_box() {
    add_meta_box(
        'cd_acr_data_mapping', // $id
        'Data Mapping (Incoming -> Post Meta)', // $title
        'cd_acr_show_data_mapping_meta_box', // $callback
        'cd-custom-rest-api', // $screen
        'normal', // $context
        'high' // $priority
    );
}

add_action( 'add_meta_boxes', 'cd_acr_add_data_mapping_meta_box' );


function cd_acr_show_data_mapping_meta_box( $post ) {
    $mapping = get_post_meta($post->ID, 'cd_acr_data_mapping', true);
    if (!is_array($mapping)) $mapping = array();

    ?>
    <div class="cd_acr_data_mapping_container">
        <p>Map incoming request data or external API response data to WordPress Post Meta fields.
           Use merge tags like <code>{{ request.body.id }}</code> or <code>{{ response.body.id }}</code> in the "Value" field.</p>

        <table id="cd-acr-mapping-table" width="100%">
            <thead>
                <tr>
                    <th align="left">Meta Key</th>
                    <th align="left">Value (supports Merge Tags)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($mapping)) : ?>
                    <?php foreach ($mapping as $index => $item) : ?>
                        <tr>
                            <td><input type="text" name="cd_acr_map_key[]" value="<?php echo esc_attr($item['key']); ?>" style="width:95%;"></td>
                            <td><input type="text" name="cd_acr_map_val[]" value="<?php echo esc_attr($item['val']); ?>" style="width:95%;"></td>
                            <td><a class="button remove-mapping" href="#">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td><input type="text" name="cd_acr_map_key[]" value="" style="width:95%;" placeholder="e.g. customer_id"></td>
                        <td><input type="text" name="cd_acr_map_val[]" value="" style="width:95%;" placeholder="e.g. {{ request.body.customer_id }}"></td>
                        <td><a class="button remove-mapping" href="#">Remove</a></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p><a id="add-mapping" class="button" href="#">Add Mapping</a></p>

        <hr>
        <p>
            <label><strong>Target Post ID for Mapping:</strong></label><br>
            <input type="text" name="cd_acr_map_target_id" value="<?php echo esc_attr(get_post_meta($post->ID, 'cd_acr_map_target_id', true)); ?>" style="width:100%;" placeholder="e.g. {{ request.body.post_id }}">
            <br><small>If empty, mapping will not be performed unless done via Custom PHP. Supports merge tags.</small>
        </p>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add-mapping').on('click', function(e) {
                e.preventDefault();
                var row = $('#cd-acr-mapping-table tbody tr:first').clone();
                row.find('input').val('');
                $('#cd-acr-mapping-table tbody').append(row);
            });

            $(document).on('click', '.remove-mapping', function(e) {
                e.preventDefault();
                if ($('#cd-acr-mapping-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    $(this).closest('tr').find('input').val('');
                }
            });
        });
    </script>
    <?php
}

function cd_acr_save_data_mapping_fields( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( get_post_type($post_id) !== 'cd-custom-rest-api' ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    if (isset($_POST['cd_acr_map_key']) && isset($_POST['cd_acr_map_val'])) {
        $keys = $_POST['cd_acr_map_key'];
        $vals = $_POST['cd_acr_map_val'];
        $mapping = array();

        for ($i = 0; $i < count($keys); $i++) {
            if (!empty($keys[$i])) {
                $mapping[] = array(
                    'key' => sanitize_text_field($keys[$i]),
                    'val' => wp_unslash($vals[$i])
                );
            }
        }
        update_post_meta($post_id, 'cd_acr_data_mapping', $mapping);
    } else {
        delete_post_meta($post_id, 'cd_acr_data_mapping');
    }

    if (isset($_POST['cd_acr_map_target_id'])) {
        update_post_meta($post_id, 'cd_acr_map_target_id', sanitize_text_field($_POST['cd_acr_map_target_id']));
    }
}
add_action( 'save_post', 'cd_acr_save_data_mapping_fields' );
