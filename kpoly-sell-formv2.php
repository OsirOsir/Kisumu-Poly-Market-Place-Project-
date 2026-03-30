<?php
/**
 * Plugin Name: KPoly Sell Form
 * Plugin URI: https://kisumupolymarket.co.ke
 * Description: Frontend shortcode form for logged-in users to submit WooCommerce marketplace listings.
 * Version: 1.2.0
 * Author: KPoly Market
 * License: GPL2+
 * Text Domain: kpoly-sell-form
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('KPoly_Sell_Form')) {

    class KPoly_Sell_Form {

        const NONCE_ACTION = 'kpoly_sell_form_submit';
        const NONCE_NAME   = 'kpoly_sell_form_nonce';
        const SHORTCODE    = 'kpoly_sell_form';

        public function __construct() {
            add_shortcode(self::SHORTCODE, [$this, 'render_shortcode']);
            add_action('init', [$this, 'handle_form_submission']);
        }

        public function render_shortcode(): string {
            if (!class_exists('WooCommerce')) {
                return '<div class="kpoly-form-error">WooCommerce must be active for this form to work.</div>';
            }
			if (!is_user_logged_in()) {
				$login_url    = wc_get_page_permalink('myaccount');
				$register_url = wc_get_page_permalink('myaccount');

				return '
				<div class="kpoly-form-error">
					You must <a href="' . esc_url($login_url) . '">log in</a> or <a href="' . esc_url($register_url) . '">create an account</a> to sell an item.
				</div>';
			}

            $messages = $this->get_messages_html();
            $values   = $this->get_old_values();

            ob_start();
            ?>
            <div class="kpoly-sell-form-wrap">
				<div class="kpoly-form-loading-overlay" id="kpoly-form-loading-overlay">
					<div class="kpoly-form-loading-box">
						<div class="kpoly-form-loading-dots">
							<span></span><span></span><span></span>
						</div>
						<p>Submitting your item...</p>
					</div>
				</div>
                <?php echo $messages; ?>

                <form class="kpoly-sell-form" method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME); ?>
                    <input type="hidden" name="kpoly_sell_form_submitted" value="1">

                    <div class="kpoly-form-group">
                        <label for="kpoly_seller_name">Your Name *</label>
                        <input type="text" id="kpoly_seller_name" name="kpoly_seller_name" value="<?php echo esc_attr($values['kpoly_seller_name'] ?? ''); ?>" required>
                    </div>

                    <div class="kpoly-form-group">
                        <label for="kpoly_item_name">Item Name *</label>
                        <input type="text" id="kpoly_item_name" name="kpoly_item_name" value="<?php echo esc_attr($values['kpoly_item_name'] ?? ''); ?>" required>
                    </div>

                    <div class="kpoly-form-group">
                        <label for="kpoly_description">Description *</label>
                        <textarea id="kpoly_description" name="kpoly_description" rows="6" required><?php echo esc_textarea($values['kpoly_description'] ?? ''); ?></textarea>
                    </div>

                    <div class="kpoly-form-group">
                        <label for="kpoly_short_description">Short Description *</label>
                        <textarea id="kpoly_short_description" name="kpoly_short_description" rows="3" required><?php echo esc_textarea($values['kpoly_short_description'] ?? ''); ?></textarea>
                        <small>A short summary that appears in the product short description area.</small>
                    </div>

                    <div class="kpoly-form-group">
                        <label for="kpoly_product_tags">Tags</label>
                        <input type="text" id="kpoly_product_tags" name="kpoly_product_tags" value="<?php echo esc_attr($values['kpoly_product_tags'] ?? ''); ?>" placeholder="e.g. umbrella, red, hostel">
                        <small>Separate tags with commas.</small>
                    </div>

                    <div class="kpoly-form-row">
                        <div class="kpoly-form-group">
                            <label for="kpoly_price">Price (KES) *</label>
                            <input type="number" id="kpoly_price" name="kpoly_price" min="0" step="0.01" value="<?php echo esc_attr($values['kpoly_price'] ?? ''); ?>" required>
                        </div>

                        <div class="kpoly-form-group">
                            <label for="kpoly_category">Category *</label>
                            <select id="kpoly_category" name="kpoly_category" required>
                                <option value="">Select category</option>
                                <?php echo $this->render_category_options((int) ($values['kpoly_category'] ?? 0)); ?>
                            </select>
                        </div>
                    </div>

                    <div class="kpoly-form-row">
                        <div class="kpoly-form-group">
                            <label for="kpoly_condition">Condition *</label>
                            <select id="kpoly_condition" name="kpoly_condition" required>
                                <option value="">Select condition</option>
                                <?php
                                $conditions = [
                                    'New',
                                    'Used - Like New',
                                    'Used - Good',
                                    'Used - Fair',
                                ];
                                $selected_condition = $values['kpoly_condition'] ?? '';

                                foreach ($conditions as $condition) {
                                    printf(
                                        '<option value="%1$s" %2$s>%1$s</option>',
                                        esc_attr($condition),
                                        selected($selected_condition, $condition, false)
                                    );
                                }
                                ?>
                            </select>
                        </div>

                        <div class="kpoly-form-group">
                            <label for="kpoly_location">Location *</label>
                            <input type="text" id="kpoly_location" name="kpoly_location" value="<?php echo esc_attr($values['kpoly_location'] ?? ''); ?>" placeholder="e.g. Kisumu Polytechnic Hostels" required>
                        </div>
                    </div>

                    <div class="kpoly-form-group">
                        <label for="kpoly_whatsapp">WhatsApp Number *</label>
                        <input type="text" id="kpoly_whatsapp" name="kpoly_whatsapp" value="<?php echo esc_attr($values['kpoly_whatsapp'] ?? ''); ?>" placeholder="e.g. 2547XXXXXXXX" required>
                    </div>

                    <div class="kpoly-form-group">
                        <label for="kpoly_images">Upload Images *</label>
                        <input type="file" id="kpoly_images" name="kpoly_images[]" accept="image/jpeg,image/png,image/webp" multiple required>
                        <small>You can upload up to 5 images. JPG, PNG, or WEBP only.</small>
                    </div>

                    <div class="kpoly-form-group">
						<button type="submit" class="kpoly-submit-btn" id="kpoly-submit-btn">
							<span class="kpoly-submit-btn-text">Submit Listing</span>
							<span class="kpoly-submit-spinner" aria-hidden="true"></span>
						</button>
					</div>
                </form>
            </div>

            <style>
                .kpoly-sell-form-wrap {
                    max-width: 800px;
                    margin: 0 auto;
                }
                .kpoly-sell-form .kpoly-form-group {
                    margin-bottom: 18px;
                }
                .kpoly-sell-form label {
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                }
                .kpoly-sell-form input[type="text"],
                .kpoly-sell-form input[type="number"],
                .kpoly-sell-form input[type="file"],
                .kpoly-sell-form textarea,
                .kpoly-sell-form select {
                    width: 100%;
                    padding: 10px 12px;
                    border: 1px solid #d0d0d0;
                    border-radius: 4px;
                    box-sizing: border-box;
                }
                .kpoly-form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 18px;
                }
                .kpoly-submit-btn {
                    background: #f0a500;
                    color: #000;
                    border: none;
                    padding: 12px 20px;
                    font-weight: 700;
                    cursor: pointer;
                    border-radius: 4px;
                }
                .kpoly-form-success,
                .kpoly-form-error {
                    padding: 12px 14px;
                    margin-bottom: 20px;
                    border-radius: 4px;
                }
                .kpoly-form-success {
                    background: #e8f7e8;
                    color: #176117;
                    border: 1px solid #b9e0b9;
                }
                .kpoly-form-error {
                    background: #fdeaea;
                    color: #8a1f1f;
                    border: 1px solid #efc2c2;
                }
                .kpoly-form-error ul {
                    margin: 8px 0 0 18px;
                }
                @media (max-width: 767px) {
                    .kpoly-form-row {
                        grid-template-columns: 1fr;
                    }
                }
				.kpoly-sell-form-wrap {
					max-width: 800px;
					margin: 0 auto;
					position: relative;
				}

				.kpoly-submit-btn {
					background: #f0a500;
					color: #000;
					border: none;
					padding: 12px 20px;
					font-weight: 700;
					cursor: pointer;
					border-radius: 4px;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					gap: 10px;
					min-width: 180px;
				}

				.kpoly-submit-btn.is-loading {
					opacity: 0.85;
					cursor: not-allowed;
				}

				.kpoly-submit-spinner {
					width: 16px;
					height: 16px;
					border: 2px solid rgba(0,0,0,0.2);
					border-top-color: #000;
					border-radius: 50%;
					display: none;
					animation: kpoly-spin 0.8s linear infinite;
				}

				.kpoly-submit-btn.is-loading .kpoly-submit-spinner {
					display: inline-block;
				}

				.kpoly-form-loading-overlay {
					position: absolute;
					inset: 0;
					background: rgba(255,255,255,0.82);
					display: none;
					align-items: center;
					justify-content: center;
					z-index: 20;
					border-radius: 8px;
				}

				.kpoly-form-loading-overlay.active {
					display: flex;
				}

				.kpoly-form-loading-box {
					text-align: center;
					padding: 20px;
				}

				.kpoly-form-loading-box p {
					margin: 12px 0 0;
					font-weight: 600;
					color: #111827;
				}

				.kpoly-form-loading-dots {
					display: inline-flex;
					gap: 8px;
					align-items: center;
					justify-content: center;
				}

				.kpoly-form-loading-dots span {
					width: 10px;
					height: 10px;
					border-radius: 50%;
					background: #f0a500;
					animation: kpoly-bounce 1.2s infinite ease-in-out;
				}

				.kpoly-form-loading-dots span:nth-child(2) {
					animation-delay: 0.15s;
				}

				.kpoly-form-loading-dots span:nth-child(3) {
					animation-delay: 0.3s;
				}

				@keyframes kpoly-spin {
					to {
						transform: rotate(360deg);
					}
				}

				@keyframes kpoly-bounce {
					0%, 80%, 100% {
						transform: scale(0.7);
						opacity: 0.5;
					}
					40% {
						transform: scale(1);
						opacity: 1;
					}
				}
            </style>
			<script>
			document.addEventListener('DOMContentLoaded', function () {
				const form = document.querySelector('.kpoly-sell-form');
				const submitBtn = document.getElementById('kpoly-submit-btn');
				const overlay = document.getElementById('kpoly-form-loading-overlay');

				if (!form || !submitBtn || !overlay) return;

				form.addEventListener('submit', function () {
					submitBtn.classList.add('is-loading');
					submitBtn.disabled = true;

					const btnText = submitBtn.querySelector('.kpoly-submit-btn-text');
					if (btnText) {
						btnText.textContent = 'Submitting...';
					}

					overlay.classList.add('active');

					const fields = form.querySelectorAll('input, textarea, select, button');
					fields.forEach(function (field) {
						field.setAttribute('readonly', 'readonly');
					});
				});
			});
			</script>
            <?php
			
            return ob_get_clean();
        }

        public function handle_form_submission(): void {
            if (
                !isset($_POST['kpoly_sell_form_submitted']) ||
                $_POST['kpoly_sell_form_submitted'] !== '1'
            ) {
                return;
            }

            if (!class_exists('WooCommerce')) {
                $this->redirect_with_error(['WooCommerce is not active.']);
            }
			if (!is_user_logged_in()) {
				$this->redirect_with_error(['You must be logged in to submit a listing.']);
			}
            if (
                !isset($_POST[self::NONCE_NAME]) ||
                !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])), self::NONCE_ACTION)
            ) {
                $this->redirect_with_error(['Security check failed. Please try again.']);
            }

            $data   = $this->sanitize_form_data($_POST);
            $errors = $this->validate_form_data($data, $_FILES);

            if (!empty($errors)) {
                $this->redirect_with_error($errors, $data);
            }

            $product_id = $this->create_user_product($data);

            if (is_wp_error($product_id) || !$product_id) {
                $this->redirect_with_error(['Could not create listing. Please try again later.'], $data);
            }

            $upload_result = $this->handle_image_uploads($product_id, $_FILES);

            if (is_wp_error($upload_result)) {
                wp_delete_post($product_id, true);
                $this->redirect_with_error([$upload_result->get_error_message()], $data);
            }

            $this->redirect_with_success('Your item is now live on KPoly Market!');
        }

        private function sanitize_form_data(array $post_data): array {
            return [
                'kpoly_seller_name'       => sanitize_text_field(wp_unslash($post_data['kpoly_seller_name'] ?? '')),
                'kpoly_item_name'         => sanitize_text_field(wp_unslash($post_data['kpoly_item_name'] ?? '')),
                'kpoly_description'       => sanitize_textarea_field(wp_unslash($post_data['kpoly_description'] ?? '')),
                'kpoly_short_description' => sanitize_textarea_field(wp_unslash($post_data['kpoly_short_description'] ?? '')),
                'kpoly_product_tags'      => sanitize_text_field(wp_unslash($post_data['kpoly_product_tags'] ?? '')),
                'kpoly_price'             => sanitize_text_field(wp_unslash($post_data['kpoly_price'] ?? '')),
                'kpoly_category'          => absint($post_data['kpoly_category'] ?? 0),
                'kpoly_condition'         => sanitize_text_field(wp_unslash($post_data['kpoly_condition'] ?? '')),
                'kpoly_location'          => sanitize_text_field(wp_unslash($post_data['kpoly_location'] ?? '')),
                'kpoly_whatsapp'          => sanitize_text_field(wp_unslash($post_data['kpoly_whatsapp'] ?? '')),
            ];
        }

        private function validate_form_data(array $data, array $files): array {
            $errors = [];

            if ($data['kpoly_seller_name'] === '') {
                $errors[] = 'Your name is required.';
            }

            if ($data['kpoly_item_name'] === '') {
                $errors[] = 'Item name is required.';
            }

            if ($data['kpoly_description'] === '') {
                $errors[] = 'Description is required.';
            }

            if ($data['kpoly_short_description'] === '') {
                $errors[] = 'Short description is required.';
            }

            if ($data['kpoly_price'] === '' || !is_numeric($data['kpoly_price']) || (float) $data['kpoly_price'] < 0) {
                $errors[] = 'Please enter a valid price.';
            }

            $allowed_slugs = [
    'electronics',
    'fashion',
    'phones-tablets',
    'bags-accessories',
    'hostel-room-items',
    'health-beauty',
    'sports-fitness',
    'services',
    'jobs-gigs',
    'other-listings',
    'books-stationery',
    'sport-outdoor',
    'kitchen-accessories',
];

$term = get_term($data['kpoly_category'], 'product_cat');

if (
    empty($data['kpoly_category']) ||
    !$term ||
    is_wp_error($term) ||
    !in_array($term->slug, $allowed_slugs, true)
) {
    $errors[] = 'Please select a valid category.';
}

            $allowed_conditions = [
                'New',
                'Used - Like New',
                'Used - Good',
                'Used - Fair',
            ];

            if (!in_array($data['kpoly_condition'], $allowed_conditions, true)) {
                $errors[] = 'Please select a valid condition.';
            }

            if ($data['kpoly_location'] === '') {
                $errors[] = 'Location is required.';
            }

            if ($data['kpoly_whatsapp'] === '') {
                $errors[] = 'WhatsApp number is required.';
            }

            if (!preg_match('/^[0-9+\s()-]{7,20}$/', $data['kpoly_whatsapp'])) {
                $errors[] = 'Please enter a valid WhatsApp number.';
            }

            if (
                !isset($files['kpoly_images']) ||
                empty($files['kpoly_images']['name'][0])
            ) {
                $errors[] = 'Please upload at least one image.';
            }

            if (isset($files['kpoly_images']['name']) && is_array($files['kpoly_images']['name'])) {
                $count = count(array_filter($files['kpoly_images']['name']));
                if ($count > 5) {
                    $errors[] = 'You can upload a maximum of 5 images.';
                }
            }

            return $errors;
        }

        private function create_user_product(array $data) {
            $full_description = $this->build_product_description($data);

            $product_id = wp_insert_post([
			'post_title'   => $data['kpoly_item_name'],
			'post_content' => $full_description,
			'post_excerpt' => $data['kpoly_short_description'],
			'post_status'  => 'publish',
			'post_type'    => 'product',
			'post_author'  => get_current_user_id(),
		], true);

            if (is_wp_error($product_id)) {
                return $product_id;
            }

            wp_set_object_terms($product_id, 'simple', 'product_type');
            wp_set_object_terms($product_id, [$data['kpoly_category']], 'product_cat');

            if (!empty($data['kpoly_product_tags'])) {
                $tags_array = array_filter(array_map('trim', explode(',', $data['kpoly_product_tags'])));
                if (!empty($tags_array)) {
                    wp_set_object_terms($product_id, $tags_array, 'product_tag');
                }
            }

            update_post_meta($product_id, '_regular_price', wc_format_decimal($data['kpoly_price']));
            update_post_meta($product_id, '_price', wc_format_decimal($data['kpoly_price']));
            update_post_meta($product_id, '_kpoly_seller_name', $data['kpoly_seller_name']);
            update_post_meta($product_id, '_kpoly_condition', $data['kpoly_condition']);
            update_post_meta($product_id, '_kpoly_location', $data['kpoly_location']);
            update_post_meta($product_id, '_kpoly_whatsapp', $data['kpoly_whatsapp']);

            return $product_id;
        }

        private function build_product_description(array $data): string {
            $parts = [];

            $parts[] = wp_kses_post($data['kpoly_description']);
            $parts[] = '';
            $parts[] = '👤 Posted by: ' . esc_html($data['kpoly_seller_name']);
            $parts[] = '📍 Location: ' . esc_html($data['kpoly_location']);
            $parts[] = '📦 Condition: ' . esc_html($data['kpoly_condition']);
            $parts[] = '💬 Contact: ' . esc_html($data['kpoly_whatsapp']) . ' (Call/WhatsApp)';

            return implode("\n", $parts);
        }

        private function handle_image_uploads(int $product_id, array $files) {
            if (
                !isset($files['kpoly_images']) ||
                !is_array($files['kpoly_images']['name'])
            ) {
                return new WP_Error('kpoly_no_images', 'No images were uploaded.');
            }

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $allowed_mimes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            $attachment_ids = [];

            foreach ($files['kpoly_images']['name'] as $index => $name) {
                if (empty($name)) {
                    continue;
                }

                $single_file = [
                    'name'     => $files['kpoly_images']['name'][$index],
                    'type'     => $files['kpoly_images']['type'][$index],
                    'tmp_name' => $files['kpoly_images']['tmp_name'][$index],
                    'error'    => $files['kpoly_images']['error'][$index],
                    'size'     => $files['kpoly_images']['size'][$index],
                ];

                if (!in_array($single_file['type'], $allowed_mimes, true)) {
                    return new WP_Error('kpoly_invalid_image', 'Only JPG, PNG, and WEBP images are allowed.');
                }

                $_FILES['kpoly_single_upload'] = $single_file;
                $attachment_id = media_handle_upload('kpoly_single_upload', $product_id);

                if (is_wp_error($attachment_id)) {
                    return $attachment_id;
                }

                $attachment_ids[] = (int) $attachment_id;
            }

            if (empty($attachment_ids)) {
                return new WP_Error('kpoly_upload_failed', 'Image upload failed.');
            }

            set_post_thumbnail($product_id, $attachment_ids[0]);

            if (count($attachment_ids) > 1) {
                $gallery_ids = array_slice($attachment_ids, 1);
                update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));
            }

            return true;
        }

        private function render_category_options(int $selected = 0): string {
    $allowed_slugs = [
        'electronics',
        'fashion',
        'phones-tablets',
        'bags-accessories',
        'hostel-room-items',
        'health-beauty',
        'sports-fitness',
        'services',
        'jobs-gigs',
        'other-listings',
        'books-stationery',
        'sport-outdoor',
        'kitchen-accessories',
    ];

    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'slug'       => $allowed_slugs,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    $ordered_terms = [];
    foreach ($allowed_slugs as $slug) {
        foreach ($terms as $term) {
            if ($term->slug === $slug) {
                $ordered_terms[] = $term;
                break;
            }
        }
    }

    $output = '';

    foreach ($ordered_terms as $term) {
        $output .= sprintf(
            '<option value="%1$d" %2$s>%3$s</option>',
            (int) $term->term_id,
            selected($selected, (int) $term->term_id, false),
            esc_html($term->name)
        );
    }

    return $output;
}

        private function redirect_with_error(array $errors, array $old_values = []): void {
            $url = $this->get_redirect_url();

            $payload = [
                'kpoly_form_status' => 'error',
                'kpoly_form_errors' => rawurlencode(wp_json_encode($errors)),
                'kpoly_old_values'  => rawurlencode(wp_json_encode($old_values)),
            ];

            wp_safe_redirect(add_query_arg($payload, $url));
            exit;
        }

        private function redirect_with_success(string $message): void {
            $url = $this->get_redirect_url();

            $payload = [
                'kpoly_form_status'  => 'success',
                'kpoly_form_message' => rawurlencode($message),
            ];

            wp_safe_redirect(add_query_arg($payload, $url));
            exit;
        }

        private function get_redirect_url(): string {
            $referer = wp_get_referer();

            if (!$referer) {
                $referer = home_url('/');
            }

            return remove_query_arg([
                'kpoly_form_status',
                'kpoly_form_errors',
                'kpoly_old_values',
                'kpoly_form_message',
            ], $referer);
        }

        private function get_messages_html(): string {
            $status  = sanitize_text_field(wp_unslash($_GET['kpoly_form_status'] ?? ''));
            $message = sanitize_text_field(wp_unslash($_GET['kpoly_form_message'] ?? ''));

            if ($status === 'success' && $message !== '') {
                return '<div class="kpoly-form-success">' . esc_html($message) . '</div>';
            }

            if ($status === 'error' && !empty($_GET['kpoly_form_errors'])) {
                $errors_json = wp_unslash($_GET['kpoly_form_errors']);
                $errors = json_decode(rawurldecode($errors_json), true);

                if (is_array($errors) && !empty($errors)) {
                    $html = '<div class="kpoly-form-error"><strong>Please fix the following:</strong><ul>';
                    foreach ($errors as $error) {
                        $html .= '<li>' . esc_html($error) . '</li>';
                    }
                    $html .= '</ul></div>';
                    return $html;
                }
            }

            return '';
        }

        private function get_old_values(): array {
            if (empty($_GET['kpoly_old_values'])) {
                return [];
            }

            $old_json = wp_unslash($_GET['kpoly_old_values']);
            $old = json_decode(rawurldecode($old_json), true);

            return is_array($old) ? $old : [];
        }
    }

    new KPoly_Sell_Form();
}
