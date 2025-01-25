<?php
if (!defined('ABSPATH')) {
    exit;
}

// Регистрация метабоксов.
function catalog_register_metaboxes() {
    add_meta_box(
        'catalog_product_data',
        'Данные товара',
        'catalog_product_data_callback',
        'catalog_product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'catalog_register_metaboxes');

function catalog_product_data_callback($post) {
    wp_nonce_field('catalog_save_product_data', 'catalog_product_nonce');

    $price = get_post_meta($post->ID, '_catalog_price', true);
    $status = get_post_meta($post->ID, '_catalog_status', true);
    $delivery_info = get_post_meta($post->ID, '_catalog_delivery_info', true);
    $features = get_post_meta($post->ID, '_catalog_features', true); // Новое поле

    ?>
    <p>
        <label for="catalog_price">Цена (KZT):</label>
        <input type="number" id="catalog_price" name="catalog_price" value="<?php echo esc_attr($price); ?>" />
    </p>
    <p>
        <label for="catalog_status">Статус:</label>
        <select id="catalog_status" name="catalog_status">
            <option value="in_stock" <?php selected($status, 'in_stock'); ?>>В наличии</option>
            <option value="out_of_stock" <?php selected($status, 'out_of_stock'); ?>>Нет в наличии</option>
            <option value="on_order" <?php selected($status, 'on_order'); ?>>Под заказ</option>
        </select>
    </p>
    <p>
        <label for="catalog_delivery_info">Информация о доставке:</label>
        <select id="catalog_delivery_info" name="catalog_delivery_info">
            <option value="" <?php selected($delivery_info, ''); ?>>Нет</option>
            <option value="pickup" <?php selected($delivery_info, 'pickup'); ?>>Самовывоз</option>
            <option value="free_delivery" <?php selected($delivery_info, 'free_delivery'); ?>>Бесплатная доставка</option>
        </select>
    </p>
    <p>
        <label for="catalog_features">Характеристики товара:</label>
        <input type="text" id="catalog_features" name="catalog_features" value="<?php echo esc_attr($features); ?>" placeholder="Введите характеристики, разделяя их запятыми или нажимая Enter." />
    </p>
    <?php
}

// Сохранение данных метабоксов.
function catalog_save_product_data($post_id) {
    if (!isset($_POST['catalog_product_nonce']) || !wp_verify_nonce($_POST['catalog_product_nonce'], 'catalog_save_product_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['catalog_price'])) {
        update_post_meta($post_id, '_catalog_price', sanitize_text_field($_POST['catalog_price']));
    }

    if (isset($_POST['catalog_status'])) {
        update_post_meta($post_id, '_catalog_status', sanitize_text_field($_POST['catalog_status']));
    }

    if (isset($_POST['catalog_delivery_info'])) {
        update_post_meta($post_id, '_catalog_delivery_info', sanitize_text_field($_POST['catalog_delivery_info']));
    }

    if (isset($_POST['catalog_features'])) { // Сохранение нового поля
        update_post_meta($post_id, '_catalog_features', sanitize_text_field($_POST['catalog_features']));
    }
}
add_action('save_post', 'catalog_save_product_data');

// Добавление метабокса для характеристик товара.
function catalog_add_features_metabox() {
    add_meta_box(
        'catalog_features_metabox',
        'Характеристики товара',
        'catalog_features_metabox_callback',
        'catalog_product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'catalog_add_features_metabox');

// Callback для вывода метабокса.
function catalog_features_metabox_callback($post) {
    wp_nonce_field('catalog_features_save', 'catalog_features_nonce');
    $features = get_post_meta($post->ID, '_catalog_features', true);
    ?>
    <label for="catalog_features">Введите характеристики товара (разделяйте запятыми или клавишей Enter):</label>
    <textarea id="catalog_features" name="catalog_features" rows="4" style="width:100%;"><?php echo esc_textarea($features); ?></textarea>
    <?php
}

// Сохранение характеристик товара.
function catalog_save_features_metabox($post_id) {
    if (!isset($_POST['catalog_features_nonce']) || !wp_verify_nonce($_POST['catalog_features_nonce'], 'catalog_features_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['catalog_features'])) {
        $features = sanitize_text_field($_POST['catalog_features']);
        update_post_meta($post_id, '_catalog_features', $features);
    }
}
add_action('save_post', 'catalog_save_features_metabox');

// Добавление метабокса для информации о доставке.
function catalog_add_delivery_metabox() {
    add_meta_box(
        'catalog_delivery_metabox',
        'Информация о доставке',
        'catalog_delivery_metabox_callback',
        'catalog_product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'catalog_add_delivery_metabox');

// Callback для вывода метабокса.
function catalog_delivery_metabox_callback($post) {
    wp_nonce_field('catalog_delivery_save', 'catalog_delivery_nonce');
    $delivery = get_post_meta($post->ID, '_catalog_delivery_info', true);
    ?>
    <label for="catalog_delivery_info">Выберите способ доставки:</label>
    <select id="catalog_delivery_info" name="catalog_delivery_info">
        <option value="" <?php selected($delivery, ''); ?>>Нет</option>
        <option value="pickup" <?php selected($delivery, 'pickup'); ?>>Самовывоз</option>
        <option value="free_delivery" <?php selected($delivery, 'free_delivery'); ?>>Бесплатная доставка</option>
    </select>
    <?php
}

// Сохранение информации о доставке.
function catalog_save_delivery_metabox($post_id) {
    if (!isset($_POST['catalog_delivery_nonce']) || !wp_verify_nonce($_POST['catalog_delivery_nonce'], 'catalog_delivery_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['catalog_delivery_info'])) {
        $delivery = sanitize_text_field($_POST['catalog_delivery_info']);
        update_post_meta($post_id, '_catalog_delivery_info', $delivery);
    }
}
add_action('save_post', 'catalog_save_delivery_metabox');