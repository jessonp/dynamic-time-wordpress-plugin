<?php
/*
Plugin Name: Dynamic Timeline
Description: A plugin to create a dynamic timeline with add, update, delete, and view functionality.
Version: 1.0
Author: Jesson Paloma
Url: https://jessonpaloma.com/
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Enqueue scripts and styles
function dt_enqueue_scripts() {
    wp_enqueue_style('dt-style', plugins_url('/style.css', __FILE__));
    wp_enqueue_script('dt-tooltip-js', plugins_url('/tooltip.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'dt_enqueue_scripts');

function dt_enqueue_admin_scripts($hook) {
    if ($hook != 'toplevel_page_dt-timeline-events') {
        return;
    }
    // Ensure unique handles and correct dependencies
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_style('quill-css', 'https://cdn.quilljs.com/1.3.6/quill.snow.css');
    wp_enqueue_script('quill-js', 'https://cdn.quilljs.com/1.3.6/quill.min.js', array(), null, true);
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/69779906ac.js', [], null, true);
    wp_enqueue_script('dt-admin-js', plugins_url('/admin.js', __FILE__), array('jquery', 'bootstrap-js', 'quill-js'), null, true);
}
add_action('admin_enqueue_scripts', 'dt_enqueue_admin_scripts');

function dt_enqueue_timeline_styles() {
    if (!is_admin()) {
        wp_enqueue_style('timeline-css', plugins_url('/timeline.css', __FILE__));
    }
}
add_action('wp_enqueue_scripts', 'dt_enqueue_timeline_styles');

// Create the database table on plugin activation
function dt_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'timeline_events';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        location tinytext NOT NULL,   /* Fixed the column declaration */
        year varchar(4) NOT NULL,
        description text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'dt_create_table');

// Include admin page
include plugin_dir_path(__FILE__) . 'admin-page.php';

// Create Shortcode to Display Timeline
function dt_timeline_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'timeline_events';
    $events = $wpdb->get_results("SELECT * FROM $table_name ORDER BY year ASC");

    if (empty($events)) {
        return '<p>No events found.</p>';
    }
    $output = '<div class="mekus';
    $output = '<div class="content d-flex justify-content-center" id="scrollable-content"> <div class="box1">';

    $x = 1;
    foreach ($events as $event) { 
        // Allow safe HTML within the description
        $description = wp_kses_post($event->description);
        
        // Encode the description as JSON to safely include it in the data attribute
        $description_json = htmlspecialchars(json_encode($description), ENT_QUOTES, 'UTF-8');
        
        $side = $x % 2 === 0 ? 'bottom' : 'top';
        $output .= '<div class="box1-content desktop-content ' . $side . '" data-description="' . $description_json . '">';
        $output .= '<span class="timeline-heading">' . esc_html($event->name) . '<br>'; 
        $output .=  esc_html($event->year) . '</span><br><br>'; 
        $output .= '<span class="timeline-location">(' . esc_html($event->location) . ')</span>'; 
        $output .= '</div>';

        $output .= '<div class="mobile-content mt-3">';
        $output .= '<span class="timeline-heading">' . esc_html($event->name) . '&nbsp;'  .  esc_html($event->year) . ' </span>' . '<span class="timeline-location text-capitalize"> - ' . esc_html($event->location) . '</span>' . '<br>'; 
        $output .= '<div class="mobile-description" style="margin-top:10px;">' . $description . '</div>';
    
        $output .= '</div>';
        
        $x++;
    }
    

    $output .= '<div class="center-line"></div>';
    $output .= '</div></div><div class="scroll-container"><div class="custom-scrollbar">
        <div id="scrollbar-thumb"></div></div>
    </div></div>';

    return $output;
}


add_shortcode('dt_timeline', 'dt_timeline_shortcode');
?>
