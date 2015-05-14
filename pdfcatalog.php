<?php
define( 'PDF_CATALOG_PLUGIN_NAME', 'PDF Catalog' );
define( 'PDF_CATALOG_VERSION', '1.0.4' );
define( 'PDF_CATALOG__MINIMUM_WP_VERSION', '3.1' );
define( 'PDF_CATALOG__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PDF_CATALOG__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/*
Plugin Name: PDF Catalog
Plugin URI: http://www.ovologics.com
Description: Generates a PDF catalog of products (WooCommerce)
Version: 1.0.4
Author: ovologics
Author URI: http://www.ovologics.com
WC requires at least: 2.2
WC tested up to: 2.3
*/

/*  Copyright 2015  OVOLogics  (email: E-MAIL_юбрнпю)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
add_action('admin_menu', 'add_pdfcatalog_menu');
add_action('wp_ajax_pdfcatalog', array('PDFCatalog', 'display'));

// action function for above hook
function add_pdfcatalog_menu() {
    // Add a new top-level menu (ill-advised):
    add_menu_page(PDF_CATALOG_PLUGIN_NAME, PDF_CATALOG_PLUGIN_NAME, 'edit_pages', 'pdfcatalog', array( 'PDFCatalog', 'display'));
}

if ( is_admin() ) {
    require_once( PDF_CATALOG__PLUGIN_DIR . 'model.php' );
    
    if(!empty($_POST['getpdf']) || !empty($_POST['pdf'])){
        require_once( PDF_CATALOG__PLUGIN_DIR . 'pdf.php' );
    }else{
        require_once( PDF_CATALOG__PLUGIN_DIR . 'pdf_catalog.php' );
    }
}
?>