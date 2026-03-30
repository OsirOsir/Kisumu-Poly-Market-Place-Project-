<?php
/**
 * Plugin Name: KPoly Home Listings
 * Plugin URI: https://kisumupolymarket.co.ke
 * Description: Custom homepage marketplace listings section for KPoly Market using WooCommerce products.
 * Version: 2.0.0
 * Author: KPoly Market
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('KPoly_Home_Listings')) {

    class KPoly_Home_Listings {

        public function __construct() {
            add_shortcode('kpoly_home_listings', [$this, 'render_shortcode']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        }

        public function enqueue_assets() {
            wp_enqueue_style(
                'font-awesome-6',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
                array(),
                '6.5.2'
            );

            wp_register_style('kpoly-home-listings-style', false);
            wp_enqueue_style('kpoly-home-listings-style');

            $css = "
            .kpoly-home-listings {
                padding: 45px 0 30px;
                width: 100%;
            }

            .kpoly-home-listings .kpoly-wrap {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }

            .kpoly-home-header {
                margin-bottom: 26px;
                text-align: left;
            }

            .kpoly-home-header h2 {
                font-size: 34px;
                font-weight: 800;
                color: #111827;
                margin: 0 0 8px;
                line-height: 1.2;
            }

            .kpoly-home-header p {
                margin: 0;
                color: #6b7280;
                font-size: 16px;
            }

            .kpoly-tabs-nav {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 14px;
                margin-bottom: 28px;
            }

            .kpoly-tab-btn {
                border: 1px solid #e5e7eb;
                background: #ffffff;
                color: #111827;
                padding: 16px 12px;
                border-radius: 16px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.25s ease;
                min-height: 90px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 10px;
                text-align: center;
                box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            }

            .kpoly-tab-btn i {
                font-size: 24px;
                color: #1f3b5b;
                transition: all 0.25s ease;
            }

            .kpoly-tab-btn span {
                display: block;
                line-height: 1.35;
            }

            .kpoly-tab-btn:hover,
            .kpoly-tab-btn.active {
                background: #fff7ed;
                border-color: #f59e0b;
                transform: translateY(-2px);
                box-shadow: 0 12px 28px rgba(245, 158, 11, 0.12);
            }

            .kpoly-tab-btn:hover i,
            .kpoly-tab-btn.active i {
                color: #f59e0b;
            }

            .kpoly-tab-pane {
                display: none;
            }

            .kpoly-tab-pane.active {
                display: block;
            }

            .kpoly-listings-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 22px;
            }

            .kpoly-card {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .kpoly-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 16px 36px rgba(0,0,0,0.08);
            }

            .kpoly-card-image {
                position: relative;
                background: #f9fafb;
                aspect-ratio: 1 / 1;
                overflow: hidden;
            }

            .kpoly-card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .kpoly-card-badge {
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

            .kpoly-card-body {
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                flex: 1;
            }

            .kpoly-card-title {
                font-size: 18px;
                line-height: 1.4;
                font-weight: 800;
                margin: 0;
            }

            .kpoly-card-title a {
                color: #111827;
                text-decoration: none;
            }

            .kpoly-card-title a:hover {
                color: #f59e0b;
            }

            .kpoly-card-price {
                font-size: 18px;
                font-weight: 800;
                color: #111827;
                margin: 0;
            }

            .kpoly-card-meta {
                display: flex;
                flex-direction: column;
                gap: 6px;
                font-size: 13px;
                color: #6b7280;
            }

            .kpoly-card-meta span {
                display: block;
            }

            .kpoly-card-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: auto;
                padding-top: 6px;
            }

            .kpoly-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 11px 14px;
                border-radius: 10px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 800;
                transition: all 0.25s ease;
            }

            .kpoly-btn-primary {
                background: #f59e0b;
                color: #111827;
            }

            .kpoly-btn-primary:hover {
                background: #d97706;
                color: #fff;
            }

            .kpoly-btn-secondary {
                background: #111827;
                color: #fff;
            }

            .kpoly-btn-secondary:hover {
                background: #000;
                color: #fff;
            }

            .kpoly-empty {
                background: #fff;
                border: 1px dashed #d1d5db;
                border-radius: 16px;
                padding: 28px;
                color: #6b7280;
            }

            .kpoly-view-all {
                margin-top: 22px;
                text-align: right;
            }

            .kpoly-view-all a {
                color: #111827;
                font-weight: 800;
                text-decoration: none;
            }

            .kpoly-view-all a:hover {
                color: #f59e0b;
            }
			.kpoly-home-listings {
				position: relative;
				z-index: 20;
			}

			.kpoly-tabs-nav {
				position: relative;
				z-index: 25;
			}

			.kpoly-tab-btn {
				position: relative;
				z-index: 30;
				-webkit-tap-highlight-color: transparent;
				touch-action: manipulation;
			}

            @media (max-width: 1200px) {
                .kpoly-tabs-nav {
                    grid-template-columns: repeat(4, 1fr);
                }

                .kpoly-listings-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 768px) {
                .kpoly-home-header h2 {
                    font-size: 26px;
                }

                .kpoly-tabs-nav {
                    grid-template-columns: repeat(3, 1fr);
                }

                .kpoly-listings-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 16px;
                }

                .kpoly-tab-btn {
                    min-height: 82px;
                    padding: 14px 10px;
                    font-size: 13px;
                }

                .kpoly-tab-btn i {
                    font-size: 22px;
                }
            }

            @media (max-width: 520px) {
					.kpoly-home-listings .kpoly-wrap {
						padding: 0 14px;
					}

					.kpoly-tabs-nav {
						grid-template-columns: repeat(2, 1fr);
					}

					.kpoly-listings-grid {
						grid-template-columns: 1fr;
					}

					.kpoly-card-actions {
						display: grid;
						grid-template-columns: 1fr 1fr;
						gap: 10px;
						align-items: stretch;
					}

					.kpoly-btn {
						width: 100%;
						min-width: 0;
						padding: 12px 8px;
						font-size: 13px;
						text-align: center;
						white-space: nowrap;
						overflow: hidden;
						text-overflow: ellipsis;
					}
				}
				@media (max-width: 768px) {
				.kpoly-tab-btn {
					pointer-events: auto;
					touch-action: manipulation;
				}

				.kpoly-tabs-nav,
				.kpoly-tabs-nav * {
					pointer-events: auto;
				}
			}
            ";

            wp_add_inline_style('kpoly-home-listings-style', $css);

            wp_register_script('kpoly-home-listings-script', false, [], false, true);
            wp_enqueue_script('kpoly-home-listings-script');

            $js = "
            document.addEventListener('DOMContentLoaded', function () {
				const wrappers = document.querySelectorAll('.kpoly-home-listings');

				wrappers.forEach(function(wrapper) {
					const buttons = wrapper.querySelectorAll('.kpoly-tab-btn');
					const panes = wrapper.querySelectorAll('.kpoly-tab-pane');

					function activateTab(btn) {
						const target = btn.getAttribute('data-target');

						buttons.forEach(function(b) {
							b.classList.remove('active');
						});

						panes.forEach(function(p) {
							p.classList.remove('active');
						});

						btn.classList.add('active');

						const pane = wrapper.querySelector('#' + target);
						if (pane) {
							pane.classList.add('active');
						}
					}

					buttons.forEach(function(btn) {
						btn.addEventListener('pointerup', function(e) {
							e.preventDefault();
							activateTab(btn);
						});
					});
				});
			});
            ";

            wp_add_inline_script('kpoly-home-listings-script', $js);
        }

        public function render_shortcode($atts) {
            if (!class_exists('WooCommerce')) {
                return '<div class=\"kpoly-empty\">WooCommerce must be active for KPoly Home Listings to work.</div>';
            }

            $atts = shortcode_atts([
                'title' => 'Browse by Category',
                'subtitle' => 'Find what students around Kisumu Polytechnic are selling right now.',
                'per_category' => 8,
            ], $atts, 'kpoly_home_listings');

            $categories = [
                [
                    'label' => 'Electronics',
                    'slug'  => 'electronics',
                    'icon'  => 'fa-solid fa-laptop',
                ],
                [
                    'label' => 'Fashion',
                    'slug'  => 'fashion',
                    'icon'  => 'fa-solid fa-shirt',
                ],
                [
                    'label' => 'Phones & Tablets',
                    'slug'  => 'phones-tablets',
                    'icon'  => 'fa-solid fa-mobile-screen-button',
                ],
                [
                    'label' => 'Bags & Accessories',
                    'slug'  => 'bags-accessories',
                    'icon'  => 'fa-solid fa-briefcase',
                ],
                [
                    'label' => 'Hostel & Room Items',
                    'slug'  => 'hostel-room-items',
                    'icon'  => 'fa-solid fa-bed',
                ],
                [
                    'label' => 'Health & Beauty',
                    'slug'  => 'health-beauty',
                    'icon'  => 'fa-solid fa-heart-pulse',
                ],
                [
                    'label' => 'Sports & Fitness',
                    'slug'  => 'sports-fitness',
                    'icon'  => 'fa-solid fa-dumbbell',
                ],
                [
                    'label' => 'Services',
                    'slug'  => 'services',
                    'icon'  => 'fa-solid fa-screwdriver-wrench',
                ],
                [
                    'label' => 'Jobs & Gigs',
                    'slug'  => 'jobs-gigs',
                    'icon'  => 'fa-solid fa-briefcase',
                ],
                [
                    'label' => 'Other Listings',
                    'slug'  => 'other-listings',
                    'icon'  => 'fa-solid fa-box-open',
                ],
                [
                    'label' => 'Books & Stationery',
                    'slug'  => 'books-stationery',
                    'icon'  => 'fa-solid fa-book',
                ],
                [
                    'label' => 'Sport & Outdoor',
                    'slug'  => 'sport-outdoor',
                    'icon'  => 'fa-solid fa-bicycle',
                ],
				[
					'label' => 'Kitchen Accessories',
					'slug'  => 'kitchen-accessories',
					'icon'  => 'fa-solid fa-utensils',
				],
            ];

            $instance_id = 'kpoly-home-' . wp_rand(1000, 9999);

            ob_start();
            ?>
            <section class="kpoly-home-listings" id="<?php echo esc_attr($instance_id); ?>">
                <div class="kpoly-wrap">

                    <div class="kpoly-home-header">
                        <h2><?php echo esc_html($atts['title']); ?></h2>
                        <p><?php echo esc_html($atts['subtitle']); ?></p>
                    </div>

                    <div class="kpoly-tabs-nav">
                        <?php foreach ($categories as $index => $category) : ?>
                            <button
                                class="kpoly-tab-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                                type="button"
                                data-target="<?php echo esc_attr($instance_id . '-' . sanitize_title($category['slug'])); ?>"
                            >
                                <i class="<?php echo esc_attr($category['icon']); ?>"></i>
                                <span><?php echo esc_html($category['label']); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ($categories as $index => $category) : ?>
                        <?php
                        $pane_id = $instance_id . '-' . sanitize_title($category['slug']);
                        $products = $this->get_products_by_category($category['slug'], (int) $atts['per_category']);
                        ?>
                        <div class="kpoly-tab-pane <?php echo $index === 0 ? 'active' : ''; ?>" id="<?php echo esc_attr($pane_id); ?>">
                            <?php if (!empty($products)) : ?>
                                <div class="kpoly-listings-grid">
                                    <?php foreach ($products as $product_post) : ?>
                                        <?php echo $this->render_product_card($product_post); ?>
                                    <?php endforeach; ?>
                                </div>

                                <?php
                                $term_link = get_term_link($category['slug'], 'product_cat');
                                if (!is_wp_error($term_link)) :
                                ?>
                                    <div class="kpoly-view-all">
                                        <a href="<?php echo esc_url($term_link); ?>">View all <?php echo esc_html($category['label']); ?> →</a>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <div class="kpoly-empty">
                                    No listings found yet under <strong><?php echo esc_html($category['label']); ?></strong>.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            </section>
            <?php
            return ob_get_clean();
        }

        private function get_products_by_category($category_slug, $limit = 8) {
            $args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $category_slug,
                    ],
                ],
            ];

            return get_posts($args);
        }

        private function render_product_card($product_post) {
            $product_id = $product_post->ID;
            $product    = wc_get_product($product_id);

            if (!$product) {
                return '';
            }

            $title      = get_the_title($product_id);
            $permalink  = get_permalink($product_id);
            $price_html = $product->get_price() !== '' ? 'KSh ' . number_format((float) $product->get_price(), 2) : 'Price on request';
            $location   = get_post_meta($product_id, '_kpoly_location', true);
            $condition  = get_post_meta($product_id, '_kpoly_condition', true);
            $whatsapp   = get_post_meta($product_id, '_kpoly_whatsapp', true);
			

            $thumbnail = get_the_post_thumbnail_url($product_id, 'medium');
            if (!$thumbnail) {
                $thumbnail = wc_placeholder_img_src();
            }

            $whatsapp_link = '';
            if (!empty($whatsapp)) {
                $clean_number = preg_replace('/[^0-9]/', '', $whatsapp);
                if (!empty($clean_number)) {
                    $message = rawurlencode('Hello, I am interested in your listing: ' . $title . ' on KPoly Market.');
                    $whatsapp_link = 'https://wa.me/' . $clean_number . '?text=' . $message;
                }
            }

            ob_start();
            ?>
            <article class="kpoly-card">
                <div class="kpoly-card-image">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                    <?php if (!empty($condition)) : ?>
                        <span class="kpoly-card-badge"><?php echo esc_html($condition); ?></span>
                    <?php endif; ?>
                </div>

                <div class="kpoly-card-body">
                    <h3 class="kpoly-card-title">
                        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                    </h3>

                    <p class="kpoly-card-price"><?php echo esc_html($price_html); ?></p>

                    <div class="kpoly-card-meta">
                        <?php if (!empty($location)) : ?>
                            <span>📍 <?php echo esc_html($location); ?></span>
                        <?php endif; ?>

                        <?php if (!empty($condition)) : ?>
                            <span>📦 <?php echo esc_html($condition); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="kpoly-card-actions">
                        <a class="kpoly-btn kpoly-btn-primary" href="<?php echo esc_url($permalink); ?>">
                            View Listing
                        </a>

                        <?php if (!empty($whatsapp_link)) : ?>
                            <a class="kpoly-btn kpoly-btn-secondary" href="<?php echo esc_url($whatsapp_link); ?>" target="_blank" rel="noopener">
                                WhatsApp Seller
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php
            return ob_get_clean();
        }
    }

    new KPoly_Home_Listings();
}
