<?php

/**
 * Logs activity for a specific endpoint.
 */
function cd_acr_log_activity($endpoint_id, $data) {
    $title = sprintf(
        '[%s] %s - %s',
        date('Y-m-d H:i:s'),
        get_the_title($endpoint_id),
        $data['method'] ?? 'GET'
    );

    $log_id = wp_insert_post(array(
        'post_type' => 'cd-acr-log',
        'post_title' => $title,
        'post_status' => 'publish',
        'post_parent' => $endpoint_id,
    ));

    if ($log_id) {
        update_post_meta($log_id, 'cd_acr_log_details', $data);

        // Keep only last 50 logs per endpoint to save space
        $logs = get_posts(array(
            'post_type' => 'cd-acr-log',
            'post_parent' => $endpoint_id,
            'posts_per_page' => -1,
            'fields' => 'ids',
            'order' => 'DESC',
        ));

        if (count($logs) > 50) {
            $to_delete = array_slice($logs, 50);
            foreach ($to_delete as $id) {
                wp_delete_post($id, true);
            }
        }
    }
}
