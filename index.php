<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name: Advanced Custom Routes - Custom Endpoints for WP REST API
 * Description: The easiest solution to add custom REST API routes to your WordPress site.
 * Author: Carlile Design
 * Version: 0.8.0
 * Author URI: carlile.design
 */


 //Register Custom Endpoint Post Type
 include 'admin/register-custom-post-type.php';

 //Create Query Builder Option Fields
 include 'admin/query-builder/query-builder-options.php';

 //Create Response Output Fields
 include 'admin/response-output/response-output.php';

 //Create Route Type Fields
 include 'admin/route-type/route-type.php';

 //Create Data Mapping Fields
 include 'admin/route-type/data-mapping.php';

 //Create Logs Metabox
 include 'admin/route-type/logs-metabox.php';

 //Create Route Endpoint Fields
 include 'admin/route-endpoint/route-endpoint.php';

 //Register Routes Function
 include 'admin/register-routes/register-routes.php';



include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {

  $dir = plugin_dir_path( __FILE__ );

  require_once($dir.'admin/settings.php');

  require_once($dir.'admin/endpoint-column.php');


  // require_once($dir.'public/frontend/frontend.php');

  // Add scripts and styles to admin
  function cd_acr_admin_assets($hook) {
      $screen = get_current_screen();

      // Load assets only on our custom post type or settings page
      if ( (isset($screen->post_type) && $screen->post_type === 'cd-custom-rest-api') || strpos($hook, 'wp-rest-endpoints-settings') !== false ) {
          wp_enqueue_style( 'select2_styles', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css' );
          wp_enqueue_style( 'cd_acr_enqueue', plugins_url( 'admin/lib/css/dist/styles.css', __FILE__ ) );

          wp_enqueue_script( 'select2_scripts', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js', array('jquery'), null, true );
          wp_enqueue_script( 'cd_acr_enqueue', plugins_url( 'admin/lib/js/scripts.js', __FILE__ ), array('jquery', 'select2_scripts'), null, true );
      }
  }
  add_action('admin_enqueue_scripts', 'cd_acr_admin_assets');




}
