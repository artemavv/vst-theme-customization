<?php
if (!defined('ABSPATH')) exit;
/**
 * Page Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a page ('page' post_type) unless another page template overrules this one.
 * @link http://codex.wordpress.org/Pages
 *
 * @package WooFramework
 * @subpackage Template
 */
get_header();
global $woo_options;
?>

<div id="content" class="page col-full">

  <?php woo_main_before(); ?>

  <section id="main" class="col-left">

    <?php
    if (have_posts()) {
      $count = 0;
      while (have_posts()) {
        the_post();
        $count++;
        ?>
        <article <?php post_class(); ?>>

          <?php

          // check if the flexible content field has rows of data
          if (have_rows('content_blocks')):

          // loop through the rows of data
          while (have_rows('content_blocks')) :
          the_row(); ?>

          <?php if (get_row_layout() == 'intro'): ?>

            <header id="page-header">
              <div class="wrapper">
                <h1><?php the_sub_field('heading'); ?></h1>
                <?php the_sub_field('content'); ?>
              </div><!-- /.wrapper -->
            </header>

            <?php if (is_page('perks')) { ?>
            <section class="entry">
              <div class="wrapper the-vault-product-container the-vault-product-container_perks">
                <div class="the-vault-inner">
                <?php } ?>

                <?php elseif (get_row_layout() == 'full_width'): ?>

                  <section class="entry text-media">
                    <div class="wrapper">

                      <?php if (get_sub_field('sub_heading')) { ?>
                        <h2><?php the_sub_field('sub_heading'); ?></h2>
                      <?php } ?>
                      <?php the_sub_field('content'); ?>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == '2_cols'): ?>

                  <section class="entry two-cols">
                    <div class="wrapper">

                      <div class="alignleft">
                        <?php the_sub_field('left_col'); ?>
                      </div>
                      <div class="alignright">
                        <?php the_sub_field('right_col'); ?>
                      </div>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

									
									
                <?php elseif (get_row_layout() == 'big_optin'): ?>

                  <section id="intro-message" class="home-section">

                    <h1><?php the_sub_field('heading'); ?></h1>
                    <h4><?php the_sub_field('subheading'); ?></h4>

                    <?php if (get_sub_field('show_form')): ?>

                    <div class="subscribe-form subscribe-form_page"><div class="klaviyo-form-SkYj5j"><span class="vst-loader"></span></div></div>
                    <?php
                    /*
                      ?>
                      <div class="mc_embed_signup">
                        <form action="//vstbuzz.us5.list-manage.com/subscribe/post?u=5a4cc3f16383a15f98af1c0d1&amp;id=8656c2da47" method="post" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>

                          <input type="email" placeholder="<?php the_sub_field('input_placeholder'); ?>" name="EMAIL" class="required email" id="mce-EMAIL">
                          <input type="submit" value="<?php the_sub_field('button_text'); ?>" name="subscribe" id="mc-embedded-subscribe" class="button">

                          <input type="hidden" value="<?php the_sub_field('source_id'); ?>" name="SOURCE" class="" id="mce-SOURCE"/>

                          <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                          <div style="position: absolute; left: -5000px;"><input type="text" name="b_5a4cc3f16383a15f98af1c0d1_8656c2da47" tabindex="-1" value=""></div>

                        </form>
                      </div>
                      <?php */ ?>

                      <p class="privacy-info-para"><!-- We’ll first send a confirmation email to make sure it’s you &#x1F603 -->  
                        <span>
                        Check out our <a href="/privacy-policy/" target="_blank">privacy policy</a> to see how we protect and manage your submitted data.
                      </span>
                      </p>

                    <?php endif; ?>

                    <?php
                      $videoTitle = get_sub_field('video_title');
                      $videoLink = get_sub_field('video_link');
                      if(!empty($videoLink)) {
                        parse_str( parse_url( $videoLink, PHP_URL_QUERY ), $videoArr );
                        if(isset($videoArr['v']) && !empty($videoArr['v'])) {
                          echo '<div class="intro-video">';
                          echo '<hr class="intro-video__divider" />';
                          if (!empty($videoTitle)) {
                            echo '<h3 class="intro-video__title">' . $videoTitle . '</h3>';
                          }
                          echo '<div class="intro-video__content"><iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoArr['v'] . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>';
                          echo '</div>';
                        }
                      }
                    ?>
                  </section><!--/#intro-message-->
									
                <?php elseif (get_row_layout() == 'bonus_deals_list'): ?>

									<section class="box_list">
                    <div class="wrapper prod_wrap main_page">
                      <div class="products">
                        <?php
                        $boxes = get_sub_field('boxes');
                        foreach ($boxes as $box) {

                          $box_type = $box['box_type'];
                          if ($box_type == 'Deal') {
                            $deal_id = $box['deal'];
                            $instance = new WC_product($deal_id);
                            $sym = get_woocommerce_currency_symbol();
                             $product_prices = vstbuzz_get_product_prices( $instance );
                             /**
                             * @var number $sale_price
                             * @var number $regular_price
                             * @var number $save_price
                             */
                             extract($product_prices);

                            $content_tab_off = get_field('content_tab_off', $deal_id);
                            $item_title = get_field('content_tab_by', $deal_id);
                            $sale_price_dates_from = get_post_meta($deal_id, '_sale_price_dates_from', true);
                            $sale_price_dates_to = get_post_meta($deal_id, '_sale_price_dates_to', true);
                            $image = get_the_post_thumbnail_url($deal_id, 'medium');

                            $backgroundImage = get_field('content_blocks', $deal_id)[0]['background_image'];
                            $link = get_permalink($deal_id);
	                          $description = wp_trim_words(get_field('content_blocks', $deal_id)[0]['description'], $num_words = 30, $more = '...');
                            $timerEnabled = vstbuzz_product_has_category($deal_id, 'Deals');
                          } else {
                            $now = time();
	                          $backgroundImage = $box['background_image'];
	                          $deal_id = rand(1000, 9999);
                            $link = $box['link'];
                            $description = wp_trim_words($box['flipside_text'], $num_words = 30, $more = '...');
                            $image = $box['front_image'];
                            $item_title = $box['front_text'];
                            $sale_price_dates_from = $now;
                            $timer_end_string = $box['timer_end'];
                            $date = new DateTime($timer_end_string, new DateTimeZone('Europe/Dublin'));
                            $timer_end = $date->format('U');
                            $sale_price_dates_to = $timer_end;
	                          $timerEnabled = !empty($timer_end_string);
	                          $sale_price = 0;
	                          $regular_price = 0;
	                          $save_price = 0;
	                          $sym = '';
	                          $content_tab_off = '';
                          }

                          ?>
                            <section style="background-image: url(<?php echo $backgroundImage; ?>);" class="product product-bonus-deal" id="product-<?php echo $deal_id; ?>-bottom">
                              <div class="product_hover">
                                <a href="<?php echo $link; ?>"></a>
                                <h3><a href="<?php echo $link; ?>"><?php echo $item_title; ?></a></h3>
                                <?php echo '<p>' . $description . '</p>'; ?>
                                <div class="hover_meta">
	                                <?php if($sale_price > 0 || $regular_price > 0) { ?>
                                    <div class="h_m_price">
                                      <?php echo $sym . $sale_price; ?>
                                    </div>
                                    <div class="values">
                                      <p>Real value: <span><?php echo $sym . $regular_price; ?></span></p>
                                      <p>You save: <span><?php echo $sym . $save_price; ?></span></p>
                                    </div>
                                  <?php } ?>
                                  <div class="link">
                                    <a href="<?php echo $link; ?>" class="button">Get it now</a>
                                  </div>
                                </div>
                              </div>
                              <div></div>
                              <a class="vert-align product__image" href="#"><img src="<?php echo $image; ?>" alt="<?php echo esc_attr($item_title); ?>"/></a>
                              <div class="vert-align product__details">
                                <?php if(!empty($content_tab_off)) { ?>
                                <h5 class="product__off"><?php echo $content_tab_off; ?></h5>
                                <?php } ?>
                                <h3 class="product__title"><a href="#" class="product__title-link"><?php echo $item_title; ?></a></h3>
                                <?php if($sale_price > 0) { ?>
                                  <div class="price product__price"><span><?php echo $sym . $sale_price; ?></span></div>
                                <?php } ?>
                                <?php if($regular_price > 0 && $regular_price > $sale_price) { ?>
                                <div class="values product__values">
                                  <p>Real value: <span><?php echo $sym . $regular_price; ?></span> You save: <span><?php echo $sym . $save_price; ?></span></p>
                                </div>
                                <?php } ?>
	                              <?php if ($timerEnabled) { ?>
                                <div class="meta product__timer">
                                    <div class="timer"><span></span>
                                      <script type="text/javascript">
                                        timer("<?php echo $sale_price_dates_from;?>", "<?php echo $sale_price_dates_to;?>", "<?php echo $deal_id . "-bottom"; ?>");
                                      </script>
                                    </div>
                                </div>
                              <?php } ?>
                              </div>
                            </section>
                            <?php }
														
														?>
									
													
									</section>
									
									
                <?php elseif (get_row_layout() == 'small_optin'):

                  $heading = get_sub_field('heading');
	                $subheading = get_sub_field('subheading');
	                $input_placeholder = get_sub_field('input_placeholder');
	                $button_text = get_sub_field('button_text');
	                $source_id = get_sub_field('source_id');
	                echo get_new_deals($source_id, "new-deals_home", $heading, $subheading, $input_placeholder, $button_text);

                elseif (get_row_layout() == 'logos'): ?>

                  <section class="entry logos">
                    <div class="wrapper">
                        <h3 class="logos__title"><?php echo get_sub_field('description'); ?></h3>
                      <?php
                      $images = get_sub_field('images');

                      if ($images): ?>
                        <ul>
                          <?php foreach ($images as $image): ?>
                            <li><img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>"/></li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == 'box_list'): ?>

                  <section class="box_list">
                    <div class="wrapper prod_wrap main_page">
                      <div class="products">
                        <?php
                        $boxes = get_sub_field('boxes');
                        foreach ($boxes as $box) {

                          $box_type = $box['box_type'];
                          if ($box_type == 'Deal') {
                            $deal_id = $box['deal'];
                            $instance = new WC_product($deal_id);
                            $sym = get_woocommerce_currency_symbol();
                             $product_prices = vstbuzz_get_product_prices( $instance );
                             /**
                             * @var number $sale_price
                             * @var number $regular_price
                             * @var number $save_price
                             */
                             extract($product_prices);

                            if ($sale_price == 0) {
                              $bundled_items = $wpdb->get_results("select * from {$wpdb->prefix}woocommerce_bundled_items where bundle_id = $deal_id and menu_order = 0");
                              foreach ($bundled_items as $bundled_item) {
                                $first_bundled_product = wc_get_product($bundled_item->product_id);
                                $sale_price = $first_bundled_product->get_sale_price();
                                if ( ! $sale_price ) {
                                  $sale_price = $first_bundled_product->get_attribute( 'sale_price' );
                                }
                                $save_price = $instance->get_regular_price() - $first_bundled_product->get_sale_price();
                              }
                            }

                            $content_tab_off = get_field('content_tab_off', $deal_id);
                            $item_title = get_field('content_tab_by', $deal_id);
                            $sale_price_dates_from = get_post_meta($deal_id, '_sale_price_dates_from', true);
                            $sale_price_dates_to = get_post_meta($deal_id, '_sale_price_dates_to', true);
                            $image = get_the_post_thumbnail_url($deal_id, 'medium');

                            $backgroundImage = get_field('content_blocks', $deal_id)[0]['background_image'];
                            $link = get_permalink($deal_id);
	                          $description = wp_trim_words(get_field('content_blocks', $deal_id)[0]['description'], $num_words = 30, $more = '...');
                            $timerEnabled = vstbuzz_product_has_category($deal_id, 'Deals');
                          } else {
                            $now = time();
	                          $backgroundImage = $box['background_image'];
	                          $deal_id = rand(1000, 9999);
                            $link = $box['link'];
                            $description = wp_trim_words($box['flipside_text'], $num_words = 30, $more = '...');
                            $image = $box['front_image'];
                            $item_title = $box['front_text'];
                            $sale_price_dates_from = $now;
                            $timer_end_string = $box['timer_end'];
                            $date = new DateTime($timer_end_string, new DateTimeZone('Europe/Dublin'));
                            $timer_end = $date->format('U');
                            $sale_price_dates_to = $timer_end;
	                          $timerEnabled = !empty($timer_end_string);
	                          $sale_price = 0;
	                          $regular_price = 0;
	                          $save_price = 0;
	                          $sym = '';
	                          $content_tab_off = '';
                          }

                          ?>
                            <section style="background-image: url(<?php echo $backgroundImage; ?>);" class="product" id="product-<?php echo $deal_id; ?>-bottom">
                              <div class="product_hover">
                                <a href="<?php echo $link; ?>"></a>
                                <h3><a href="<?php echo $link; ?>"><?php echo $item_title; ?></a></h3>
                                <?php echo '<p>' . $description . '</p>'; ?>
                                <div class="hover_meta">
	                                <?php if($sale_price > 0 || $regular_price > 0) { ?>
                                    <div class="h_m_price">
                                      <?php echo $sym . $sale_price; ?>
                                    </div>
                                    <div class="values">
                                      <p>Real value: <span><?php echo $sym . $regular_price; ?></span></p>
                                      <p>You save: <span><?php echo $sym . $save_price; ?></span></p>
                                    </div>
                                  <?php } ?>
                                  <div class="link">
                                    <a href="<?php echo $link; ?>" class="button">Get it now</a>
                                  </div>
                                </div>
                              </div>
                              <div></div>
                              <a class="vert-align product__image" href="#"><img src="<?php echo $image; ?>" alt="<?php echo esc_attr($item_title); ?>"/></a>
                              <div class="vert-align product__details">
                                <?php if(!empty($content_tab_off)) { ?>
                                <h5 class="product__off"><?php echo $content_tab_off; ?></h5>
                                <?php } ?>
                                <h3 class="product__title"><a href="#" class="product__title-link"><?php echo $item_title; ?></a></h3>
                                <?php if($sale_price > 0) { ?>
                                  <div class="price product__price"><span><?php echo $sym . $sale_price; ?></span></div>
                                <?php } ?>
                                <?php if($regular_price > 0 && $regular_price > $sale_price) { ?>
                                <div class="values product__values">
                                  <p>Real value: <span><?php echo $sym . $regular_price; ?></span> You save: <span><?php echo $sym . $save_price; ?></span></p>
                                </div>
                                <?php } ?>
	                              <?php if ($timerEnabled) { ?>
                                <div class="meta product__timer">
                                    <div class="timer"><span></span>
                                      <script type="text/javascript">
                                        timer("<?php echo $sale_price_dates_from;?>", "<?php echo $sale_price_dates_to;?>", "<?php echo $deal_id . "-bottom"; ?>");
                                      </script>
                                    </div>
                                </div>
                              <?php } ?>
                              </div>
                            </section>
                            <?php }
                        ?>

                          <?php
                          /**
                           * Show the final blowout sale banner
                           *
                          */

                          /* Disabled 2024.12.07 
                          $promopage = get_post(81902);
                            if(isset($_COOKIE['kontest']) || isset($_GET['promotest']) || ($promopage && $promopage->post_status === 'publish')) {
                              $salePageLink = get_permalink(81902);
                              ?>
                            <section class="blowout">
                              <div class="wrapper">
                                <div class="blowout__inner">
                                  <div class="blowout__content">
                                    <a href="<?php echo $salePageLink; ?>" class="blowout__title"><img src="/wp-content/themes/vstbuzz-v2/images/VSTBuzz1_white.png" alt="VSTBuzz" class="blowout__logo"> is Closing Down&hellip;</a>
                                    <a href="<?php echo $salePageLink; ?>"><img src="/wp-content/uploads/2024/07/final-blowout.png" alt="Final Blowout SALE" class="blowout__image" /></a>
                                    <p class="blowout__text">We've Brought Back Our Most Popular Deals from the Last 10 Years</p>
                                    <a href="<?php echo $salePageLink; ?>" class="blowout__button button btn vst-button">BROWSE DEALS</a>
                                  </div>
                                  <a href="<?php echo $salePageLink; ?>" class="blowout__max_discount">
                                    <h3 class="blowout__max_discount-title">up to</h3>
                                    <p class="blowout__max_discount-text">96%<br> off</p>
                                  </a>
                                </div>
                              </div>
                            </section>
                            <?php
                            }
                            */ ?>

                        <?php
//                        $pulsePromoClass = count($boxes) % 2 === 0 ? 'fullwidth' : 'item';
                        $pulsePromoClass = 'fullwidth';
                        ?>
                        <section class="product pulse pulse_promo pulse_<?php echo $pulsePromoClass; ?>">
                          <a class="wrapper_pulse" href="https://www.pulse.audio/?utm_source=vstbuzz&utm_medium=banner&utm_campaign=frontpage" target="_blank">
                            <div class="pulse__logo-wrap">
                              <img src="<?php echo get_stylesheet_directory_uri() . '/images/pulse-app-icon.svg'; ?>" class="pulse__logo" alt="Pulse audio" />
                              <h3 class="pulse__title">Pulse Audio</h3>
                            </div>
                            <span class="pulse__slogan">More Music Software Deals & Freebies</span>
                            <span class="pulse__button">Show</span>
                          </a>
                        </section>
<?php /*
                         <section class="product pulse pulse_<?php echo $pulsePromoClass; ?> pulse_msd">
                          <a class="wrapper_pulse" href="https://www.musicsoftwaredeals.com/?utm_source=vstbuzz&utm_medium=banner&utm_campaign=frontpage" target="_blank">
                            <div class="pulse__logo-wrap">
                              <img src="<?php echo get_stylesheet_directory_uri() . '/images/msd.svg?v'; ?>" class="pulse__logo" alt="Music Software Deals" />
                              <h3 class="pulse__title">Music Software Deals</h3>
                            </div>
                            <span class="pulse__slogan">Never pay full price for an audio plugin again</span>
                            <span class="pulse__button">Show</span>
                          </a>
                        </section>
*/ ?>
                      </div><!--/.products-->
                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == 'spotlights'): ?>

                  <section class="entry spotlights">
                    <div class="wrapper">

                      <?php
                      $images = get_sub_field('images');

                      if ($images): ?>
                        <ul>
                          <?php foreach ($images as $image): ?>
                            <li>
                              <a href="<?php echo $image['caption']; ?>">
                                <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>"/>
                                <h3><?php echo $image['title']; ?></h3>
                                <p><?php echo $image['description']; ?></p>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == 'faq'): ?>

                  <section class="entry faq">
                    <div class="wrapper">

                      <h3 class="question"><span></span><?php the_sub_field('question'); ?></h3>
                      <div class="answer">
                        <?php the_sub_field('answer'); ?>
                      </div>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == 'perk'): ?>
                      <div class="the-vault-item the-vault-item_perk">
                      <?php $image = get_sub_field('image'); ?>
                        <div class="the-vault-item__link">
                          <img src="<?php echo $image; ?>?featured" alt="<?php the_sub_field('title'); ?>" class="the-vault-item__link-image"/>
                        </div>
                        <h3 class="the-vault-product-title"><?php the_sub_field('title'); ?></h3>
                        <p><?php the_sub_field('description'); ?></p>
                        <?php if (is_user_logged_in()) { ?>
                        <?php $uniq = 'code-' . rand(10000, 99999); ?>
                            <input type="text" value="<?php the_sub_field('claim_code'); ?>" id="<?php echo $uniq; ?>" class="perk-code">
                            <button class="button button_copy" data-target="#<?php echo $uniq; ?>">Copy to clipboard</button>
                        <?php } else { ?>
                            <a class="simplemodal-register button">CLAIM</a>
                        <?php } ?>
                      </div>
                <?php elseif (get_row_layout() == 'slider'): ?>

                  <?php woo_featured_slider_loader_vstbuzz(); ?>

                <?php elseif (get_row_layout() == 'deals'): ?>

                  <section class="entry deals">
                    <div class="wrapper">

                      <?php
                      $args = array('post_type' => 'product', 'stock' => 1, 'posts_per_page' => 2,
                        'tax_query' => array(
                          array(
                            'taxonomy' => 'product_cat',
                            'field' => 'slug',
                            'terms' => array('store', 'never-expires', 'the-vault'),
                            'operator' => 'NOT IN'
                          ),
                        ),
                        'orderby' => 'date', 'order' => 'DESC');
                      $loop = new WP_Query($args);
                      $x = 1;
                      while ($loop->have_posts()) : $loop->the_post();
                        global $product; ?>

                        <?php if ($x == 1) {

                          $class = 'current';
                          $show_image = 1;
                          $h4 = 'Current Deal';
                          $limit = 100;

                        } else {

                          $class = 'past';
                          $show_image = 0;
                          $h4 = 'Previous Deal';
                          $limit = 80;

                        } ?>

                        <div class="<?php echo $class; ?>">

                          <?php if (have_rows('content_blocks')):

                            // loop through the rows of data
                            while (have_rows('content_blocks')) : the_row(); ?>

                              <?php if (get_row_layout() == 'intro'): ?>

                                <?php if ($show_image) { ?>
                                  <?php $image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())); ?>
                                  <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                    <img src="<?php echo $image; ?>" alt="<?php the_title(); ?>"/>
                                  </a>
                                <?php } ?>
                                <h4><?php echo $h4; ?></h4>
                                <h3><?php the_title(); ?></h3>

                                <p><?php echo myTruncate(strip_tags(get_sub_field('description')), $limit); ?></p>

                                <?php if ($x == 1) { ?>

                                  <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="submit">See Current Deal</a>

                                <?php } else { ?>

                                  <a href="/deals" class="submit">See All Past Deals</a>

                                <?php } ?>

                              <?php endif; ?>

                            <?php endwhile;

                          endif; ?>

                        </div>

                        <?php $x++; ?>

                      <?php endwhile; ?>
                      <?php wp_reset_query(); ?>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == 'free_products_promo'): ?>

               
                  <section class="free-products-promo">
	                  <h2 class="free-products-promo__title"><?php the_sub_field('title'); ?></h2>
                    <a href="<?php the_sub_field('button_link'); ?>" class="free-products-promo__button"><?php the_sub_field('button_title'); ?></a>
                    <a href="<?php the_sub_field('button_link'); ?>" style="margin-top: 50px;">
                      <img src="<?php the_sub_field('image'); ?>" class="free-products-promo__image" alt="<?php echo esc_attr(get_sub_field('button_title')); ?>">
                    </a>
                  </section>
                   
                   
                <?php elseif (get_row_layout() == 'products_promo_columns'):
                $bg = get_sub_field('background');
                if(!empty($bg)) {
                  $bg = ' style="background-image:url(\'' . $bg . '\')"';
                } else {
                  $bg = '';
                }
                ?>
                  <section class="products-promo-columns" <?php echo $bg; ?>>
                  <div class="wrapper">
                    <div class="products-promo-columns__col1">
                      <a href="<?php the_sub_field('button_link'); ?>">
                        <img src="<?php the_sub_field('image'); ?>" class="products-promo-columns__image" alt="<?php echo esc_attr(get_sub_field('button_title')); ?>">
                      </a>
                    </div>
                    <div class="products-promo-columns__col2">
	                  <h2 class="products-promo-columns__title"><?php the_sub_field('title'); ?></h2>
                    <a href="<?php the_sub_field('button_link'); ?>" class="products-promo-columns__button vst-button"><?php the_sub_field('button_title'); ?></a>
                    </div>
                  </div>
                  </section>
                <?php endif; ?>

                <?php endwhile; ?>

                <?php if (is_page('perks')) { ?>
              </div><!-- /.wrapper -->
              </div>
              </div>
            </section><!-- /.entry -->
          <?php } ?>

            <?php else: ?>

              <?php the_content(); ?>

            <?php endif; ?>

            <?php edit_post_link(__('{ Edit }', 'woothemes'), '<section class="entry"><div class="wrapper"><span class="small">', '</span></div></section>'); ?>

        </article><!-- /.post -->

        <?php
        // Determine wether or not to display comments here, based on "Theme Options".
        if (isset($woo_options['woo_comments']) && in_array($woo_options['woo_comments'], array('page', 'both'))) {
          comments_template();
        }

      } // End WHILE Loop
    } else {
      ?>
      <article <?php post_class(); ?>>
        <p><?php _e('Sorry, no posts matched your criteria.', 'woothemes'); ?></p>
      </article><!-- /.post -->
    <?php } // End IF Statement ?>

  </section><!-- /#main -->

  <?php woo_main_after(); ?>

  <?php get_sidebar(); ?>


</div><!-- /#content -->

<?php get_footer(); ?>
