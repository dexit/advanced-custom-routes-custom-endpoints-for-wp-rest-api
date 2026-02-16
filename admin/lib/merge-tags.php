<?php

/**
 * Parses templated merge tags like {{ request.body.item_id }}
 *
 * @param string $content The string containing tags.
 * @param WP_REST_Request|null $request The incoming REST request.
 * @param array|null $response The external API response array (body, headers, status).
 * @return string The parsed content.
 */
function cd_acr_parse_merge_tags($content, $request = null, $response = null) {
    if (empty($content)) return $content;

    // Pattern for {{ type.key.subkey... }}
    return preg_replace_callback('/\{\{\s*([\w\.]+)\s*\}\}/', function($matches) use ($request, $response) {
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
                    if (!$data) $data = $response['body']; // fallback to raw body if not JSON
                    return cd_acr_get_nested_value($data, $parts);
                } elseif ($source === 'header') {
                    $headers = $response['headers'] ?? [];
                    return $headers[implode('-', $parts)] ?? '';
                }
                break;

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
 * Helper to get nested value from array using parts.
 */
function cd_acr_get_nested_value($data, $parts) {
    if (empty($parts)) return is_array($data) ? json_encode($data) : (string)$data;

    $current = $data;
    foreach ($parts as $part) {
        if (is_array($current) && isset($current[$part])) {
            $current = $current[$part];
        } else {
            return '';
        }
    }

    return is_array($current) ? json_encode($current) : (string)$current;
}
