<?php

//Creating Logs - Meta box
function cd_acr_add_logs_meta_box() {
    add_meta_box(
        'cd_acr_logs', // $id
        'Endpoint Logs (Last 50)', // $title
        'cd_acr_show_logs_meta_box', // $callback
        'cd-custom-rest-api', // $screen
        'normal', // $context
        'low' // $priority
    );
}

add_action( 'add_meta_boxes', 'cd_acr_add_logs_meta_box' );


function cd_acr_show_logs_meta_box( $post ) {
    $logs = get_posts(array(
        'post_type' => 'cd-acr-log',
        'post_parent' => $post->ID,
        'posts_per_page' => 50,
        'order' => 'DESC',
    ));

    if (empty($logs)) {
        echo '<p>No logs found for this endpoint.</p>';
        return;
    }

    ?>
    <div class="cd_acr_logs_container">
        <style>
            .cd-acr-log-item { border-bottom: 1px solid #ddd; padding: 10px 0; }
            .cd-acr-log-header { cursor: pointer; font-weight: bold; display: flex; justify-content: space-between; }
            .cd-acr-log-details { display: none; background: #f9f9f9; padding: 10px; margin-top: 10px; overflow-x: auto; }
            .cd-acr-log-details pre { margin: 0; white-space: pre-wrap; font-size: 11px; }
            .cd-acr-log-item.active .cd-acr-log-details { display: block; }
        </style>

        <?php foreach ($logs as $log) :
            $details = get_post_meta($log->ID, 'cd_acr_log_details', true);
            ?>
            <div class="cd-acr-log-item">
                <div class="cd-acr-log-header">
                    <span><?php echo esc_html($log->post_title); ?></span>
                    <span class="dashicons dashicons-arrow-down"></span>
                </div>
                <div class="cd-acr-log-details">
                    <pre><?php echo esc_html(json_encode($details, JSON_PRETTY_PRINT)); ?></pre>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.cd-acr-log-header').on('click', function() {
                $(this).parent().toggleClass('active');
            });
        });
    </script>
    <?php
}
