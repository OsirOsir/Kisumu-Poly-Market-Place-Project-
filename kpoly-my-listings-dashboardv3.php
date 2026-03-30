<?php
/**
 * Plugin Name: KPoly My Listings Dashboard
 * Plugin URI: https://kisumupolymarket.co.ke
 * Description: Frontend dashboard for logged-in users to view their own marketplace listings.
 * Version: 1.0.0
 * Author: KPoly Market
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('KPoly_My_Listings_Dashboard')) {

    class KPoly_My_Listings_Dashboard {

        public function __construct() {
			add_shortcode('kpoly_my_listings', [$this, 'render_shortcode']);
			add_shortcode('kpoly_edit_listing', [$this, 'render_edit_shortcode']);
			add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
			add_action('init', [$this, 'handle_actions']);
			add_action('init', [$this, 'handle_edit_submission']);
		}
		public function handle_actions() {
			if (!is_user_logged_in()) {
				return;
			}

			if (!isset($_GET['kpoly_action'], $_GET['listing_id'], $_GET['_kpoly_nonce'])) {
				return;
			}

			$action     = sanitize_text_field(wp_unslash($_GET['kpoly_action']));
			$listing_id = absint($_GET['listing_id']);
			$nonce      = sanitize_text_field(wp_unslash($_GET['_kpoly_nonce']));

			if (!$listing_id || get_post_type($listing_id) !== 'product') {
				return;
			}

			if ((int) get_post_field('post_author', $listing_id) !== get_current_user_id()) {
				return;
			}

			if (!wp_verify_nonce($nonce, 'kpoly_listing_action_' . $listing_id)) {
				return;
			}

			if ($action === 'delete') {
				wp_trash_post($listing_id);
				$this->safe_redirect_with_notice('Listing deleted successfully.');
			}

			if ($action === 'mark_sold') {
				update_post_meta($listing_id, '_kpoly_listing_status', 'sold');
				$this->safe_redirect_with_notice('Listing marked as sold.');
			}

			if ($action === 'mark_active') {
				update_post_meta($listing_id, '_kpoly_listing_status', 'active');
				$this->safe_redirect_with_notice('Listing marked as active again.');
			}
			
		}
		private function safe_redirect_with_notice(string $message): void {
			$referer = wp_get_referer();

			if (!$referer) {
				$referer = home_url('/');
			}

			$url = add_query_arg([
				'kpoly_notice' => rawurlencode($message),
			], remove_query_arg(['kpoly_action', 'listing_id', '_kpoly_nonce', 'kpoly_notice'], $referer));

			wp_safe_redirect($url);
			exit;
		}

        public function enqueue_assets() {
            wp_register_style('kpoly-my-listings-style', false);
            wp_enqueue_style('kpoly-my-listings-style');

            $css = "
            .kpoly-my-listings-wrap {
                max-width: 1200px;
                margin: 0 auto;
                padding: 30px 20px;
            }

            .kpoly-my-listings-header {
                margin-bottom: 24px;
            }

            .kpoly-my-listings-header h2 {
                margin: 0 0 8px;
                font-size: 30px;
                font-weight: 800;
                color: #111827;
                line-height: 1.2;
            }

            .kpoly-my-listings-header p {
                margin: 0;
                color: #6b7280;
                font-size: 15px;
            }

            .kpoly-my-listings-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 22px;
            }

            .kpoly-my-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 10px 28px rgba(0,0,0,0.05);
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .kpoly-my-card-image {
                position: relative;
                aspect-ratio: 1 / 1;
                background: #f9fafb;
                overflow: hidden;
            }

            .kpoly-my-card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .kpoly-my-card-badge {
                position: absolute;
                top: 12px;
                left: 12px;
                background: rgba(17,24,39,0.92);
                color: #fff;
                font-size: 12px;
                font-weight: 700;
                padding: 6px 10px;
                border-radius: 999px;
            }

            .kpoly-my-card-body {
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                flex: 1;
            }

            .kpoly-my-card-title {
                margin: 0;
                font-size: 18px;
                font-weight: 800;
                line-height: 1.35;
                color: #111827;
            }

            .kpoly-my-card-title a {
                color: #111827;
                text-decoration: none;
            }

            .kpoly-my-card-title a:hover {
                color: #f59e0b;
            }

            .kpoly-my-card-price {
                margin: 0;
                font-size: 18px;
                font-weight: 800;
                color: #111827;
            }

            .kpoly-my-card-meta {
                display: flex;
                flex-direction: column;
                gap: 6px;
                font-size: 13px;
                color: #6b7280;
            }

            .kpoly-my-card-status {
                display: inline-block;
                font-size: 12px;
                font-weight: 700;
                padding: 6px 10px;
                border-radius: 999px;
                background: #ecfdf5;
                color: #065f46;
                width: fit-content;
            }

            .kpoly-my-card-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin-top: auto;
                padding-top: 10px;
            }

            .kpoly-my-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 11px 12px;
                border-radius: 10px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 800;
                border: none;
                cursor: pointer;
                transition: all .25s ease;
            }

            .kpoly-my-btn-view {
                background: #111827;
                color: #fff;
            }

            .kpoly-my-btn-view:hover {
                background: #000;
                color: #fff;
            }

            .kpoly-my-btn-edit {
                background: #f59e0b;
                color: #111827;
            }

            .kpoly-my-btn-edit:hover {
                background: #d97706;
                color: #fff;
            }

            .kpoly-my-btn-delete {
                background: #fee2e2;
                color: #991b1b;
            }

            .kpoly-my-btn-delete:hover {
                background: #fecaca;
                color: #7f1d1d;
            }

            .kpoly-my-btn-sold {
                background: #dbeafe;
                color: #1d4ed8;
            }

            .kpoly-my-btn-sold:hover {
                background: #bfdbfe;
                color: #1e40af;
            }

            .kpoly-my-empty,
            .kpoly-my-login-required {
                background: #fff;
                border: 1px dashed #d1d5db;
                border-radius: 16px;
                padding: 24px;
                color: #6b7280;
            }

            .kpoly-my-empty a,
            .kpoly-my-login-required a {
                color: #f59e0b;
                font-weight: 700;
                text-decoration: none;
            }
			.kpoly-my-notice {
				background: #e8f7e8;
				color: #176117;
				border: 1px solid #b9e0b9;
				padding: 12px 14px;
				border-radius: 12px;
				margin-bottom: 18px;
				font-weight: 600;
			}

			.kpoly-my-card-status.is-sold {
				background: #fee2e2;
				color: #991b1b;
			}
			.kpoly-edit-wrap {
				max-width: 850px;
				margin: 0 auto;
				padding: 30px 20px;
			}

			.kpoly-edit-card {
				background: #fff;
				border: 1px solid #e5e7eb;
				border-radius: 18px;
				padding: 24px;
				box-shadow: 0 10px 28px rgba(0,0,0,0.05);
			}

			.kpoly-edit-card h2 {
				margin: 0 0 10px;
				font-size: 30px;
				font-weight: 800;
				color: #111827;
			}

			.kpoly-edit-card p {
				margin: 0 0 24px;
				color: #6b7280;
			}

			.kpoly-edit-group {
				margin-bottom: 18px;
			}

			.kpoly-edit-group label {
				display: block;
				font-weight: 700;
				margin-bottom: 8px;
				color: #111827;
			}

			.kpoly-edit-group input[type='text'],
			.kpoly-edit-group input[type='number'],
			.kpoly-edit-group textarea,
			.kpoly-edit-group select {
				width: 100%;
				padding: 12px 14px;
				border: 1px solid #d1d5db;
				border-radius: 10px;
				box-sizing: border-box;
				font-size: 14px;
			}

			.kpoly-edit-group textarea {
				min-height: 120px;
				resize: vertical;
			}

			.kpoly-edit-row {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 16px;
			}

			.kpoly-edit-actions {
				display: flex;
				gap: 12px;
				flex-wrap: wrap;
				margin-top: 22px;
			}

			.kpoly-edit-btn {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 12px 18px;
				border-radius: 10px;
				text-decoration: none;
				border: none;
				cursor: pointer;
				font-size: 14px;
				font-weight: 800;
			}

			.kpoly-edit-btn-primary {
				background: #f59e0b;
				color: #111827;
			}

			.kpoly-edit-btn-primary:hover {
				background: #d97706;
				color: #fff;
			}

			.kpoly-edit-btn-secondary {
				background: #111827;
				color: #fff;
			}

			.kpoly-edit-btn-secondary:hover {
				background: #000;
				color: #fff;
			}

			.kpoly-edit-error {
				background: #fdeaea;
				color: #8a1f1f;
				border: 1px solid #efc2c2;
				padding: 12px 14px;
				border-radius: 12px;
				margin-bottom: 18px;
				font-weight: 600;
			}

			@media (max-width: 640px) {
				.kpoly-edit-wrap {
					padding: 24px 14px;
				}

				.kpoly-edit-card h2 {
					font-size: 24px;
				}

				.kpoly-edit-row {
					grid-template-columns: 1fr;
				}
			}

            @media (max-width: 1024px) {
                .kpoly-my-listings-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 560px) {
                .kpoly-my-listings-wrap {
                    padding: 24px 14px;
                }

                .kpoly-my-listings-header h2 {
                    font-size: 24px;
                }

                .kpoly-my-listings-grid {
                    grid-template-columns: 1fr;
                    gap: 16px;
                }

                .kpoly-my-card-actions {
                    grid-template-columns: 1fr 1fr;
                }
            }
            ";

            wp_add_inline_style('kpoly-my-listings-style', $css);
        }

        public function render_shortcode(): string {
            if (!class_exists('WooCommerce')) {
                return '<div class=\"kpoly-my-empty\">WooCommerce must be active for this dashboard to work.</div>';
            }

            if (!is_user_logged_in()) {
                $account_url = wc_get_page_permalink('myaccount');

                return '<div class=\"kpoly-my-login-required\">Please <a href=\"' . esc_url($account_url) . '\">log in</a> to view your listings dashboard.</div>';
            }

            $user_id = get_current_user_id();
			$notice = sanitize_text_field(wp_unslash($_GET['kpoly_notice'] ?? ''));

            $products = get_posts([
                'post_type'      => 'product',
                'post_status'    => ['publish', 'pending', 'draft'],
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'author'         => $user_id,
            ]);

            ob_start();
            ?>
            <div class="kpoly-my-listings-wrap">
                <div class="kpoly-my-listings-header">
                    <h2>My Listings</h2>
                    <p>Manage the items you have posted on KPoly Market.</p>
                </div>
				<?php if (!empty($notice)) : ?>
				<div class="kpoly-my-notice"><?php echo esc_html($notice); ?></div>
				<?php endif; ?>

                <?php if (!empty($products)) : ?>
                    <div class="kpoly-my-listings-grid">
                        <?php foreach ($products as $product_post) : ?>
                            <?php echo $this->render_product_card($product_post); ?>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="kpoly-my-empty">
                        You have not posted any listings yet. <a href="<?php echo esc_url(home_url('/sell-an-item/')); ?>">Add your first item</a>.
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
		
		public function render_edit_shortcode(): string {
			if (!class_exists('WooCommerce')) {
				return '<div class="kpoly-edit-error">WooCommerce must be active for this page to work.</div>';
			}

			if (!is_user_logged_in()) {
				return '<div class="kpoly-edit-error">You must be logged in to edit a listing.</div>';
			}

			$listing_id = absint($_GET['listing_id'] ?? 0);

			if (!$listing_id || get_post_type($listing_id) !== 'product') {
				return '<div class="kpoly-edit-error">Invalid listing.</div>';
			}

			if ((int) get_post_field('post_author', $listing_id) !== get_current_user_id()) {
				return '<div class="kpoly-edit-error">You are not allowed to edit this listing.</div>';
			}

			$product = wc_get_product($listing_id);

			if (!$product) {
				return '<div class="kpoly-edit-error">Product not found.</div>';
			}

			$title             = get_the_title($listing_id);
			$description       = get_post_field('post_content', $listing_id);
			$short_description = get_post_field('post_excerpt', $listing_id);
			$price             = $product->get_price();
			$location          = get_post_meta($listing_id, '_kpoly_location', true);
			$condition         = get_post_meta($listing_id, '_kpoly_condition', true);
			$whatsapp          = get_post_meta($listing_id, '_kpoly_whatsapp', true);

			$current_cats = wp_get_post_terms($listing_id, 'product_cat', ['fields' => 'ids']);
			$current_cat  = !empty($current_cats) ? (int) $current_cats[0] : 0;

			$tag_names = wp_get_post_terms($listing_id, 'product_tag', ['fields' => 'names']);
			$tag_value = !empty($tag_names) ? implode(', ', $tag_names) : '';

			ob_start();
			?>
			<div class="kpoly-edit-wrap">
				<div class="kpoly-edit-card">
					<h2>Edit Listing</h2>
					<p>Update your item details below.</p>

					<form method="post">
						<?php wp_nonce_field('kpoly_edit_listing_action', 'kpoly_edit_listing_nonce'); ?>
						<input type="hidden" name="kpoly_edit_listing_submitted" value="1">
						<input type="hidden" name="listing_id" value="<?php echo esc_attr($listing_id); ?>">

						<div class="kpoly-edit-group">
							<label for="kpoly_item_name">Item Name *</label>
							<input type="text" id="kpoly_item_name" name="kpoly_item_name" value="<?php echo esc_attr($title); ?>" required>
						</div>

						<div class="kpoly-edit-group">
							<label for="kpoly_description">Description *</label>
							<textarea id="kpoly_description" name="kpoly_description" required><?php echo esc_textarea($description); ?></textarea>
						</div>

						<div class="kpoly-edit-group">
							<label for="kpoly_short_description">Short Description *</label>
							<textarea id="kpoly_short_description" name="kpoly_short_description" required><?php echo esc_textarea($short_description); ?></textarea>
						</div>

						<div class="kpoly-edit-group">
							<label for="kpoly_product_tags">Tags</label>
							<input type="text" id="kpoly_product_tags" name="kpoly_product_tags" value="<?php echo esc_attr($tag_value); ?>" placeholder="e.g. umbrella, red, hostel">
						</div>

						<div class="kpoly-edit-row">
							<div class="kpoly-edit-group">
								<label for="kpoly_price">Price (KES) *</label>
								<input type="number" id="kpoly_price" name="kpoly_price" min="0" step="0.01" value="<?php echo esc_attr($price); ?>" required>
							</div>

							<div class="kpoly-edit-group">
								<label for="kpoly_category">Category *</label>
								<select id="kpoly_category" name="kpoly_category" required>
									<option value="">Select category</option>
									<?php echo $this->render_category_options($current_cat); ?>
								</select>
							</div>
						</div>

						<div class="kpoly-edit-row">
							<div class="kpoly-edit-group">
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

									foreach ($conditions as $item_condition) {
										printf(
											'<option value="%1$s" %2$s>%1$s</option>',
											esc_attr($item_condition),
											selected($condition, $item_condition, false)
										);
									}
									?>
								</select>
							</div>

							<div class="kpoly-edit-group">
								<label for="kpoly_location">Location *</label>
								<input type="text" id="kpoly_location" name="kpoly_location" value="<?php echo esc_attr($location); ?>" required>
							</div>
						</div>

						<div class="kpoly-edit-group">
							<label for="kpoly_whatsapp">WhatsApp Number *</label>
							<input type="text" id="kpoly_whatsapp" name="kpoly_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" required>
						</div>

						<div class="kpoly-edit-actions">
							<button type="submit" class="kpoly-edit-btn kpoly-edit-btn-primary">Update Listing</button>
							<a href="<?php echo esc_url(home_url('/my-listings/')); ?>" class="kpoly-edit-btn kpoly-edit-btn-secondary">Back to My Listings</a>
						</div>
					</form>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
		
		public function handle_edit_submission(): void {
			if (
				!isset($_POST['kpoly_edit_listing_submitted']) ||
				$_POST['kpoly_edit_listing_submitted'] !== '1'
			) {
				return;
			}

			if (!is_user_logged_in()) {
				return;
			}

			if (
				!isset($_POST['kpoly_edit_listing_nonce']) ||
				!wp_verify_nonce(
					sanitize_text_field(wp_unslash($_POST['kpoly_edit_listing_nonce'])),
					'kpoly_edit_listing_action'
				)
			) {
				return;
			}

			$listing_id = absint($_POST['listing_id'] ?? 0);

			if (!$listing_id || get_post_type($listing_id) !== 'product') {
				return;
			}

			if ((int) get_post_field('post_author', $listing_id) !== get_current_user_id()) {
				return;
			}

			$title             = sanitize_text_field(wp_unslash($_POST['kpoly_item_name'] ?? ''));
			$description       = sanitize_textarea_field(wp_unslash($_POST['kpoly_description'] ?? ''));
			$short_description = sanitize_textarea_field(wp_unslash($_POST['kpoly_short_description'] ?? ''));
			$tags              = sanitize_text_field(wp_unslash($_POST['kpoly_product_tags'] ?? ''));
			$price             = sanitize_text_field(wp_unslash($_POST['kpoly_price'] ?? ''));
			$category_id       = absint($_POST['kpoly_category'] ?? 0);
			$condition         = sanitize_text_field(wp_unslash($_POST['kpoly_condition'] ?? ''));
			$location          = sanitize_text_field(wp_unslash($_POST['kpoly_location'] ?? ''));
			$whatsapp          = sanitize_text_field(wp_unslash($_POST['kpoly_whatsapp'] ?? ''));

			wp_update_post([
				'ID'           => $listing_id,
				'post_title'   => $title,
				'post_content' => $description,
				'post_excerpt' => $short_description,
			]);

			update_post_meta($listing_id, '_regular_price', wc_format_decimal($price));
			update_post_meta($listing_id, '_price', wc_format_decimal($price));
			update_post_meta($listing_id, '_kpoly_condition', $condition);
			update_post_meta($listing_id, '_kpoly_location', $location);
			update_post_meta($listing_id, '_kpoly_whatsapp', $whatsapp);

			if ($category_id) {
				wp_set_object_terms($listing_id, [$category_id], 'product_cat');
			}

			if ($tags !== '') {
				$tags_array = array_filter(array_map('trim', explode(',', $tags)));
				wp_set_object_terms($listing_id, $tags_array, 'product_tag');
			} else {
				wp_set_object_terms($listing_id, [], 'product_tag');
			}

			$redirect_url = add_query_arg([
				'kpoly_notice' => rawurlencode('Listing updated successfully.'),
			], home_url('/my-listings/'));

			wp_safe_redirect($redirect_url);
			exit;
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

        private function render_product_card($product_post): string {
            $product_id = $product_post->ID;
            $product    = wc_get_product($product_id);

            if (!$product) {
                return '';
            }

            $title      = get_the_title($product_id);
            $permalink  = get_permalink($product_id);
            $price      = $product->get_price();
            $price_html = $price !== '' ? 'KSh ' . number_format((float) $price, 2) : 'Price on request';
            $location   = get_post_meta($product_id, '_kpoly_location', true);
            $condition  = get_post_meta($product_id, '_kpoly_condition', true);
            $post_status    = get_post_status($product_id);
			$listing_status = get_post_meta($product_id, '_kpoly_listing_status', true);
			$listing_status = $listing_status ? $listing_status : 'active';

            $thumbnail = get_the_post_thumbnail_url($product_id, 'medium');
            if (!$thumbnail) {
                $thumbnail = wc_placeholder_img_src();
            }

            $nonce = wp_create_nonce('kpoly_listing_action_' . $product_id);

			$edit_link = add_query_arg([
				'listing_id' => $product_id,
			], home_url('/edit-listing/'));

			$dashboard_url = home_url('/my-listings/');

			$delete_link = add_query_arg([
				'kpoly_action' => 'delete',
				'listing_id'   => $product_id,
				'_kpoly_nonce' => $nonce,
			], $dashboard_url);

			if ($listing_status === 'sold') {
				$sold_link = add_query_arg([
					'kpoly_action' => 'mark_active',
					'listing_id'   => $product_id,
					'_kpoly_nonce' => $nonce,
				], $dashboard_url);

				$sold_label = 'Mark Active';
			} else {
				$sold_link = add_query_arg([
					'kpoly_action' => 'mark_sold',
					'listing_id'   => $product_id,
					'_kpoly_nonce' => $nonce,
				], $dashboard_url);

				$sold_label = 'Mark Sold';
			}

            ob_start();
            ?>
            <article class="kpoly-my-card">
                <div class="kpoly-my-card-image">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                    <?php if (!empty($condition)) : ?>
                        <span class="kpoly-my-card-badge"><?php echo esc_html($condition); ?></span>
                    <?php endif; ?>
                </div>

                <div class="kpoly-my-card-body">
                    <h3 class="kpoly-my-card-title">
                        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                    </h3>

                    <p class="kpoly-my-card-price"><?php echo esc_html($price_html); ?></p>

                    <div class="kpoly-my-card-meta">
                        <?php if (!empty($location)) : ?>
                            <span>📍 <?php echo esc_html($location); ?></span>
                        <?php endif; ?>

                        <span>📄 Post Status: <?php echo esc_html(ucfirst($post_status)); ?></span>
						<span>🏷 Listing Status: <?php echo esc_html(ucfirst($listing_status)); ?></span>
                        <span>🗓 Posted: <?php echo esc_html(get_the_date('M j, Y', $product_id)); ?></span>
                    </div>

                    <span class="kpoly-my-card-status <?php echo $listing_status === 'sold' ? 'is-sold' : ''; ?>">
						<?php echo esc_html($listing_status === 'sold' ? 'Sold' : 'Active'); ?>
					</span>

                    <div class="kpoly-my-card-actions">
						<a class="kpoly-my-btn kpoly-my-btn-view" href="<?php echo esc_url($permalink); ?>">View</a>
						<a class="kpoly-my-btn kpoly-my-btn-edit" href="<?php echo esc_url($edit_link); ?>">Edit</a>
						<a class="kpoly-my-btn kpoly-my-btn-delete" href="<?php echo esc_url($delete_link); ?>" onclick="return confirm('Are you sure you want to delete this listing?');">Delete</a>
						<a class="kpoly-my-btn kpoly-my-btn-sold" href="<?php echo esc_url($sold_link); ?>"><?php echo esc_html($sold_label); ?></a>
					</div>
                </div>
            </article>
            <?php
            return ob_get_clean();
        }
    }

    new KPoly_My_Listings_Dashboard();
}
