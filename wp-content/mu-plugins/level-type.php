<?php
// Register Custom Type
function level_type() {
  $labels = array(
    'name'                       => _x( 'Types', 'Type General Name', 'level_type' ),
    'singular_name'              => _x( 'Type', 'Type Singular Name', 'level_type' ),
    'menu_name'                  => __( 'Type', 'level_type' ),
    'all_items'                  => __( 'All Level Types', 'level_type' ),
    'parent_item'                => __( 'Parent Level Type', 'level_type' ),
    'parent_item_colon'          => __( 'Parent Level Type:', 'level_type' ),
    'new_item_name'              => __( 'New Level Type Name', 'level_type' ),
    'add_new_item'               => __( 'Add New Level Type', 'level_type' ),
    'edit_item'                  => __( 'Edit Level Type', 'level_type' ),
    'update_item'                => __( 'Update Level Type', 'level_type' ),
    'separate_items_with_commas' => __( 'Separate level types with commas', 'level_type' ),
    'search_items'               => __( 'Search Level Types', 'level_type' ),
    'add_or_remove_items'        => __( 'Add or remove level types', 'level_type' ),
    'choose_from_most_used'      => __( 'Choose from the most used level types', 'level_type' ),
    'not_found'                  => __( 'Not Found', 'level_type' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => false,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'level_type', array( 'level' ), $args );
}

// Hook into the 'init' action
add_action( 'init', 'level_type', 0 );
