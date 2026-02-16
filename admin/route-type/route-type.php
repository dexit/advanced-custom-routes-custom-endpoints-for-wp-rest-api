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
    $route_type = get_post_meta( $post->ID, 'cd_acr_route_type', true ) ?: 'query_builder';
    $http_methods = get_post_meta( $post->ID, 'cd_acr_http_methods', true ) ?: array('GET');
    $php_code = get_post_meta( $post->ID, 'cd_acr_php_code', true );

    // Incoming Security
    $inc_auth_type = get_post_meta($post->ID, 'cd_acr_inc_auth_type', true) ?: 'none';
    $inc_key_name = get_post_meta($post->ID, 'cd_acr_inc_key_name', true) ?: 'X-API-Key';
    $inc_key_val = get_post_meta($post->ID, 'cd_acr_inc_key_val', true);
    $inc_key_loc = get_post_meta($post->ID, 'cd_acr_inc_key_loc', true) ?: 'header';
    $inc_hmac_secret = get_post_meta($post->ID, 'cd_acr_inc_hmac_secret', true);
    $inc_hmac_header = get_post_meta($post->ID, 'cd_acr_inc_hmac_header', true) ?: 'X-Signature';
    $inc_hmac_algo = get_post_meta($post->ID, 'cd_acr_inc_hmac_algo', true) ?: 'sha256';

    // External API Settings
    $ext_api_enabled = get_post_meta( $post->ID, 'cd_acr_ext_api_enabled', true );
    $ext_api_url = get_post_meta( $post->ID, 'cd_acr_ext_api_url', true );
    $ext_api_method = get_post_meta( $post->ID, 'cd_acr_ext_api_method', true ) ?: 'GET';
    $ext_api_headers = get_post_meta( $post->ID, 'cd_acr_ext_api_headers', true );
    $ext_api_body = get_post_meta( $post->ID, 'cd_acr_ext_api_body', true );
    $ext_auth_type = get_post_meta($post->ID, 'cd_acr_ext_auth_type', true) ?: 'none';
    $ext_bearer_token = get_post_meta($post->ID, 'cd_acr_ext_bearer_token', true);
    $ext_basic_user = get_post_meta($post->ID, 'cd_acr_ext_basic_user', true);
    $ext_basic_pass = get_post_meta($post->ID, 'cd_acr_ext_basic_pass', true);
    $ext_key_name = get_post_meta($post->ID, 'cd_acr_ext_key_name', true);
    $ext_key_val = get_post_meta($post->ID, 'cd_acr_ext_key_val', true);
    $ext_key_loc = get_post_meta($post->ID, 'cd_acr_ext_key_loc', true) ?: 'header';

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

            <hr>
            <h3>Incoming Security / Authentication</h3>
            <p>
                <label>Auth Type:</label><br>
                <select name="cd_acr_inc_auth_type" id="cd_acr_inc_auth_type">
                    <option value="none" <?php selected($inc_auth_type, 'none'); ?>>None (Public)</option>
                    <option value="api_key" <?php selected($inc_auth_type, 'api_key'); ?>>API Key</option>
                    <option value="hmac" <?php selected($inc_auth_type, 'hmac'); ?>>HMAC Signature</option>
                </select>
            </p>

            <div id="inc_auth_api_key_fields" class="inc_auth_fields" <?php echo ($inc_auth_type === 'api_key') ? '' : 'style="display:none;"'; ?>>
                <p>
                    <label>Key Name:</label> <input type="text" name="cd_acr_inc_key_name" value="<?php echo esc_attr($inc_key_name); ?>" placeholder="X-API-Key">
                    <label>Key Value:</label> <input type="password" name="cd_acr_inc_key_val" value="<?php echo esc_attr($inc_key_val); ?>">
                    <label>Location:</label>
                    <select name="cd_acr_inc_key_loc">
                        <option value="header" <?php selected($inc_key_loc, 'header'); ?>>Header</option>
                        <option value="query" <?php selected($inc_key_loc, 'query'); ?>>Query Param</option>
                    </select>
                </p>
            </div>

            <div id="inc_auth_hmac_fields" class="inc_auth_fields" <?php echo ($inc_auth_type === 'hmac') ? '' : 'style="display:none;"'; ?>>
                <p>
                    <label>Secret Key:</label> <input type="password" name="cd_acr_inc_hmac_secret" value="<?php echo esc_attr($inc_hmac_secret); ?>" style="width:300px;">
                    <br><br>
                    <label>Signature Header:</label> <input type="text" name="cd_acr_inc_hmac_header" value="<?php echo esc_attr($inc_hmac_header); ?>" placeholder="X-Signature">
                    <label>Algorithm:</label>
                    <select name="cd_acr_inc_hmac_algo">
                        <option value="sha256" <?php selected($inc_hmac_algo, 'sha256'); ?>>SHA256</option>
                        <option value="sha1" <?php selected($inc_hmac_algo, 'sha1'); ?>>SHA1</option>
                        <option value="md5" <?php selected($inc_hmac_algo, 'md5'); ?>>MD5</option>
                    </select>
                </p>
                <p><small>HMAC signature is calculated as <code>hash_hmac(algo, request_body, secret)</code></small></p>
            </div>
        </div>

        <div id="cd_acr_custom_php_fields" <?php echo ($route_type === 'custom_php') ? '' : 'style="display:none;"'; ?>>
            <hr>
            <h3>External API Call (Forwarding)</h3>
            <p>
                <label><input type="checkbox" name="cd_acr_ext_api_enabled" value="1" <?php checked($ext_api_enabled, '1'); ?>> Enable External API Call</label>
            </p>
            <div id="cd_acr_ext_api_settings" <?php echo ($ext_api_enabled === '1') ? '' : 'style="display:none;"'; ?>>
                <p>
                    <label>URL:</label><br>
                    <input type="text" name="cd_acr_ext_api_url" value="<?php echo esc_attr($ext_api_url); ?>" style="width:100%;" placeholder="https://api.example.com/endpoint">
                </p>
                <p>
                    <label>Method:</label>
                    <select name="cd_acr_ext_api_method">
                        <?php foreach (array('GET', 'POST', 'PUT', 'DELETE', 'PATCH') as $m) : ?>
                            <option value="<?php echo $m; ?>" <?php selected($ext_api_method, $m); ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label><strong>Authentication:</strong></label><br>
                    <select name="cd_acr_ext_auth_type" id="cd_acr_ext_auth_type">
                        <option value="none" <?php selected($ext_auth_type, 'none'); ?>>None</option>
                        <option value="bearer" <?php selected($ext_auth_type, 'bearer'); ?>>Bearer Token</option>
                        <option value="basic" <?php selected($ext_auth_type, 'basic'); ?>>Basic Auth</option>
                        <option value="api_key" <?php selected($ext_auth_type, 'api_key'); ?>>API Key</option>
                    </select>
                </p>

                <div id="ext_auth_bearer_fields" class="ext_auth_fields" <?php echo ($ext_auth_type === 'bearer') ? '' : 'style="display:none;"'; ?>>
                    <p><label>Token:</label> <input type="text" name="cd_acr_ext_bearer_token" value="<?php echo esc_attr($ext_bearer_token); ?>" style="width:80%;" placeholder="Supports merge tags"></p>
                </div>
                <div id="ext_auth_basic_fields" class="ext_auth_fields" <?php echo ($ext_auth_type === 'basic') ? '' : 'style="display:none;"'; ?>>
                    <p>
                        <label>Username:</label> <input type="text" name="cd_acr_ext_basic_user" value="<?php echo esc_attr($ext_basic_user); ?>">
                        <label>Password:</label> <input type="password" name="cd_acr_ext_basic_pass" value="<?php echo esc_attr($ext_basic_pass); ?>">
                    </p>
                </div>
                <div id="ext_auth_api_key_fields" class="ext_auth_fields" <?php echo ($ext_auth_type === 'api_key') ? '' : 'style="display:none;"'; ?>>
                    <p>
                        <label>Key Name:</label> <input type="text" name="cd_acr_ext_key_name" value="<?php echo esc_attr($ext_key_name); ?>" placeholder="e.g. apikey">
                        <label>Key Value:</label> <input type="text" name="cd_acr_ext_key_val" value="<?php echo esc_attr($ext_key_val); ?>">
                        <label>Location:</label>
                        <select name="cd_acr_ext_key_loc">
                            <option value="header" <?php selected($ext_key_loc, 'header'); ?>>Header</option>
                            <option value="query" <?php selected($ext_key_loc, 'query'); ?>>Query Param</option>
                        </select>
                    </p>
                </div>

                <p>
                    <label>Headers (JSON):</label><br>
                    <textarea name="cd_acr_ext_api_headers" rows="3" style="width:100%; font-family: monospace;" placeholder='{"Content-Type": "application/json"}'><?php echo esc_textarea($ext_api_headers); ?></textarea>
                </p>
                <p>
                    <label>Body (JSON/Text):</label><br>
                    <textarea name="cd_acr_ext_api_body" rows="5" style="width:100%; font-family: monospace;" placeholder='{"id": "{{ request.body.id }}", "transformed": "{{ php.transformed_val }}"}'><?php echo esc_textarea($ext_api_body); ?></textarea>
                </p>
            </div>

            <hr>
            <h3>Custom PHP Code</h3>
            <p>
                <label for="cd_acr_php_code"><strong>PHP Code:</strong></label><br>
                <textarea name="cd_acr_php_code" id="cd_acr_php_code" rows="10" style="width:100%; font-family: monospace; margin-top: 10px;" placeholder="return ['transformed_val' => $request->get_param('id')];"><?php echo esc_textarea( $php_code ); ?></textarea>
                <br>
                <small>Available: <code>$request</code> (WP_REST_Request). Use <code>acr_parse("{{ tag }}")</code> to manually parse tags. Return value is available via <code>{{ php.key }}</code>.</small>
            </p>

            <hr>
            <h3>Response Template (Optional)</h3>
            <p>
                <label>JSON Response Template:</label><br>
                <textarea name="cd_acr_response_template" rows="5" style="width:100%; font-family: monospace;" placeholder='{"status": "success", "ext": "{{ response.body.id }}"}'><?php echo esc_textarea($response_template); ?></textarea>
                <br>
                <small>If provided, this will be the final API response. Supports merge tags.</small>
            </p>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Incoming Auth toggle
            $('#cd_acr_inc_auth_type').change(function() {
                $('.inc_auth_fields').hide();
                if ($(this).val() === 'api_key') $('#inc_auth_api_key_fields').show();
                if ($(this).val() === 'hmac') $('#inc_auth_hmac_fields').show();
            });

            // External API toggle
            $('input[name="cd_acr_ext_api_enabled"]').change(function() {
                if ($(this).is(':checked')) {
                    $('#cd_acr_ext_api_settings').show();
                } else {
                    $('#cd_acr_ext_api_settings').hide();
                }
            });

            // External Auth toggle
            $('#cd_acr_ext_auth_type').change(function() {
                $('.ext_auth_fields').hide();
                if ($(this).val() === 'bearer') $('#ext_auth_bearer_fields').show();
                if ($(this).val() === 'basic') $('#ext_auth_basic_fields').show();
                if ($(this).val() === 'api_key') $('#ext_auth_api_key_fields').show();
            });
        });
    </script>

<?php };


function cd_acr_save_route_type_fields( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( get_post_type($post_id) !== 'cd-custom-rest-api' ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $fields = array(
        'cd_acr_route_type',
        'cd_acr_inc_auth_type',
        'cd_acr_inc_key_name',
        'cd_acr_inc_key_val',
        'cd_acr_inc_key_loc',
        'cd_acr_inc_hmac_secret',
        'cd_acr_inc_hmac_header',
        'cd_acr_inc_hmac_algo',
        'cd_acr_ext_api_url',
        'cd_acr_ext_api_method',
        'cd_acr_ext_auth_type',
        'cd_acr_ext_bearer_token',
        'cd_acr_ext_basic_user',
        'cd_acr_ext_basic_pass',
        'cd_acr_ext_key_name',
        'cd_acr_ext_key_val',
        'cd_acr_ext_key_loc'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    if ( isset( $_POST['cd_acr_http_methods'] ) && is_array( $_POST['cd_acr_http_methods'] ) ) {
        $methods = array_map( 'sanitize_text_field', $_POST['cd_acr_http_methods'] );
        update_post_meta( $post_id, 'cd_acr_http_methods', $methods );
    } else {
        delete_post_meta( $post_id, 'cd_acr_http_methods' );
    }

    if ( isset( $_POST['cd_acr_php_code'] ) ) {
        update_post_meta( $post_id, 'cd_acr_php_code', wp_unslash( $_POST['cd_acr_php_code'] ) );
    }

    update_post_meta($post_id, 'cd_acr_ext_api_enabled', isset($_POST['cd_acr_ext_api_enabled']) ? '1' : '0');

    if (isset($_POST['cd_acr_ext_api_headers'])) {
        update_post_meta($post_id, 'cd_acr_ext_api_headers', wp_unslash($_POST['cd_acr_ext_api_headers']));
    }
    if (isset($_POST['cd_acr_ext_api_body'])) {
        update_post_meta($post_id, 'cd_acr_ext_api_body', wp_unslash($_POST['cd_acr_ext_api_body']));
    }
    if (isset($_POST['cd_acr_response_template'])) {
        update_post_meta($post_id, 'cd_acr_response_template', wp_unslash($_POST['cd_acr_response_template']));
    }
}
add_action( 'save_post', 'cd_acr_save_route_type_fields' );
