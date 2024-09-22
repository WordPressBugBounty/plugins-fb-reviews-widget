<?php

namespace WP_TrustReviews\Includes;

class Post_Types {

    const FEED_POST_TYPE = Plugin::SLG . '_feed';

    public function register() {
        add_action('init', array($this, 'register_post_types'));

        add_action('trash_' . self::FEED_POST_TYPE, array($this, 'trash'), 10, 2);
        add_action('publish_' . self::FEED_POST_TYPE, array($this, 'publish'), 10, 2);
    }

    public function register_post_types() {
        $this->register_feed_post_type();
    }

    public function register_feed_post_type() {
        $labels = array(
            'name'                  => _x('Reviews widgets', 'Post Type General Name', Plugin::NAME),
            'singular_name'         => _x('Reviews widget', 'Post Type Singular Name', Plugin::NAME),
            'menu_name'             => __('Reviews widgets', Plugin::NAME),
            'name_admin_bar'        => __('Reviews widget', Plugin::NAME),
            'archives'              => __('Reviews Feed Archives', Plugin::NAME),
            'attributes'            => __('Reviews Feed Attributes', Plugin::NAME),
            'parent_item_colon'     => __('Parent Reviews Feed:', Plugin::NAME),
            'all_items'             => __('Widgets', Plugin::NAME),
            'add_new_item'          => __('Add New Reviews Feed', Plugin::NAME),
            'add_new'               => __('Add Reviews Feed', Plugin::NAME),
            'new_item'              => __('New Reviews Feed', Plugin::NAME),
            'edit_item'             => __('Edit Reviews Feed', Plugin::NAME),
            'update_item'           => __('Update Reviews Feed', Plugin::NAME),
            'view_item'             => __('View Reviews Feed', Plugin::NAME),
            'view_items'            => __('View Reviews Feeds', Plugin::NAME),
            'search_items'          => __('Search Reviews Widgets', Plugin::NAME),
            'not_found'             => __('Not found', Plugin::NAME),
            'not_found_in_trash'    => __('Not found in Trash', Plugin::NAME),
            'featured_image'        => __('Featured Image', Plugin::NAME),
            'set_featured_image'    => __('Set featured image', Plugin::NAME),
            'remove_featured_image' => __('Remove featured image', Plugin::NAME),
            'use_featured_image'    => __('Use as featured image', Plugin::NAME),
            'insert_into_item'      => __('Insert into item', Plugin::NAME),
            'uploaded_to_this_item' => __('Uploaded to this item', Plugin::NAME),
            'items_list'            => __('Reviews Feeds list', Plugin::NAME),
            'items_list_navigation' => __('Reviews Feeds list navigation', Plugin::NAME),
            'filter_items_list'     => __('Filter items list', Plugin::NAME),
        );

        $args = array(
            'label'               => __('Reviews Feed', Plugin::NAME),
            'labels'              => $labels,
            'supports'            => array('title'),
            'taxonomies'          => array(),
            'hierarchical'        => false,
            'public'              => false,
            'show_in_rest'        => false,
            'show_ui'             => true,
            'show_in_menu'        => Plugin::SLG,
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capabilities'        => array('create_posts' => 'do_not_allow'),
            'map_meta_cap'        => true,
        );

        register_post_type(self::FEED_POST_TYPE, $args);
    }

    public function trash($ID) {
        $feed_ids = get_option(Plugin::SLG . '_feed_ids');
        if (!empty($feed_ids)) {
            $ids = explode(",", $feed_ids);
            if (in_array($ID, $ids)) {
                $ids = array_diff($ids, [$ID]);
                update_option(Plugin::SLG . '_feed_ids', implode(",", $ids));
            }
        }
    }

    public function publish($ID) {
        $feed_ids = get_option(Plugin::SLG . '_feed_ids');
        $ids = empty($feed_ids) ? array($ID) : explode(",", $feed_ids);
        if (!in_array($ID, $ids)) {
            array_push($ids, $ID);
        }
        update_option(Plugin::SLG . '_feed_ids', implode(",", $ids));
    }
}
