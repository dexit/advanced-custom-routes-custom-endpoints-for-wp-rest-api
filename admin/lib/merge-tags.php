<?php

/**
 * Parses templated merge tags like {{ request.body.item_id }}
 *
 * @param string $content The string containing tags.
 * @param WP_REST_Request|null $request The incoming REST request.
 * @param array|null $response The external API response array (body, headers, status).
 * @param mixed $php_result The result from the pre-transform PHP block.
 * @return string The parsed content.
 */
function cd_acr_parse_merge_tags($content, $request = null, $response = null, $php_result = null) {
    if (empty($content)) return $content;

    return preg_replace_callback('/\{\{\s*([\w\.\-]+)\s*\}\}/', function($matches) use ($request, $response, $php_result) {
        $tag = $matches[1];
        $parts = explode('.', $tag);
        $type = array_shift($parts);

        switch ($type) {
            case 'request':
                if (!$request) return $matches[0];
                $source = array_shift($parts);
                if ($source === 'body') {
                    $data = $request->get_json_params();
                    if (empty($data)) $data = $request->get_body_params();
                    return cd_acr_get_nested_value($data, $parts);
                } elseif ($source === 'query') {
                    return cd_acr_get_nested_value($request->get_query_params(), $parts);
                } elseif ($source === 'header') {
                    return $request->get_header(implode('_', $parts)) ?? '';
                }
                break;

            case 'response':
                if (!$response || !isset($response['body'])) return $matches[0];
                $source = array_shift($parts);
                if ($source === 'body') {
                    $data = json_decode($response['body'], true);
                    if (!$data) $data = $response['body'];
                    return cd_acr_get_nested_value($data, $parts);
                } elseif ($source === 'header') {
                    $headers = $response['headers'] ?? [];
                    return $headers[implode('-', $parts)] ?? '';
                }
                break;

            case 'php':
                if (is_null($php_result)) return '';
                return cd_acr_get_nested_value($php_result, $parts);

            case 'user':
                $user = wp_get_current_user();
                if (!$user->exists()) return '';
                $field = $parts[0] ?? 'ID';
                return $user->$field ?? '';

            case 'site':
                $field = $parts[0] ?? 'name';
                if ($field === 'url') return get_site_url();
                return get_bloginfo($field);
        }

        return $matches[0];
    }, $content);
}

/**
 * Helper to get nested value from array or object using parts.
 */
function cd_acr_get_nested_value($data, $parts) {
    if (empty($parts)) return is_array($data) || is_object($data) ? json_encode($data) : (string)$data;

    $current = $data;
    foreach ($parts as $part) {
        if (is_array($current) && isset($current[$part])) {
            $current = $current[$part];
        } elseif (is_object($current) && isset($current->$part)) {
            $current = $current->$part;
        } else {
            return '';
        }
    }

    return is_array($current) || is_object($current) ? json_encode($current) : (string)$current;
}
