<?php
if (!defined('ABSPATH')) {
    exit;
}

// Регистрация меню "Каталог товаров" в админке.
function catalog_register_admin_menu() {
    // Основное меню "Каталог товаров".
    add_menu_page(
        'Каталог товаров',            // Название страницы
        'Каталог товаров',            // Название меню
        'manage_options',             // Уровень доступа
        'catalog_overview',           // Слаг для меню
        'catalog_overview_callback',  // Функция отображения
        'dashicons-products',         // Иконка
        25                            // Позиция в меню
    );

    // Подменю: Настройки каталога.
    add_submenu_page(
        'catalog_overview',           // Родительское меню
        'Настройки каталога',         // Название страницы
        'Настройки',                  // Название подменю
        'manage_options',             // Уровень доступа
        'catalog_settings',           // Слаг для подменю
        'catalog_settings_page'       // Функция отображения
    );

    // Подменю: Список товаров.
    add_submenu_page(
        'catalog_overview',          // Родительское меню
        'Список товаров',            // Название страницы
        'Список товаров',            // Название подменю
        'manage_options',            // Уровень доступа
        'catalog_products_list',     // Слаг для подменю
        'catalog_products_list_page' // Функция отображения
    );
}
add_action('admin_menu', 'catalog_register_admin_menu');

// Callback для страницы "Обзор каталога".
function catalog_overview_callback() {
    echo '<div class="wrap">';
    echo '<h1>Каталог товаров</h1>';
    echo '<p>Добро пожаловать в управление каталогом товаров. Используйте меню слева для настройки и управления.</p>';
    echo '</div>';
}

// Callback для страницы "Настройки каталога".
function catalog_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>Настройки каталога</h1>';
    echo '<p>Настройте каталог товаров, включая номер WhatsApp, способы доставки и другие параметры.</p>';
    do_settings_sections('catalog_settings');
    echo '</div>';
}

// Callback для подменю "Список товаров".
function catalog_products_list_page() {
    $args = [
        'post_type'      => 'catalog_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];
    $query = new WP_Query($args);

    echo '<div class="wrap">';
    echo '<h1>Список товаров</h1>';
    echo '<table class="widefat fixed" style="width: 100%;">';
    echo '<thead>
        <tr>
            <th>Название</th>
            <th>Цена</th>
            <th>Категория</th>
            <th>Статус</th>
        </tr>
    </thead>';
    echo '<tbody>';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $price = get_post_meta(get_the_ID(), '_catalog_price', true);
            $status = get_post_meta(get_the_ID(), '_catalog_status', true);
            $categories = wp_get_post_terms(get_the_ID(), 'catalog_category', ['fields' => 'names']);

            echo '<tr>';
            echo '<td><a href="' . get_edit_post_link() . '">' . get_the_title() . '</a></td>';
            echo '<td>' . esc_html($price) . ' KZT</td>';
            echo '<td>' . implode(', ', $categories) . '</td>';
            echo '<td>' . esc_html(catalog_get_status_label($status)) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4">Товары не найдены.</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    wp_reset_postdata();
}

// Регистрация таксономии для категорий товаров.
function catalog_register_taxonomies() {
    $labels = [
        'name'              => 'Категории',
        'singular_name'     => 'Категория',
        'search_items'      => 'Искать категории',
        'all_items'         => 'Все категории',
        'parent_item'       => 'Родительская категория',
        'parent_item_colon' => 'Родительская категория:',
        'edit_item'         => 'Редактировать категорию',
        'update_item'       => 'Обновить категорию',
        'add_new_item'      => 'Добавить новую категорию',
        'new_item_name'     => 'Новое имя категории',
        'menu_name'         => 'Категории',
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'catalog_category'],
    ];

    register_taxonomy('catalog_category', 'catalog_product', $args);
}
add_action('init', 'catalog_register_taxonomies');
