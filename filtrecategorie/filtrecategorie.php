<?php
/*
 * Plugin Name: FiltreCategorie
 * Description: Filtrer les résumés de destinations par catégorie via REST API.
 * Version: 1.0
 * Author: Laura-Jeanne Fournier Lanctôt
 * Author URI: https://www.referenced.ca
 */

// Enfilement des styles et scripts
function filtrecategorie_enqueue_assets() {
    if (is_front_page()) { // Charge uniquement sur la page d'accueil
        $css_file = plugin_dir_path(__FILE__) . 'assets/style.css';
        $js_file = plugin_dir_path(__FILE__) . 'assets/js/filtrecategorie.js';

        $version_css = file_exists($css_file) ? filemtime($css_file) : '1.0';
        $version_js = file_exists($js_file) ? filemtime($js_file) : '1.0';

        if (file_exists($css_file)) {
            wp_enqueue_style(
                'filtrecategorie-style',
                plugin_dir_url(__FILE__) . 'assets/style.css',
                array(),
                $version_css
            );
        } else {
            error_log('Le fichier CSS est manquant : ' . $css_file);
        }

        if (file_exists($js_file)) {
            wp_enqueue_script(
                'filtrecategorie-script',
                plugin_dir_url(__FILE__) . 'assets/js/filtrecategorie.js',
                array('jquery'),
                $version_js,
                true
            );
        } else {
            error_log('Le fichier JS est manquant : ' . $js_file);
        }

        // Localisation pour transmettre l’URL REST API
        wp_localize_script('filtrecategorie-script', 'filtrecategorie', array(
            'rest_url' => esc_url(rest_url('filtrecategorie/v1/destinations')),
            'nonce'    => wp_create_nonce('wp_rest')
        ));
    }
}
add_action('wp_enqueue_scripts', 'filtrecategorie_enqueue_assets');

// Génération du shortcode
function filtrecategorie_shortcode() {
    $categories = get_categories();
    ob_start();
    ?>
    <div class="filtrecategorie-container">
        <label for="filtrecategorie-selecteur">Filtrer par catégorie :</label>
        <select id="filtrecategorie-selecteur">
            <option value="all">Toutes les catégories</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo esc_attr($category->term_id); ?>">
                    <?php echo esc_html($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="filtrecategorie-results"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('filtre_categorie', 'filtrecategorie_shortcode');

// Création de la route REST API
add_action('rest_api_init', function () {
    register_rest_route('filtrecategorie/v1', '/destinations', array(
        'methods'  => 'GET',
        'callback' => 'filtrecategorie_get_destinations',
        'permission_callback' => '__return_true',
    ));
});

// Fonction de récupération des articles
function filtrecategorie_get_destinations($request) {
    $category_id = $request->get_param('category_id');

    $args = array(
        'post_type'    => 'post', 
        'post_status'  => 'publish',
        'numberposts'  => -1,
    );

    if ($category_id && $category_id !== 'all') {
        $args['category__in'] = array($category_id);
    }

    $posts = get_posts($args);
    $data = array();

    foreach ($posts as $post) {
        $data[] = array(
            'title'       => get_the_title($post->ID),
            'link'        => get_permalink($post->ID),
            'excerpt'     => wp_trim_words($post->post_content, 20, '...'),
            'thumbnail'   => get_the_post_thumbnail_url($post->ID, 'medium'),
        );
    }

    return rest_ensure_response($data);
}