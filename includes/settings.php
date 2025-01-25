<?php
if (!defined('ABSPATH')) {
    exit;
}

// Добавление страницы настроек.
function catalog_add_settings_page() {
    add_options_page(
        'Настройки Каталога',
        'Настройки Каталога',
        'manage_options',
        'catalog-settings',
        'catalog_render_settings_page'
    );
}
add_action('admin_menu', 'catalog_add_settings_page');

// Рендер страницы настроек.
function catalog_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Настройки Каталога</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('catalog_settings_group');
            do_settings_sections('catalog-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Регистрация настроек.
function catalog_register_settings() {
    register_setting('catalog_settings_group', 'catalog_whatsapp_number');

    add_settings_section(
        'catalog_settings_section',
        'Основные настройки',
        null,
        'catalog-settings'
    );

    add_settings_field(
        'catalog_whatsapp_number',
        'Номер WhatsApp',
        'catalog_whatsapp_number_callback',
        'catalog-settings',
        'catalog_settings_section'
    );
}
add_action('admin_init', 'catalog_register_settings');

// Поле ввода номера WhatsApp.
function catalog_whatsapp_number_callback() {
    $number = get_option('catalog_whatsapp_number', '');
    echo '<input type="text" name="catalog_whatsapp_number" value="' . esc_attr($number) . '" placeholder="Введите номер WhatsApp (без +)">';
}
