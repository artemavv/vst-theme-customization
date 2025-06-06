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
if (is_page(552236)) {
  get_header('dark');
} else {
  get_header();
}
global $woo_options;

if (post_password_required()) {
    echo get_the_password_form();
} else {
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
                <?php
                $custom_class = get_sub_field('custom_class');
                 ?>
                  <section class="entry text-media <?php echo $custom_class; ?>">
                    <div class="wrapper">

                      <?php if (get_sub_field('sub_heading')) { ?>
                        <h2><?php the_sub_field('sub_heading'); ?></h2>
                      <?php } ?>
                      <?php the_sub_field('content'); ?>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == '2_cols'): ?>
                <?php
                $custom_class = get_sub_field('custom_class');
                 ?>
                  <section class="entry two-cols  <?php echo $custom_class; ?>">
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

                <?php elseif (get_row_layout() == 'small_optin'):

                  $heading = get_sub_field('heading');
	                $subheading = get_sub_field('subheading');
	                $input_placeholder = get_sub_field('input_placeholder');
	                $button_text = get_sub_field('button_text');
	                $source_id = get_sub_field('source_id');
	                echo get_new_deals($source_id, "new-deals_home", $heading, $subheading, $input_placeholder, $button_text);

                elseif (get_row_layout() == 'logos'):
                $sliderID = md5(rand(1000, 9999));
                $alt_carousel = get_sub_field('alt_carousel');
                $wrapper_class = '';
                if($alt_carousel) {
                  $wrapper_class = 'logos__wrapper';
                }
                ?>
                  <section class="entry logos" id="<?php echo $sliderID; ?>">
                    <div class="wrapper">
                        <h3 class="logos__title"><?php echo get_sub_field('description'); ?></h3>
                        <?php
                        $content = get_sub_field('content');
                        if(!empty($content)) {
                          echo '<p class="logos__content">' . $content . '</p>';
                        }
                         ?>
                      <?php
                      $images = get_sub_field('images');

                      $classes = 'logos__carousel';
                      if(!$alt_carousel) {
                        $classes = "logos-owl owl-carousel";
                      }
                      if ($images): ?>
                        <div class="<?php echo $wrapper_class; ?>">
                        <ul class="<?php echo $classes; ?>">
                          <?php foreach ($images as $image): ?>
                            <li><img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>"/></li>
                          <?php endforeach; ?>
                        </ul>
                        <?php
                        // repeat logos for a smooth transition
                        if($alt_carousel) { ?>
                        <ul class="<?php echo $classes; ?>">
                          <?php foreach ($images as $image): ?>
                            <li><img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>"/></li>
                          <?php endforeach; ?>
                        </ul>
                        <?php } ?>
                        </div>
                      <?php endif; ?>

                    </div><!-- /.wrapper -->
                    <?php if(!$alt_carousel) { ?>
                      <script>
                      jQuery(document).ready(function($) {
                              $("#<?php echo $sliderID; ?> .owl-carousel.logos-owl").owlCarousel({
                              nav: true,
                              navText: ["<", ">"],
                              autoplay: true,
                              autoplayHoverPause: true,
                              loop: true,
                              responsive: {
                                0: {
                                  items: 3,
                                },
                                768: {
                                  items: 4,
                                },
                                1024: {
                                  items: 7,
                                },
                              },
                            });
                                            });
                      </script>
                    <?php } ?>
                  </section><!-- /.entry -->

                <?php elseif (get_row_layout() == 'box_list'): ?>

                  <section class="box_list">
                    <div class="wrapper prod_wrap main_page">
                      <div class="products">
                        <?php
                        $boxes = get_sub_field('boxes');
                        echo get_home_deals($boxes, $product_class = "");
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

                <?php elseif (get_row_layout() == 'box_list_2'): ?>
                  <section class="box_list_2">
                    <div class="wrapper main_page">
                        <?php
                        $boxes = get_sub_field('deals');
                        echo get_box_list_2($boxes, $product_class = "");
                        ?>
                        <?php
$next_deal = get_sub_field('next_deal');
if($next_deal) { ?>
  <section class="box-item box-item__coming-soon" id="product-new-deals">
                          <h4 class="box__manufacturer"><?php echo __('Coming soon', 'vstbuzz'); ?></h4>
                          <h3 class="box__title"><?php echo __('Next Deal in', 'vstbuzz'); ?></h3>
                          <?php
                          $timestamp = strtotime($next_deal);
                          if ($timestamp > time()) { ?>
                        <span class="timer"></span>
                        <script type="text/javascript">
                            timer("<?php echo time(); ?>", "<?php echo esc_attr($timestamp); ?>", "new-deals");
                        </script>
                            <?php
                          } else {
                            echo '<span class="timer">' . __('Coming soon', 'vstbuzz') . '</span>';
                          }
                          ?>
                          <div class="box__image">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/images/chest_full.png'; ?>" alt="<?php echo __('Next Deal in', 'vstbuzz'); ?>" />
                          </div>
                        </section>
  <?php
}
 ?>

                    </div><!-- /.wrapper -->
                  </section><!-- /.entry -->

                  <?php elseif (get_row_layout() == 'box_list_bonus_deals' ): ?>

<section class="box_list" id="bonus-deals">
  <div class="wrapper prod_wrap main_page" style="margin-bottom: 50px;">
<?php /*
  <h2 style="
    text-align: center;
    margin: 60px 30px;
    font-size: 40px;
    font-weight: bold;
    text-transform: uppercase;
">Check out these bonus deals!</h2>*/?>

    <div class="products bonus-deals">
      <?php
      $boxes = get_sub_field('boxes');
      echo get_home_deals($boxes, $product_class = "bonus-deal");
      ?>
  

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

               <?php
                $custom_class = get_sub_field('custom_class');
                ?>
                  <section class="free-products-promo <?php echo $custom_class; ?>">
                  <div class="free-products-promo__title-wrap">
                  <?php $subtitle = get_sub_field('subtitle');
                  if(!empty($subtitle)) { ?>
                    <p class="free-products-promo__subtitle"><?php echo $subtitle; ?></p>
                  <?php } ?>
	                  <a href="<?php the_sub_field('button_link'); ?>" class="free-products-promo__title" target="_blank"><?php the_sub_field('title'); ?></a>
	                  </div>
                    <a href="<?php the_sub_field('button_link'); ?>" class="free-products-promo__button" target="_blank"><?php the_sub_field('button_title'); ?></a>
                    <a href="<?php the_sub_field('button_link'); ?>" class="free-products-promo__image-link" target="_blank">
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
                $custom_class = get_sub_field('custom_class');
                ?>
                  <section class="products-promo-columns <?php echo $custom_class; ?>" <?php echo $bg; ?>>
                  <div class="wrapper">
                    <div class="products-promo-columns__col1">
                      <a href="<?php the_sub_field('button_link'); ?>" target="_blank">
                        <img src="<?php the_sub_field('image'); ?>" class="products-promo-columns__image" alt="<?php echo esc_attr(get_sub_field('button_title')); ?>">
                      </a>
                    </div>
                    <div class="products-promo-columns__col2">
	                  <h2 class="products-promo-columns__title"><?php the_sub_field('title'); ?></h2>
                    <a href="<?php the_sub_field('button_link'); ?>" class="products-promo-columns__button vst-button" target="_blank"><?php the_sub_field('button_title'); ?></a>
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
<?php
  }
 ?>

<?php get_footer(); ?>
