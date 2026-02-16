<?php

//Creating Route Type - Meta box
function cd_acr_add_route_type_meta_box() {
    add_meta_box(
        'cd_acr_route_type', // $id
        'Route Type & Actions', // $title
        'cd_acr_show_route_type_meta_box', // $callback
        'cd-custom-rest-api', // $screen
        'normal', // $context
        'high' // $priority
    );
}

add_action( 'add_meta_boxes', 'cd_acr_add_route_type_meta_box' );


//Show Route Type - Custom Fields
function cd_acr_show_route_type_meta_box( $post ) {
    $route_type = get_post_meta( $post->ID, 'cd_acr_route_type', true );
    if ( empty( $route_type ) ) {
        $route_type = 'query_builder';
    }

    $http_methods = get_post_meta( $post->ID, 'cd_acr_http_methods', true );
    if ( empty( $http_methods ) ) {
        $http_methods = array('GET');
    }

    $php_code = get_post_meta( $post->ID, 'cd_acr_php_code', true );

    // External API Settings
    $ext_api_enabled = get_post_meta( $post->ID, 'cd_acr_ext_api_enabled', true );
    $ext_api_url = get_post_meta( $post->ID, 'cd_acr_ext_api_url', true );
    $ext_api_method = get_post_meta( $post->ID, 'cd_acr_ext_api_method', true ) ?: 'GET';
    $ext_api_headers = get_post_meta( $post->ID, 'cd_acr_ext_api_headers', true );
    $ext_api_body = get_post_meta( $post->ID, 'cd_acr_ext_api_body', true );

    // Response Template
    $response_template = get_post_meta( $post->ID, 'cd_acr_response_template', true );
    ?>

    <div class="cd_acr_route_type_container">
        <p>
            <label for="cd_acr_route_type_select"><strong>Select Route Type:</strong></label><br>
            <select name="cd_acr_route_type" id="cd_acr_route_type_select" style="width:100%; max-width: 400px; margin-top: 10px;">
                <option value="query_builder" <?php selected( $route_type, 'query_builder' ); ?>>Query Builder (Default)</option>
                <option value="custom_php" <?php selected( $route_type, 'custom_php' ); ?>>Custom PHP Action</option>
            </select>
        </p>

        <div id="cd_acr_common_fields">
            <p>
                <strong>Incoming HTTP Methods:</strong><br>
                <?php
                $methods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH');
                foreach ($methods as $method) {
                    $checked = in_array($method, $http_methods) ? 'checked' : '';
                    echo '<label style="margin-right: 15px;"><input type="checkbox" name="cd_acr_http_methods[]" value="' . $method . '" ' . $checked . '> ' . $method . '</label>';
                }
                ?>
            </p>
        </div>

        <div id="cd_acr_custom_php_fields" <?php echo ($route_type === 'custom_php') ? '' : 'style="display:none;"'; ?>>
            <hr>
            <h3>External API Call (Optional)</h3>
            <p>
                <label><input type="checkbox" name="cd_acr_ext_api_enabled" value="1" <?php checked($ext_api_enabled, '1'); ?>> Enable External API Call</label>
            </p>
            <div id="cd_acr_ext_api_settings" <?php echo ($ext_api_enabled === '1') ? '' : 'style="display:none;"'; ?>>
                <p>
                    <label>URL:</label><br>
                    <input type="text" name="cd_acr_ext_api_url" value="<?php echo esc_attr($ext_api_url); ?>" style="width:100%;" placeholder="https://api.example.com/endpoint">
                </p>
                <p>
                    <label>Method:</label><br>
                    <select name="cd_acr_ext_api_method">
                        <?php foreach (array('GET', 'POST', 'PUT', 'DELETE', 'PATCH') as $m) : ?>
                            <option value="<?php echo $m; ?>" <?php selected($ext_api_method, $m); ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label>Headers (JSON):</label><br>
                    <textarea name="cd_acr_ext_api_headers" rows="3" style="width:100%; font-family: monospace;" placeholder='{"Authorization": "Bearer {{ request.header.token }}"}'><?php echo esc_textarea($ext_api_headers); ?></textarea>
                </p>
                <p>
                    <label>Body (JSON/Text):</label><br>
                    <textarea name="cd_acr_ext_api_body" rows="5" style="width:100%; font-family: monospace;" placeholder='{"id": "{{ request.body.id }}", "action": "trigger"}'><?php echo esc_textarea($ext_api_body); ?></textarea>
                </p>
            </div>

            <hr>
            <h3>Custom PHP Code</h3>
            <p>
                <label for="cd_acr_php_code"><strong>PHP Code:</strong></label><br>
                <textarea name="cd_acr_php_code" id="cd_acr_php_code" rows="10" style="width:100%; font-family: monospace; margin-top: 10px;" placeholder="return ['message' => 'Hello World'];"><?php echo esc_textarea( $php_code ); ?></textarea>
                <br>
                <small>Use <code>$request</code> to access the <code>WP_REST_Request</code> object and <code>$ext_response</code> for external API response. Return an array/object for JSON response.</small>
            </p>

            <hr>
            <h3>Response Template (Optional)</h3>
            <p>
                <label>JSON Response Template:</label><br>
                <textarea name="cd_acr_response_template" rows="5" style="width:100%; font-family: monospace;" placeholder='{"status": "success", "external_id": "{{ response.body.id }}"}'><?php echo esc_textarea($response_template); ?></textarea>
                <br>
                <small>If provided, this will be returned as the API response (supporting merge tags). If empty, the return value of PHP code will be used.</small>
            </p>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('input[name="cd_acr_ext_api_enabled"]').change(function() {
                if ($(this).is(':checked')) {
                    $('#cd_acr_ext_api_settings').show();
                } else {
                    $('#cd_acr_ext_api_settings').hide();
                }
            });
        });
    </script>

<?php };


function cd_acr_save_route_type_fields( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( get_post_type($post_id) !== 'cd-custom-rest-api' ) return;

    if ( ! current_user_can( 'manage_options' ) ) return;

    if ( isset( $_POST['cd_acr_route_type'] ) ) {
        update_post_meta( $post_id, 'cd_acr_route_type', sanitize_text_field( $_POST['cd_acr_route_type'] ) );
    }

    if ( isset( $_POST['cd_acr_http_methods'] ) && is_array( $_POST['cd_acr_http_methods'] ) ) {
        $methods = array_map( 'sanitize_text_field', $_POST['cd_acr_http_methods'] );
        update_post_meta( $post_id, 'cd_acr_http_methods', $methods );
    } else {
        delete_post_meta( $post_id, 'cd_acr_http_methods' );
    }

    if ( isset( $_POST['cd_acr_php_code'] ) ) {
        $php_code = wp_unslash( $_POST['cd_acr_php_code'] );
        update_post_meta( $post_id, 'cd_acr_php_code', $php_code );
    }

    // External API
    update_post_meta($post_id, 'cd_acr_ext_api_enabled', isset($_POST['cd_acr_ext_api_enabled']) ? '1' : '0');
    if (isset($_POST['cd_acr_ext_api_url'])) {
        update_post_meta($post_id, 'cd_acr_ext_api_url', sanitize_text_field($_POST['cd_acr_ext_api_url']));
    }
    if (isset($_POST['cd_acr_ext_api_method'])) {
        update_post_meta($post_id, 'cd_acr_ext_api_method', sanitize_text_field($_POST['cd_acr_ext_api_method']));
    }
    if (isset($_POST['cd_acr_ext_api_headers'])) {
        update_post_meta($post_id, 'cd_acr_ext_api_headers', wp_unslash($_POST['cd_acr_ext_api_headers']));
    }
    if (isset($_POST['cd_acr_ext_api_body'])) {
        update_post_meta($post_id, 'cd_acr_ext_api_body', wp_unslash($_POST['cd_acr_ext_api_body']));
    }

    // Response Template
    if (isset($_POST['cd_acr_response_template'])) {
        update_post_meta($post_id, 'cd_acr_response_template', wp_unslash($_POST['cd_acr_response_template']));
    }
}
add_action( 'save_post', 'cd_acr_save_route_type_fields' );
