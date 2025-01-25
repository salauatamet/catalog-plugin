<?php
/**
 * Plugin Name: Catalog Plugin
 * Description: Лёгкий плагин для каталога товаров с интеграцией Elementor.
 * Version: 1.0.0
 * Author: Salauat Ametov
 */

if (!defined('ABSPATH')) {
    exit; // Запрет прямого доступа.
}

// Подключение файлов плагина, если они существуют.
$includes = [
    'includes/register-cpt.php',
    'includes/register-metaboxes.php',
    'includes/shortcodes.php',
];
// Подключение файла с регистрацией таксономий.
require_once plugin_dir_path(__FILE__) . 'includes/register-taxonomies.php';
require_once plugin_dir_path(__FILE__) . 'includes/single-product-template.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

foreach ($includes as $file) {
    if (file_exists(plugin_dir_path(__FILE__) . $file)) {
        include_once plugin_dir_path(__FILE__) . $file;
    }
}

// Хуки активации/деактивации.
register_activation_hook(__FILE__, 'catalog_plugin_activate');
register_deactivation_hook(__FILE__, 'catalog_plugin_deactivate');

// Функции активации и деактивации.
function catalog_plugin_activate() {
    // Регистрация пользовательского типа записи.
    if (function_exists('catalog_register_cpt')) {
        catalog_register_cpt();
    }
    flush_rewrite_rules();
}

function catalog_plugin_deactivate() {
    flush_rewrite_rules();
}
