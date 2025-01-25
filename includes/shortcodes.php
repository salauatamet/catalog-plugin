<?php
// Шорткод для отображения товаров.
function catalog_products_shortcode($atts) {
    // Атрибуты шорткода.
    $atts = shortcode_atts([
        'category' => '', // Фильтрация по категории (необязательно).
    ], $atts, 'catalog_products');

    // Параметры WP_Query.
    $args = [
        'post_type' => 'catalog_product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ];

    if (!empty($atts['category'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'catalog_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ],
        ];
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();
        echo '<div class="catalog-products">';
        while ($query->have_posts()) {
            $query->the_post();

            $price = get_post_meta(get_the_ID(), '_catalog_price', true);
            $status = get_post_meta(get_the_ID(), '_catalog_status', true);
            $delivery_info = get_post_meta(get_the_ID(), '_catalog_delivery_info', true);

            ?>
            <div class="catalog-product">
                <div class="img-thumbnail"><?php the_post_thumbnail('full'); ?></div>
                <h2><?php the_title(); ?></h2>
                <p><?php the_content(); ?></p>
                <p><strong>Цена:</strong> <?php echo esc_html($price); ?> KZT</p>
                <p><strong>Статус:</strong> <?php echo esc_html(catalog_get_status_label($status)); ?></p>
                <p><strong>Доставка:</strong> <?php echo esc_html($delivery_info); ?></p>
            </div>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();

        return ob_get_clean();
    } else {
        return '<p>Товары не найдены.</p>';
    }
}
add_shortcode('catalog_products', 'catalog_products_shortcode');

// Вспомогательная функция для статусов.
function catalog_get_status_label($status) {
    $labels = [
        'in_stock' => 'В наличии',
        'out_of_stock' => 'Нет в наличии',
        'on_order' => 'Под заказ',
    ];
    return $labels[$status] ?? 'Неизвестно';
}
