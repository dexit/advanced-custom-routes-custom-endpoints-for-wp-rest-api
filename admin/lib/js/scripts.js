jQuery(document).ready(function($) {

  $(".cd_acr_heading--post-type").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--post-types" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--post-parents").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_post_parents" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--post-id").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--post-ids" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--category").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--category" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--tag").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--tags" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--author").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--author" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--status").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--status" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--date").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--date" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--ordering").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--ordering" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--amount-offset").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--amount-offset" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

  $(".cd_acr_heading--post-parent").click(function(){
    $( ".cd_acr_field" ).removeClass('cd_acr_field--active');
    $( ".cd_acr_field--post-parent" ).addClass('cd_acr_field--active');
    $( ".cd_acr_heading" ).removeClass('cd_acr_heading--active');
    $( this ).addClass('cd_acr_heading--active');
  });

    $('.cd_acr_post_ids, .cd_acr_post_ids_exclude, .cd_acr_categories, .cd_acr_categories_exclude, .cd_acr_tags, .cd_acr_tags_exclude, .cd_acr_authors, .cd_acr_authors_exclude, .cd_acr_post_parent_exclude, .cd_acr_post_parent_include').select2();

    function toggleRouteTypeFields(type) {
        if (type === 'custom_php') {
            $('#cd_acr_custom_php_fields').show();
            $('#cd_acr_data_mapping').show();
            $('#cd_acr_query_builder_options').hide();
            $('#cd_acr_response_output').hide();
        } else {
            $('#cd_acr_custom_php_fields').hide();
            $('#cd_acr_data_mapping').hide();
            $('#cd_acr_query_builder_options').show();
            $('#cd_acr_response_output').show();
        }
    }

    $('#cd_acr_route_type_select').change(function() {
        toggleRouteTypeFields($(this).val());
    });

    // Initial state
    toggleRouteTypeFields($('#cd_acr_route_type_select').val());

});
