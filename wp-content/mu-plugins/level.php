<?php

// Register Custom Level
function level() {

  $labels = array(
    'name'                => _x( 'Levels', 'Level General Name', 'level' ),
    'singular_name'       => _x( 'Level', 'Level Singular Name', 'level' ),
    'menu_name'           => __( 'Levels', 'level' ),
    'parent_item_colon'   => __( 'Parent Level:', 'level' ),
    'all_items'           => __( 'All Levels', 'level' ),
    'view_item'           => __( 'View Level', 'level' ),
    'add_new_item'        => __( 'Add New Level', 'level' ),
    'add_new'             => __( 'Add New', 'level' ),
    'edit_item'           => __( 'Edit Level', 'level' ),
    'update_item'         => __( 'Update Level', 'level' ),
    'search_items'        => __( 'Search Level', 'level' ),
    'not_found'           => __( 'Not found', 'level' ),
    'not_found_in_trash'  => __( 'Not found in Trash', 'level' ),
  );
  $args = array(
    'label'               => __( 'level', 'level' ),
    'description'         => __( 'Level Description', 'level' ),
    'labels'              => $labels,
    'supports'            => array( ),
    'taxonomies'          => array( 'level_type' ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capabilities' => array(
      'publish_posts' => 'publish_levels',
      'edit_posts' => 'edit_levels',
      'edit_others_posts' => 'edit_others_levels',
      'delete_posts' => 'delete_levels',
      'delete_others_posts' => 'delete_others_levels',
      'read_private_posts' => 'read_private_levels',
      'edit_post' => 'edit_level',
      'delete_post' => 'delete_level',
      'read_post' => 'read_level',
    ),
  );
  register_post_type( 'level', $args );

  $role = get_role( 'contributor' );
  $role->add_cap( 'publish_levels' );
  $role->add_cap( 'edit_levels' );
  $role->add_cap( 'delete_levels' );
  $role->add_cap( 'edit_level' );
  $role->add_cap( 'read_level' );

  $role = get_role( 'administrator' );
  $role->add_cap( 'publish_levels' );
  $role->add_cap( 'edit_levels' );
  $role->add_cap( 'edit_others_levels' );
  $role->add_cap( 'delete_levels' );
  $role->add_cap( 'delete_others_levels' );
  $role->add_cap( 'read_private_levels' );
  $role->add_cap( 'edit_level' );
  $role->add_cap( 'delete_level' );
  $role->add_cap( 'read_level' );

  echo '<script>console.log('.json_encode( $role ).');</script>';
}

// Hook into the 'init' action
add_action( 'init', 'level', 0 );

