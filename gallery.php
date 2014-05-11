<?php
/**
 * @package KK_My_Gallery
 */
/*
Plugin Name: KK_Gallery
Plugin URI: brak
Description: brak
Version: 0.0.1
Author: Kamil Zoń and Konrad Plinta
Author URI: brak
License: GPLv2 or later
Text Domain: brak
*/

/*Funkcja inicjalizuje tworzenie wszystkich potrzebnych pluginowi danych do działania
 *
 */

register_activation_hook( __FILE__, function() {
    add_option('Activated_Plugin', 'Plugin-KK_Gallery');
    /* activation code here */
    kk_create_all_tables();
    kk_create_folder();
});

/*
 * Funkcja usówa wszystkie dane wtyczki
 */

register_deactivation_hook(__FILE__, function(){
    delete_option('Activated_Plugin');

    kk_delete_all_tables();
});

/* Funkcja tworzy niezbędne tabele w bazue danych
 *
 */

global $gallery_db_version;
$gallery_db_version = "1.0";

function kk_create_all_tables(){
    global $wpdb;
    global $gallery_db_version;

    $table_name = $wpdb->prefix . "kk_gallery";

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      name tinytext NOT NULL,
      text text NOT NULL,
      url_img text DEFAULT '' NOT NULL,
      UNIQUE KEY id (id)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option("gallery_db_version", $gallery_db_version);
}

/*
 * Funkcja usówa wszystkie tabele z bazy danych
 */

function kk_delete_all_tables(){
    global $wpdb;

    $table_name = $wpdb->prefix . "kk_gallery";

    $sql = "DROP TABLE moja_strona $table_name;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    delete_option("gallery_db_version");
}

/*
 * Funkcja tworzy niezbędne foldery
 */

function kk_create_folder(){
    $patch = "kk-gallery/gallery-img";
    if(!is_dir($patch)){mkdir($patch);}
}

/*W tej funkcji sprawdzamy czy plugin jest atywowany i działamy na nim :)
 *
 */
add_action('admin_init','kk_load_plugin');
function kk_load_plugin() {
    if(is_admin()&&get_option('Activated_Plugin')=='Plugin-Slug') {
        delete_option('Activated_Plugin');
        /* do some stuff once right after activation */
    }
}

/* Funkcja tworzy manu w panelu admina
 *
 */
if (!function_exists('kk_register_my_custom_menu_page')) {
    add_action( 'admin_menu', 'kk_register_my_custom_menu_page' );

    function kk_register_my_custom_menu_page(){
        add_menu_page( 'KK_Gallery', 'KK_Gallery', 'manage_options', 'kk-gallery\menu\top_menu.php'); //plugins_url( 'myplugin/images/icon.png' )
        add_submenu_page('kk-gallery\menu\top_menu.php', 'SubMenu1', 'SubMenu1', 'manage_options', 'kk-gallery\menu\sub_menu_1.php');
        add_submenu_page('kk-gallery\menu\top_menu.php', 'SubMenu2', 'SubMenu2', 'manage_options', 'kk-gallery\menu\sub_menu_2.php');
    }
}

if (!function_exists('kk_gallery_shortcode')) {
    function kk_gallery_shortcode($args) {
        ob_start();

        ?>

        <div class="wrapper">
            <h1>Responsive Carousel</h1>


            <p>This example shows how to implement a responsive carousel. Resize the browser window to see the effect.</p>

            <div class="jcarousel-wrapper">
                <div class="jcarousel">
                    <ul>
                        <li><img src="https://raw.githubusercontent.com/jsor/jcarousel/master/examples/_shared/img/img1.jpg" alt="Image 1"></li>
                        <li><img src="https://raw.githubusercontent.com/jsor/jcarousel/master/examples/_shared/img/img2.jpg" alt="Image 2"></li>
                        <li><img src="https://raw.githubusercontent.com/jsor/jcarousel/master/examples/_shared/img/img3.jpg" alt="Image 3"></li>
                        <li><img src="https://raw.githubusercontent.com/jsor/jcarousel/master/examples/_shared/img/img4.jpg" alt="Image 4"></li>
                        <li><img src="https://raw.githubusercontent.com/jsor/jcarousel/master/examples/_shared/img/img5.jpg" alt="Image 5"></li>
                        <li><img src="https://raw.githubusercontent.com/jsor/jcarousel/master/examples/_shared/img/img6.jpg" alt="Image 6"></li>
                    </ul>
                </div>

                <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                <a href="#" class="jcarousel-control-next">&rsaquo;</a>

                <p class="jcarousel-pagination"></p>
            </div>
        </div>


        <?php
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    add_shortcode('kk_gallery', 'kk_gallery_shortcode');
}