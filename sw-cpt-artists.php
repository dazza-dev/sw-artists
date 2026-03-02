<?php

/**
 * Plugin Name: SW - Artists CPT
 * Plugin URI: https://www.seniors.com.co
 * Description: Custom Post Type "Artists" with native custom fields and optional WPGraphQL support.
 * Version: 1.0.0
 * Author: Seniors
 * Author URI: https://www.seniors.com.co
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sw-artists
 * Requires PHP: 7.4
 * Requires at least: 5.8
 *
 * FEATURES:
 * - Custom Post Type: Artists
 * - Custom Taxonomy: Artist Category
 * - WPGraphQL support
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SW_ARTISTS_VERSION', '1.0.0');
define('SW_ARTISTS_TEXT_DOMAIN', 'sw-artists');

/**
 * Register Custom Post Type: Artists
 */
function sw_artists_register_cpt()
{
    $labels = array(
        'name'                  => _x('Artists', 'Post Type General Name', SW_ARTISTS_TEXT_DOMAIN),
        'singular_name'         => _x('Artist', 'Post Type Singular Name', SW_ARTISTS_TEXT_DOMAIN),
        'menu_name'             => __('Artists', SW_ARTISTS_TEXT_DOMAIN),
        'name_admin_bar'        => __('Artist', SW_ARTISTS_TEXT_DOMAIN),
        'archives'              => __('Artist Archives', SW_ARTISTS_TEXT_DOMAIN),
        'attributes'            => __('Artist Attributes', SW_ARTISTS_TEXT_DOMAIN),
        'all_items'             => __('All Artists', SW_ARTISTS_TEXT_DOMAIN),
        'add_new_item'          => __('Add New Artist', SW_ARTISTS_TEXT_DOMAIN),
        'add_new'               => __('Add New', SW_ARTISTS_TEXT_DOMAIN),
        'new_item'              => __('New Artist', SW_ARTISTS_TEXT_DOMAIN),
        'edit_item'             => __('Edit Artist', SW_ARTISTS_TEXT_DOMAIN),
        'update_item'           => __('Update Artist', SW_ARTISTS_TEXT_DOMAIN),
        'view_item'             => __('View Artist', SW_ARTISTS_TEXT_DOMAIN),
        'view_items'            => __('View Artists', SW_ARTISTS_TEXT_DOMAIN),
        'search_items'          => __('Search Artist', SW_ARTISTS_TEXT_DOMAIN),
        'not_found'             => __('Not found', SW_ARTISTS_TEXT_DOMAIN),
        'not_found_in_trash'    => __('Not found in Trash', SW_ARTISTS_TEXT_DOMAIN),
    );

    $args = array(
        'label'                 => __('Artist', SW_ARTISTS_TEXT_DOMAIN),
        'description'           => __('Artists and musicians', SW_ARTISTS_TEXT_DOMAIN),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'page-attributes'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-microphone',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );

    // Add GraphQL support if WPGraphQL is active
    if (class_exists('WPGraphQL')) {
        $args['show_in_graphql'] = true;
        $args['graphql_single_name'] = 'swArtist';
        $args['graphql_plural_name'] = 'swArtists';
    }

    register_post_type('artist', $args);
}
add_action('init', 'sw_artists_register_cpt', 0);

/**
 * Register Taxonomy: Artist Category
 */
function sw_artists_register_taxonomy()
{
    $labels = array(
        'name'                       => _x('Artist Categories', 'Taxonomy General Name', SW_ARTISTS_TEXT_DOMAIN),
        'singular_name'              => _x('Artist Category', 'Taxonomy Singular Name', SW_ARTISTS_TEXT_DOMAIN),
        'menu_name'                  => __('Categories', SW_ARTISTS_TEXT_DOMAIN),
        'all_items'                  => __('All Categories', SW_ARTISTS_TEXT_DOMAIN),
        'new_item_name'              => __('New Category Name', SW_ARTISTS_TEXT_DOMAIN),
        'add_new_item'               => __('Add New Category', SW_ARTISTS_TEXT_DOMAIN),
        'edit_item'                  => __('Edit Category', SW_ARTISTS_TEXT_DOMAIN),
        'update_item'                => __('Update Category', SW_ARTISTS_TEXT_DOMAIN),
        'view_item'                  => __('View Category', SW_ARTISTS_TEXT_DOMAIN),
        'search_items'               => __('Search Categories', SW_ARTISTS_TEXT_DOMAIN),
        'not_found'                  => __('Not Found', SW_ARTISTS_TEXT_DOMAIN),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
    );

    // Add GraphQL support if WPGraphQL is active
    if (class_exists('WPGraphQL')) {
        $args['show_in_graphql'] = true;
        $args['graphql_single_name'] = 'swArtistCategory';
        $args['graphql_plural_name'] = 'swArtistCategories';
    }

    register_taxonomy('artist_category', array('artist'), $args);
}
add_action('init', 'sw_artists_register_taxonomy', 0);

/**
 * Add custom meta box for Artist fields
 */
function sw_artists_add_meta_box()
{
    add_meta_box(
        'artist_details',
        __('Artist Details', SW_ARTISTS_TEXT_DOMAIN),
        'sw_artists_meta_box_callback',
        'artist',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'sw_artists_add_meta_box');

/**
 * Meta box callback
 */
function sw_artists_meta_box_callback($post)
{
    // Add nonce for security
    wp_nonce_field('sw_artists_save_meta', 'sw_artists_meta_nonce');

    // Get current values
    $instagram = get_post_meta($post->ID, '_artist_instagram', true);
    $tiktok = get_post_meta($post->ID, '_artist_tiktok', true);
    $spotify = get_post_meta($post->ID, '_artist_spotify', true);
    $youtube = get_post_meta($post->ID, '_artist_youtube', true);
    $facebook = get_post_meta($post->ID, '_artist_facebook', true);
    $twitter = get_post_meta($post->ID, '_artist_twitter', true);
    $apple_music = get_post_meta($post->ID, '_artist_apple_music', true);

?>
    <p class="description">
        <strong><?php _e('Note:', SW_ARTISTS_TEXT_DOMAIN); ?></strong>
        <?php _e('To set the display order, use the "Order" field in the Page Attributes panel on the right side of the editor.', SW_ARTISTS_TEXT_DOMAIN); ?>
    </p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="artist_instagram">
                    <?php _e('Instagram URL', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_instagram"
                    name="artist_instagram"
                    value="<?php echo esc_attr($instagram); ?>"
                    class="regular-text"
                    placeholder="https://instagram.com/username" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="artist_tiktok">
                    <?php _e('TikTok URL', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_tiktok"
                    name="artist_tiktok"
                    value="<?php echo esc_attr($tiktok); ?>"
                    class="regular-text"
                    placeholder="https://tiktok.com/@username" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="artist_spotify">
                    <?php _e('Spotify URL', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_spotify"
                    name="artist_spotify"
                    value="<?php echo esc_attr($spotify); ?>"
                    class="regular-text"
                    placeholder="https://open.spotify.com/artist/..." />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="artist_youtube">
                    <?php _e('YouTube URL', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_youtube"
                    name="artist_youtube"
                    value="<?php echo esc_attr($youtube); ?>"
                    class="regular-text"
                    placeholder="https://youtube.com/@username" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="artist_facebook">
                    <?php _e('Facebook URL', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_facebook"
                    name="artist_facebook"
                    value="<?php echo esc_attr($facebook); ?>"
                    class="regular-text"
                    placeholder="https://facebook.com/username" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="artist_twitter">
                    <?php _e('Twitter/X URL', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_twitter"
                    name="artist_twitter"
                    value="<?php echo esc_attr($twitter); ?>"
                    class="regular-text"
                    placeholder="https://twitter.com/username" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="artist_apple_music">
                    <?php _e('Apple Music', SW_ARTISTS_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="url"
                    id="artist_apple_music"
                    name="artist_apple_music"
                    value="<?php echo esc_attr($apple_music); ?>"
                    class="regular-text"
                    placeholder="https://music.apple.com/artist/..." />
            </td>
        </tr>
    </table>
<?php
}

/**
 * Save meta box data
 */
function sw_artists_save_meta($post_id)
{
    // Check nonce
    if (!isset($_POST['sw_artists_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['sw_artists_meta_nonce'], 'sw_artists_save_meta')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Note: Order is now handled by WordPress natively via menu_order (page-attributes)

    // Save social media URLs
    $social_fields = ['instagram', 'tiktok', 'spotify', 'youtube', 'facebook', 'twitter', 'apple_music'];
    foreach ($social_fields as $field) {
        if (isset($_POST["artist_$field"])) {
            update_post_meta(
                $post_id,
                "_artist_$field",
                esc_url_raw($_POST["artist_$field"])
            );
        }
    }
}
add_action('save_post_artist', 'sw_artists_save_meta');

/**
 * Add custom columns to Artists admin table
 */
function sw_artists_add_admin_columns($columns)
{
    // Insert after title column
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['artist_thumbnail'] = __('Photo', SW_ARTISTS_TEXT_DOMAIN);
            $new_columns['artist_order'] = __('Order', SW_ARTISTS_TEXT_DOMAIN);
            $new_columns['artist_social'] = __('Social Media', SW_ARTISTS_TEXT_DOMAIN);
        }
    }
    return $new_columns;
}
add_filter('manage_artist_posts_columns', 'sw_artists_add_admin_columns');

/**
 * Display custom column content
 */
function sw_artists_display_admin_columns($column, $post_id)
{
    switch ($column) {
        case 'artist_thumbnail':
            $thumbnail = get_the_post_thumbnail($post_id, array(50, 50));
            echo $thumbnail ?: '—';
            break;

        case 'artist_order':
            $post = get_post($post_id);
            echo $post->menu_order;
            break;

        case 'artist_social':
            $socials = ['instagram', 'tiktok', 'spotify', 'youtube', 'facebook', 'twitter', 'apple_music'];
            $icons = [];
            foreach ($socials as $social) {
                $url = get_post_meta($post_id, "_artist_$social", true);
                if ($url) {
                    $display_name = $social === 'apple_music' ? 'Apple Music' : ucfirst($social);
                    $icons[] = $display_name;
                }
            }
            echo $icons ? implode(', ', $icons) : '—';
            break;
    }
}
add_action('manage_artist_posts_custom_column', 'sw_artists_display_admin_columns', 10, 2);

/**
 * Make Order column sortable
 */
function sw_artists_sortable_columns($columns)
{
    $columns['artist_order'] = 'artist_order';
    return $columns;
}
add_filter('manage_edit-artist_sortable_columns', 'sw_artists_sortable_columns');

/**
 * Sort by menu_order field
 */
function sw_artists_orderby($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'artist_order') {
        $query->set('orderby', 'menu_order');
    }
}
add_action('pre_get_posts', 'sw_artists_orderby');

/**
 * Register custom fields in WPGraphQL (only if WPGraphQL is active)
 */
function sw_artists_register_graphql_fields()
{
    // Only register if WPGraphQL is active
    if (!class_exists('WPGraphQL')) {
        return;
    }

    // Register swArtistFields object type
    register_graphql_object_type('SwArtistFields', [
        'description' => __('Artist custom fields', SW_ARTISTS_TEXT_DOMAIN),
        'fields' => [
            'order' => [
                'type' => 'Integer',
                'description' => __('Display order', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'instagram' => [
                'type' => 'String',
                'description' => __('Instagram URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'tiktok' => [
                'type' => 'String',
                'description' => __('TikTok URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'spotify' => [
                'type' => 'String',
                'description' => __('Spotify URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'youtube' => [
                'type' => 'String',
                'description' => __('YouTube URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'facebook' => [
                'type' => 'String',
                'description' => __('Facebook URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'twitter' => [
                'type' => 'String',
                'description' => __('Twitter/X URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
            'appleMusic' => [
                'type' => 'String',
                'description' => __('Apple Music URL', SW_ARTISTS_TEXT_DOMAIN),
            ],
        ],
    ]);

    // Register field on SwArtist post type
    register_graphql_field('SwArtist', 'swArtistFields', [
        'type' => 'SwArtistFields',
        'description' => __('Artist custom fields', SW_ARTISTS_TEXT_DOMAIN),
        'resolve' => function ($post) {
            // Get menu_order from the post object
            $post_object = get_post($post->ID);
            $order = $post_object ? $post_object->menu_order : 0;

            $instagram = get_post_meta($post->ID, '_artist_instagram', true);
            $tiktok = get_post_meta($post->ID, '_artist_tiktok', true);
            $spotify = get_post_meta($post->ID, '_artist_spotify', true);
            $youtube = get_post_meta($post->ID, '_artist_youtube', true);
            $facebook = get_post_meta($post->ID, '_artist_facebook', true);
            $twitter = get_post_meta($post->ID, '_artist_twitter', true);
            $apple_music = get_post_meta($post->ID, '_artist_apple_music', true);

            return [
                'order' => absint($order),
                'instagram' => $instagram ?: '',
                'tiktok' => $tiktok ?: '',
                'spotify' => $spotify ?: '',
                'youtube' => $youtube ?: '',
                'facebook' => $facebook ?: '',
                'twitter' => $twitter ?: '',
                'appleMusic' => $apple_music ?: '',
            ];
        },
    ]);
}
add_action('graphql_register_types', 'sw_artists_register_graphql_fields');

/**
 * Add custom categorySlug filter to WPGraphQL
 */
function sw_artists_register_graphql_category_filter()
{
    // Only register if WPGraphQL is active
    if (!class_exists('WPGraphQL')) {
        return;
    }

    // Register custom where arg for category slug
    add_filter('graphql_input_fields', function ($fields, $type_name) {
        if ($type_name === 'RootQueryToSwArtistConnectionWhereArgs') {
            $fields['categorySlug'] = [
                'type' => 'String',
                'description' => __('Filter by artist category slug', SW_ARTISTS_TEXT_DOMAIN),
            ];
        }
        return $fields;
    }, 10, 2);

    // Apply the category filter to the query
    add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {
        // Check if we're querying swArtists and categorySlug is provided
        if ($info->fieldName === 'swArtists' && isset($args['where']['categorySlug'])) {
            $category_slug = sanitize_text_field($args['where']['categorySlug']);

            // Add tax_query to filter by category
            if (!isset($query_args['tax_query'])) {
                $query_args['tax_query'] = [];
            }

            $query_args['tax_query'][] = [
                'taxonomy' => 'artist_category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ];
        }
        return $query_args;
    }, 10, 5);
}
add_action('graphql_register_types', 'sw_artists_register_graphql_category_filter');

/**
 * Flush rewrite rules on activation
 */
function sw_artists_activate()
{
    sw_artists_register_cpt();
    sw_artists_register_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sw_artists_activate');

/**
 * Flush rewrite rules on deactivation
 */
function sw_artists_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'sw_artists_deactivate');
