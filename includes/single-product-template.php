<?php
if (!defined('ABSPATH')) {
    exit;
}

// Шорткод для отображения страницы товара.
function catalog_single_product_shortcode($atts) {
    // Проверка на переданный ID товара.
    if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
        return '<p>Товар не найден.</p>';
    }

    $product_id = intval($_GET['product_id']);
    $product = get_post($product_id);

    if (!$product || $product->post_type !== 'catalog_product') {
        return '<p>Товар не найден.</p>';
    }

    // Получение метаданных товара.
    $price = get_post_meta($product_id, '_catalog_price', true);
    $status = get_post_meta($product_id, '_catalog_status', true);
    $delivery_info = get_post_meta($product_id, '_catalog_delivery_info', true);
    $features = get_post_meta($product_id, '_catalog_features', true);
    $categories = wp_get_post_terms($product_id, 'catalog_category', ['fields' => 'names']);

    ob_start();
    ?>
    <div class="catalog-single-product">
        <div class="product-gallery">
            <?php if (has_post_thumbnail($product_id)) : ?>
                <div class="main-image">
                    <?php echo get_the_post_thumbnail($product_id, 'large'); ?>
                </div>
            <?php endif; ?>

            <div class="thumbnails">
                <?php
                $gallery = get_post_meta($product_id, '_product_image_gallery', true);
                if ($gallery) {
                    $images = explode(',', $gallery);
                    foreach ($images as $image_id) {
                        echo wp_get_attachment_image($image_id, 'thumbnail');
                    }
                }
                ?>
            </div>
        </div>

        <div class="product-details">
            <h1><?php echo esc_html($product->post_title); ?></h1>
            <p><?php echo esc_html($product->post_content); ?></p>
            <p><strong>Цена:</strong> <?php echo esc_html($price); ?> KZT</p>
            <p><strong>Статус:</strong> <?php echo esc_html(catalog_get_status_label($status)); ?></p>
            <p><strong>Доставка:</strong> <?php echo esc_html(catalog_get_delivery_label($delivery_info)); ?></p>
            <p><strong>Категории:</strong> <?php echo implode(', ', $categories); ?></p>

            <?php if (!empty($features)) : ?>
                <p><strong>Характеристики:</strong></p>
                <ul>
                    <?php
                    $features_list = explode(',', $features);
                    foreach ($features_list as $feature) {
                        echo '<li>' . esc_html(trim($feature)) . '</li>';
                    }
                    ?>
                </ul>
            <?php endif; ?>

            <a href="https://wa.me/<?php echo esc_attr(get_option('catalog_whatsapp_number', '')); ?>?text=Здравствуйте! Я хочу заказать товар: <?php echo urlencode($product->post_title); ?>" class="button whatsapp-button">Заказать через WhatsApp</a>
        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('catalog_single_product', 'catalog_single_product_shortcode');

// Вспомогательная функция для доставки.
function catalog_get_delivery_label($delivery) {
    $labels = [
        'pickup' => 'Самовывоз',
        'free_delivery' => 'Бесплатная доставка',
    ];
    return $labels[$delivery] ?? 'Нет';
}
