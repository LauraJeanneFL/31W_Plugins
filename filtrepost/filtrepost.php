<?php
/*
 * Plugin Name: FiltrePost
 * Description: Une extension pour filtrer les articles par catégories.
 * Version: 1.0
 * Author: Laura-Jeanne Fournier Lanctôt
 * Author uri: https://www.referenced.ca
*/

// Enfilement des styles et scripts
function filtrepost_enqueue_assets() {
    $version_css = filemtime(plugin_dir_path(__FILE__) . 'css/style.css');
    $version_js = filemtime(plugin_dir_path(__FILE__) . 'js/filtrepost.js');

    wp_enqueue_style(
        'filtrepost-style',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        $version_css
    );

    wp_enqueue_script(
        'filtrepost-script',
        plugin_dir_url(__FILE__) . 'js/filtrepost.js',
        array('jquery'),
        $version_js,
        true
    );

    // Localisation pour transmettre les données nécessaires à JavaScript
    wp_localize_script('filtrepost-script', 'filtrepost', array(
        'rest_url' => esc_url(rest_url('filtrepost/v1/articles')),
        'nonce'    => wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts', 'filtrepost_enqueue_assets');

// Génération des boutons de filtre
function filtrepost_generate_buttons() {
    $categories = get_categories();
    $buttons = '<div class="filtrepost-buttons">';
    foreach ($categories as $category) {
        $buttons .= '<button class="filtrepost-button" data-id="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</button>';
    }
    $buttons .= '</div>';
    $buttons .= '<div id="filtrepost-results"></div>';
    return $buttons;
}
add_shortcode('filtrepost', 'filtrepost_generate_buttons');

// Création de la route REST API
add_action('rest_api_init', function () {
    register_rest_route('filtrepost/v1', '/articles', array(
        'methods'  => 'GET',
        'callback' => 'filtrepost_get_articles',
        'permission_callback' => '__return_true'
    ));
});

// Fonction de récupération des articles
function filtrepost_get_articles($request) {
    $category_id = $request->get_param('category_id');
    if (!$category_id) {
        return new WP_Error('invalid_category', 'Catégorie invalide.', array('status' => 400));
    }

    $args = array(
        'category__in' => array($category_id),
        'post_type'    => 'post',
        'post_status'  => 'publish',
        'numberposts'  => -1,
    );

    $posts = get_posts($args);
    $data = array();

    foreach ($posts as $post) {
        $data[] = array(
            'title' => $post->post_title,
            'link'  => get_permalink($post->ID),
        );
    }

    return rest_ensure_response($data);
}