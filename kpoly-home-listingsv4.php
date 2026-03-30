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
			add_shortcode('kpoly_deals_of_day', [$this, 'render_deals_shortcode']);
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
			.kpoly-deal-price a {
				color: #111827;
				text-decoration: none;
			}

			.kpoly-deal-price a:hover {
				color: #f59e0b;
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
			.kpoly-deals-section {
				padding: 20px 0 40px;
				width: 100%;
			}

			.kpoly-deals-wrap {
				width: 100%;
				max-width: 1400px;
				margin: 0 auto;
				padding: 0 20px;
			}

			.kpoly-deals-box {
				background: #fff;
				border-radius: 16px;
				padding: 18px 18px 22px;
				border: 1px solid #e5e7eb;
			}

			.kpoly-deals-header {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 16px;
				margin-bottom: 18px;
				flex-wrap: wrap;
			}

			.kpoly-deals-title-wrap {
				display: flex;
				align-items: center;
				gap: 16px;
				flex-wrap: wrap;
			}

			.kpoly-deals-title {
				margin: 0;
				font-size: 22px;
				font-weight: 800;
				color: #111827;
			}

			.kpoly-deals-timer {
				background: #fb5b00;
				color: #fff;
				font-weight: 800;
				font-size: 14px;
				padding: 10px 18px;
				border-radius: 4px;
				line-height: 1;
				min-width: 120px;
				text-align: center;
			}

			.kpoly-deals-viewall {
				color: #111827;
				font-weight: 700;
				text-decoration: none;
			}

			.kpoly-deals-viewall:hover {
				color: #f59e0b;
			}

			.kpoly-deals-scroll {
				display: flex;
				gap: 18px;
				overflow-x: auto;
				scroll-behavior: auto;
				padding-bottom: 6px;
				-webkit-overflow-scrolling: touch;
			}

			.kpoly-deals-scroll::-webkit-scrollbar {
				height: 8px;
			}

			.kpoly-deals-scroll::-webkit-scrollbar-thumb {
				background: #d1d5db;
				border-radius: 999px;
			}

			.kpoly-deal-card {
				min-width: 210px;
				max-width: 210px;
				flex: 0 0 210px;
			}

			.kpoly-deal-image {
				background: #f9fafb;
				aspect-ratio: 1 / 1;
				overflow: hidden;
				margin-bottom: 12px;
			}

			.kpoly-deal-image img {
				width: 100%;
				height: 100%;
				object-fit: cover;
				display: block;
			}

			.kpoly-deal-icons {
				display: flex;
				align-items: center;
				gap: 18px;
				margin-bottom: 16px;
			}

			.kpoly-deal-icon-btn {
				width: 18px;
				height: 18px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				color: #111827;
				text-decoration: none;
				cursor: pointer;
				font-size: 16px;
				border: none;
				background: transparent;
				padding: 0;
			}

			.kpoly-deal-icon-btn:hover {
				color: #f59e0b;
			}

			.kpoly-deal-price {
				margin: 0 0 10px;
				font-size: 18px;
				font-weight: 500;
				color: #111827;
			}

			.kpoly-deal-title {
				margin: 0;
				font-size: 14px;
				line-height: 1.4;
				font-weight: 500;
			}

			.kpoly-deal-title a {
				color: #2563eb;
				text-decoration: none;
			}

			.kpoly-deal-title a:hover {
				color: #f59e0b;
			}

			.kpoly-deals-empty {
				padding: 20px 0;
				color: #6b7280;
			}

			/* Quick View Modal */
			.kpoly-quickview-overlay {
				position: fixed;
				inset: 0;
				background: rgba(17,24,39,0.6);
				display: none;
				align-items: center;
				justify-content: center;
				padding: 20px;
				z-index: 999999;
			}

			.kpoly-quickview-overlay.active {
				display: flex;
			}

			.kpoly-quickview-modal {
				width: 100%;
				max-width: 760px;
				background: #fff;
				border-radius: 18px;
				overflow: hidden;
				position: relative;
				box-shadow: 0 20px 50px rgba(0,0,0,0.2);
			}

			.kpoly-quickview-close {
				position: absolute;
				top: 14px;
				right: 16px;
				border: none;
				background: transparent;
				font-size: 26px;
				line-height: 1;
				cursor: pointer;
				color: #111827;
				z-index: 5;
			}

			.kpoly-quickview-content {
				display: grid;
				grid-template-columns: 1fr 1fr;
			}

			.kpoly-quickview-image {
				background: #f9fafb;
				min-height: 320px;
			}

			.kpoly-quickview-image img {
				width: 100%;
				height: 100%;
				object-fit: cover;
				display: block;
			}

			.kpoly-quickview-body {
				padding: 28px 24px;
			}

			.kpoly-quickview-body h3 {
				margin: 0 0 12px;
				font-size: 24px;
				font-weight: 800;
				color: #111827;
			}

			.kpoly-quickview-price {
				margin: 0 0 14px;
				font-size: 22px;
				font-weight: 800;
				color: #111827;
			}

			.kpoly-quickview-meta {
				display: flex;
				flex-direction: column;
				gap: 8px;
				color: #6b7280;
				font-size: 14px;
				margin-bottom: 16px;
			}

			.kpoly-quickview-desc {
				color: #374151;
				font-size: 14px;
				line-height: 1.6;
				margin-bottom: 18px;
			}

			.kpoly-quickview-link {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 12px 16px;
				border-radius: 10px;
				background: #f59e0b;
				color: #111827;
				text-decoration: none;
				font-weight: 800;
			}

			.kpoly-quickview-link:hover {
				background: #d97706;
				color: #fff;
			}

			@media (max-width: 768px) {
				.kpoly-deals-wrap {
					padding: 0 14px;
				}

				.kpoly-deal-card {
					min-width: 170px;
					max-width: 170px;
					flex: 0 0 170px;
				}

				.kpoly-quickview-content {
					grid-template-columns: 1fr;
				}

				.kpoly-quickview-image {
					min-height: 220px;
				}
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
			document.querySelectorAll('.kpoly-quickview-trigger').forEach(function(btn) {
				btn.addEventListener('click', function() {
					const overlay = document.getElementById('kpoly-quickview-overlay');
					if (!overlay) return;

					overlay.querySelector('.kpoly-quickview-image img').src = btn.getAttribute('data-image') || '';
					overlay.querySelector('.kpoly-quickview-title').textContent = btn.getAttribute('data-title') || '';
					overlay.querySelector('.kpoly-quickview-price').textContent = btn.getAttribute('data-price') || '';
					overlay.querySelector('.kpoly-quickview-location').textContent = btn.getAttribute('data-location') || '';
					overlay.querySelector('.kpoly-quickview-condition').textContent = btn.getAttribute('data-condition') || '';
					overlay.querySelector('.kpoly-quickview-desc').textContent = btn.getAttribute('data-desc') || '';
					overlay.querySelector('.kpoly-quickview-link').href = btn.getAttribute('data-link') || '#';

					overlay.classList.add('active');
					document.body.style.overflow = 'hidden';
				});
			});

			const kpolyQuickviewOverlay = document.getElementById('kpoly-quickview-overlay');
			if (kpolyQuickviewOverlay) {
				const closeBtn = kpolyQuickviewOverlay.querySelector('.kpoly-quickview-close');

				if (closeBtn) {
					closeBtn.addEventListener('click', function() {
						kpolyQuickviewOverlay.classList.remove('active');
						document.body.style.overflow = '';
					});
				}

				kpolyQuickviewOverlay.addEventListener('click', function(e) {
					if (e.target === kpolyQuickviewOverlay) {
						kpolyQuickviewOverlay.classList.remove('active');
						document.body.style.overflow = '';
					}
				});
			}

			document.querySelectorAll('.kpoly-deals-timer').forEach(function(timerEl) {
				let totalSeconds = 3 * 60 * 60;

				function updateTimer() {
					const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
					const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
					const seconds = String(totalSeconds % 60).padStart(2, '0');

					timerEl.textContent = hours + ' : ' + minutes + ' : ' + seconds;

					if (totalSeconds > 0) {
						totalSeconds--;
					} else {
						totalSeconds = 3 * 60 * 60;
					}
				}

				updateTimer();
				setInterval(updateTimer, 1000);
			});
			document.querySelectorAll('.kpoly-deals-scroll').forEach(function(track) {
				let autoScroll;
				let speed = 1;

				function startAutoScroll() {
					autoScroll = setInterval(function() {
						track.scrollLeft += speed;

						if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 1) {
							track.scrollLeft = 0;
						}
					}, 20);
				}

				function stopAutoScroll() {
					clearInterval(autoScroll);
				}

				startAutoScroll();

				track.addEventListener('mouseenter', stopAutoScroll);
				track.addEventListener('mouseleave', startAutoScroll);
				track.addEventListener('touchstart', stopAutoScroll, { passive: true });
				track.addEventListener('touchend', startAutoScroll);
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
		
		public function render_deals_shortcode($atts) {
			if (!class_exists('WooCommerce')) {
				return '<div class="kpoly-empty">WooCommerce must be active for Deals Of The Day to work.</div>';
			}

			$atts = shortcode_atts([
				'title' => 'Deals Of The Day',
				'limit' => 10,
				'view_all_url' => home_url('/shop/'),
			], $atts, 'kpoly_deals_of_day');

			$products = $this->get_latest_products((int) $atts['limit']);

			ob_start();
			?>
			<section class="kpoly-deals-section">
				<div class="kpoly-deals-wrap">
					<div class="kpoly-deals-box">
						<div class="kpoly-deals-header">
							<div class="kpoly-deals-title-wrap">
								<h2 class="kpoly-deals-title"><?php echo esc_html($atts['title']); ?></h2>
								<div class="kpoly-deals-timer">03 : 00 : 00</div>
							</div>

							<a class="kpoly-deals-viewall" href="<?php echo esc_url($atts['view_all_url']); ?>">View All</a>
						</div>

						<?php if (!empty($products)) : ?>
							<div class="kpoly-deals-scroll">
								<?php foreach ($products as $product_post) : ?>
									<?php echo $this->render_deal_card($product_post); ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<div class="kpoly-deals-empty">No fresh listings available right now.</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<div class="kpoly-quickview-overlay" id="kpoly-quickview-overlay">
				<div class="kpoly-quickview-modal">
					<button type="button" class="kpoly-quickview-close" aria-label="Close quick view">&times;</button>

					<div class="kpoly-quickview-content">
						<div class="kpoly-quickview-image">
							<img src="" alt="">
						</div>

						<div class="kpoly-quickview-body">
							<h3 class="kpoly-quickview-title"></h3>
							<p class="kpoly-quickview-price"></p>

							<div class="kpoly-quickview-meta">
								<span>📍 <span class="kpoly-quickview-location"></span></span>
								<span>📦 <span class="kpoly-quickview-condition"></span></span>
							</div>

							<div class="kpoly-quickview-desc"></div>

							<a href="#" class="kpoly-quickview-link">View Full Listing</a>
						</div>
					</div>
				</div>
			</div>
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
				'meta_query'     => [
					'relation' => 'OR',
					[
						'key'     => '_kpoly_listing_status',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => '_kpoly_listing_status',
						'value'   => 'sold',
						'compare' => '!=',
					],
				],
			];

			return get_posts($args);
		}
		private function get_latest_products($limit = 10) {
			$args = [
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_query'     => [
					'relation' => 'OR',
					[
						'key'     => '_kpoly_listing_status',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => '_kpoly_listing_status',
						'value'   => 'sold',
						'compare' => '!=',
					],
				],
			];

			return get_posts($args);
		}
		private function render_deal_card($product_post) {
			$product_id = $product_post->ID;
			$product    = wc_get_product($product_id);

			if (!$product) {
				return '';
			}

			$title       = get_the_title($product_id);
			$permalink   = get_permalink($product_id);
			$price_html  = $product->get_price() !== '' ? 'KSh' . number_format((float) $product->get_price(), 2) : 'Price on request';
			$location    = get_post_meta($product_id, '_kpoly_location', true);
			$condition   = get_post_meta($product_id, '_kpoly_condition', true);
			$description = wp_strip_all_tags(get_post_field('post_excerpt', $product_id));

			if ($description === '') {
				$description = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $product_id)), 18);
			}

			$thumbnail = get_the_post_thumbnail_url($product_id, 'medium');
			if (!$thumbnail) {
				$thumbnail = wc_placeholder_img_src();
			}

			ob_start();
			?>
			<div class="kpoly-deal-card">
				<div class="kpoly-deal-image">
					<a href="<?php echo esc_url($permalink); ?>">
						<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>">
					</a>
				</div>

				<div class="kpoly-deal-icons">
					<button
						type="button"
						class="kpoly-deal-icon-btn kpoly-quickview-trigger"
						data-image="<?php echo esc_url($thumbnail); ?>"
						data-title="<?php echo esc_attr($title); ?>"
						data-price="<?php echo esc_attr($price_html); ?>"
						data-location="<?php echo esc_attr($location); ?>"
						data-condition="<?php echo esc_attr($condition); ?>"
						data-desc="<?php echo esc_attr($description); ?>"
						data-link="<?php echo esc_url($permalink); ?>"
						aria-label="Quick view"
					>
						<i class="fa-regular fa-eye"></i>
					</button>

					<a class="kpoly-deal-icon-btn" href="<?php echo esc_url($permalink); ?>" aria-label="View full listing">
						<i class="fa-regular fa-file-lines"></i>
					</a>
				</div>

				<p class="kpoly-deal-price">
					<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($price_html); ?></a>
				</p>

				<h3 class="kpoly-deal-title">
					<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
				</h3>
			</div>
			<?php
			return ob_get_clean();
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
			$listing_status = get_post_meta($product_id, '_kpoly_listing_status', true);
			$listing_status = $listing_status ? $listing_status : 'active';
			

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
            <article class="kpoly-card <?php echo $listing_status === 'sold' ? 'kpoly-sold' : ''; ?>">
                <div class="kpoly-card-image">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                    <?php if ($listing_status === 'sold') : ?>
						<span class="kpoly-card-badge" style="background:#dc2626;">Sold</span>
					<?php elseif (!empty($condition)) : ?>
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

                        <?php if (!empty($whatsapp_link) && $listing_status !== 'sold') : ?>
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
