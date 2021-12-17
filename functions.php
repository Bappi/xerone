<?php

/*	-----------------------------------------------------------------------------------------------
	THEME SUPPORTS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_setup' ) ) :
	function zero-cool_setup() {

		load_theme_textdomain( 'zero-cool', get_template_directory() . '/languages' );
		set_post_thumbnail_size( 1792, 9999 );

	}
	add_action( 'after_setup_theme', 'zero-cool_setup' );
endif;


/*	-----------------------------------------------------------------------------------------------
	ENQUEUE STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_styles' ) ) :
	function zero-cool_styles() {

		wp_register_style( 'zero-cool-styles-google-fonts', 	zero-cool_get_google_fonts_url() );
		wp_register_style( 'zero-cool-styles-shared', 		get_template_directory_uri() . '/assets/css/shared.css' );
		wp_register_style( 'zero-cool-styles-blocks', 		get_template_directory_uri() . '/assets/css/blocks.css' );

		$dependencies = apply_filters( 'zero-cool_style_dependencies', array( 'zero-cool-styles-shared', 'zero-cool-styles-blocks', 'zero-cool-styles-google-fonts' ) );

		wp_enqueue_style( 'zero-cool-styles-front-end', get_template_directory_uri() . '/assets/css/front-end.css', $dependencies, wp_get_theme( 'zero-cool' )->get( 'Version' ) );

	}
	add_action( 'wp_enqueue_scripts', 'zero-cool_styles' );
endif;


/*	-----------------------------------------------------------------------------------------------
	ENQUEUE EDITOR STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_editor_styles' ) ) :
	function zero-cool_editor_styles() {

		add_editor_style( array( 
			'./assets/css/editor.css',
			'./assets/css/blocks.css',
			'./assets/css/shared.css',
			zero-cool_get_google_fonts_url()
		) );

	}
	add_action( 'admin_init', 'zero-cool_editor_styles' );
endif;


/*	-----------------------------------------------------------------------------------------------
	GET GOOGLE FONTS URL
	Builds a Google Fonts request URL from the Google Fonts families used in theme.json.
	Based on a solution in the Blockbase theme (see readme.txt for licensing info).
 
 	@return $fonts_url
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_get_google_fonts_url' ) ) : 
	function zero-cool_get_google_fonts_url() {

		if ( ! class_exists( 'WP_Theme_JSON_Resolver_Gutenberg' ) ) return '';

		$theme_data = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data()->get_settings();

		if ( empty( $theme_data['typography']['fontFamilies'] ) ) return '';

		$theme_families 	= ! empty( $theme_data['typography']['fontFamilies']['theme'] ) ? $theme_data['typography']['fontFamilies']['theme'] : array();
		$user_families 		= ! empty( $theme_data['typography']['fontFamilies']['user'] ) ? $theme_data['typography']['fontFamilies']['user'] : array();
		$font_families 		= array_merge( $theme_families, $user_families );

		if ( ! $font_families ) return '';

		$font_family_urls = array();

		foreach ( $font_families as $font_family ) {
			if ( ! empty( $font_family['google'] ) ) $font_family_urls[] = $font_family['google'];
		}

		if ( ! $font_family_urls ) return '';

		// Return a single request URL for all of the font families.
		return apply_filters( 'zero-cool_google_fonts_url', esc_url_raw( 'https://fonts.googleapis.com/css2?' . implode( '&', $font_family_urls ) . '&display=swap' ) );

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	BLOCK PATTERNS
	Register theme specific block patterns.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_register_block_patterns' ) ) : 
	function zero-cool_register_block_patterns() {

		if ( ! ( function_exists( 'register_block_pattern_category' ) && function_exists( 'register_block_pattern' ) ) ) return;

		// The block pattern categories included in zero-cool.
		$zero-cool_block_pattern_categories = apply_filters( 'zero-cool_block_pattern_categories', array(
			'zero-cool-blog' => array(
				'label'			=> esc_html__( 'zero-cool Blog', 'zero-cool' ),
			),
			'zero-cool-cta'  => array(
				'label'			=> esc_html__( 'zero-cool Call to Action', 'zero-cool' ),
			),
			'zero-cool-footer' => array(
				'label'			=> esc_html__( 'zero-cool Footer', 'zero-cool' ),
			),
			'zero-cool-general' => array(
				'label'			=> esc_html__( 'zero-cool General', 'zero-cool' ),
			),
			'zero-cool-header' => array(
				'label'			=> esc_html__( 'zero-cool Header', 'zero-cool' ),
			),
			'zero-cool-hero' => array(
				'label'			=> esc_html__( 'zero-cool Hero', 'zero-cool' ),
			),
			'zero-cool-restaurant' => array(
				'label'			=> esc_html__( 'zero-cool Restaurant', 'zero-cool' ),
			),
		) );

		// Sort the block pattern categories alphabetically based on the label value, to ensure alphabetized order when the strings are localized.
		uasort( $zero-cool_block_pattern_categories, function( $a, $b ) { 
			return strcmp( $a["label"], $b["label"] ); }
		);

		// Register block pattern categories.
		foreach ( $zero-cool_block_pattern_categories as $slug => $settings ) {
			register_block_pattern_category( $slug, $settings );
		}

		// viewportWidth values, determining the width of the preview in the Block Patterns drawer.
		$viewport = apply_filters( 'zero-cool_block_patterns_viewport', array(
			'full'			=> 1440,
			'wide'			=> 1312,
			'wide_grouped'	=> 1180,
			'content'		=> 640
		) );

		// The block patterns included in zero-cool.
		$zero-cool_block_patterns = apply_filters( 'zero-cool_block_patterns', array(

			/* BLOG */

			'zero-cool/blog-grid-cols-2' => array(
				'title'         => esc_html__( 'Two column grid with featured image, title, and post date', 'zero-cool' ),
				'categories'    => array( 'zero-cool-blog' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'blog/blog-grid-cols-2' ),
			),
			'zero-cool/blog-grid-cols-3' => array(
				'title'         => esc_html__( 'Three column grid with featured image, title, and post date', 'zero-cool' ),
				'categories'    => array( 'zero-cool-blog' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'blog/blog-grid-cols-3' ),
			),
			'zero-cool/blog-list' => array(
				'title'         => esc_html__( 'List with featured image, title, excerpt, and post date.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-blog' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'blog/blog-list' ),
			),
			'zero-cool/blog-list-compact' => array(
				'title'         => esc_html__( 'Compact list with title and post date.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-blog' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'blog/blog-list-compact' ),
			),
			'zero-cool/blog-list-compact-media' => array(
				'title'         => esc_html__( 'Compact list with featured image, title, and post date.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-blog' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'blog/blog-list-compact-media' ),
			),

			/* CALL TO ACTION */

			'zero-cool/cta-horizontal' => array(
				'title'         => esc_html__( 'Horizontal call to action.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-cta' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'cta/cta-horizontal' ),
			),
			'zero-cool/cta-vertical' => array(
				'title'         => esc_html__( 'Vertical call to action.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-cta' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'cta/cta-vertical' ),
			),

			/* FOOTER */

			'zero-cool/footer-horizontal' => array(
				'title'         => esc_html__( 'Footer with site title and theme credit in a centered paragraph. This is the default footer in the theme.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-horizontal' ),
			),
			'zero-cool/footer-horizontal-social' => array(
				'title'         => esc_html__( 'Footer with site title, theme credit and social icons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-horizontal-social' ),
			),
			'zero-cool/footer-horizontal-columns-1' => array(
				'title'         => esc_html__( 'Footer with site title, menu, opening hours and contact information in four columns.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-horizontal-columns-1' ),
			),
			'zero-cool/footer-horizontal-columns-2' => array(
				'title'         => esc_html__( 'Footer with site title, menu, opening hours, contact information and social icons in two columns.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-horizontal-columns-2' ),
			),
			'zero-cool/footer-horizontal-columns-3' => array(
				'title'         => esc_html__( 'Footer with site title, information blocks for two business locations, menu and social icons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-horizontal-columns-3' ),
			),
			'zero-cool/footer-horizontal-columns-4' => array(
				'title'         => esc_html__( 'Footer with site title, contact information, and two menus.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-horizontal-columns-4' ),
			),
			'zero-cool/footer-stacked-centered' => array(
				'title'         => esc_html__( 'Footer with site title, theme credit and social icons stacked and centered.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-footer' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'footer/footer-stacked-centered' ),
			),

			/* GENERAL */

			'zero-cool/general-faq' => array(
				'title'         => esc_html__( 'Frequently Asked Questions (FAQ) section.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['full'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-faq' ),
			),
			'zero-cool/general-feature-large' => array(
				'title'         => esc_html__( 'Full-width feature section with headings, text, and buttons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-feature-large' ),
			),
			'zero-cool/general-follow-us-vertical' => array(
				'title'         => esc_html__( 'Follow us section with a vertical layout.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['content'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-follow-us-vertical' ),
			),
			'zero-cool/general-follow-us-horizontal' => array(
				'title'         => esc_html__( 'Follow us section with a horizontal layout.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-follow-us-horizontal' ),
			),
			'zero-cool/general-information-banner' => array(
				'title'         => esc_html__( 'Information banner.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-information-banner' ),
			),
			'zero-cool/general-media-text-button' => array(
				'title'         => esc_html__( 'Media and text with button.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-media-text-button' ),
			),
			'zero-cool/general-previews-featured' => array(
				'title'         => esc_html__( 'Large featured section for the latest sticky post on the site.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-previews-featured' ),
			),
			'zero-cool/general-previews-columns' => array(
				'title'         => esc_html__( 'Latest news section with three posts.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-previews-columns' ),
			),
			'zero-cool/general-previews-columns-small' => array(
				'title'         => esc_html__( 'Compact latest news section with three posts.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-previews-columns-small' ),
			),
			'zero-cool/general-pricing-table' => array(
				'title'         => esc_html__( 'Pricing table with three tiers.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-pricing-table' ),
			),
			'zero-cool/general-testimonials-columns' => array(
				'title'         => esc_html__( 'Testimonials section with three quotes.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-general', 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'general/general-testimonials-columns' ),
			),

			/* HEADER */

			'zero-cool/header-horizontal' => array(
				'title'         => esc_html__( 'Header with site title and a menu. This is the default header in the theme.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-header' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'header/header-horizontal' ),
			),
			'zero-cool/header-horizontal-button' => array(
				'title'         => esc_html__( 'Header with site title, a menu and buttons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-header' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'header/header-horizontal-button' ),
			),
			'zero-cool/header-horizontal-social' => array(
				'title'         => esc_html__( 'Header with site title, a menu and social icons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-header' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'header/header-horizontal-social' ),
			),
			'zero-cool/header-horizontal-double' => array(
				'title'         => esc_html__( 'Header with opening hours, button, site title and a menu.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-header' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'header/header-horizontal-double' ),
			),
			'zero-cool/header-horizontal-double-deluxe' => array(
				'title'         => esc_html__( 'Header with opening hours, site title, social icons, a menu and a Call us button.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-header' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'header/header-horizontal-double-deluxe' ),
			),
			'zero-cool/header-stacked-centered' => array(
				'title'         => esc_html__( 'Header with site title, a menu and social icons stacked and centered.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-header' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'header/header-stacked-centered' ),
			),

			/* HERO */

			'zero-cool/hero-cover' => array(
				'title'         => esc_html__( 'Hero with a background image, a heading and buttons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-hero' ),
				'viewportWidth' => $viewport['full'],
				'content'       => zero-cool_get_block_pattern_markup( 'hero/hero-cover' ),
			),
			'zero-cool/hero-cover-group-bg' => array(
				'title'         => esc_html__( 'Hero with a background image and a heading, paragraph of text, and buttons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-hero' ),
				'viewportWidth' => $viewport['full'],
				'content'       => zero-cool_get_block_pattern_markup( 'hero/hero-cover-group-bg' ),
			),
			'zero-cool/hero-text' => array(
				'title'         => esc_html__( 'Hero with headings, a paragraph of text, and buttons.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-hero' ),
				'viewportWidth' => $viewport['content'],
				'content'       => zero-cool_get_block_pattern_markup( 'hero/hero-text' ),
			),
			'zero-cool/hero-text-displaced' => array(
				'title'         => esc_html__( 'Hero with a large heading to the left and text to the right.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-hero' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'hero/hero-text-displaced' ),
			),

			/* RESTAURANT */

			'zero-cool/restaurant-featured-dish' => array(
				'title'         => esc_html__( 'Promotional section for a featured dish.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-featured-dish' ),
			),
			'zero-cool/restaurant-location' => array(
				'title'         => esc_html__( 'Information block for a restaurant or café location.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-location' ),
			),
			'zero-cool/restaurant-menu' => array(
				'title'         => esc_html__( 'Restaurant menu with columns for three different menu sections.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-menu' ),
			),
			'zero-cool/restaurant-menu-row' => array(
				'title'         => esc_html__( 'A row with three columns for the restaurant menu.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide_grouped'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-menu-row' ),
			),
			'zero-cool/restaurant-opening-hours' => array(
				'title'         => esc_html__( 'A table with opening hours.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['content'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-opening-hours' ),
			),
			'zero-cool/restaurant-opening-hours-big' => array(
				'title'         => esc_html__( 'A large section with opening hours.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-opening-hours-big' ),
			),
			'zero-cool/restaurant-reservation-big' => array(
				'title'         => esc_html__( 'A really big button for reservations.', 'zero-cool' ),
				'categories'    => array( 'zero-cool-restaurant' ),
				'viewportWidth' => $viewport['wide'],
				'content'       => zero-cool_get_block_pattern_markup( 'restaurant/restaurant-reservation-big' ),
			),

		) );

		// Register block patterns.
		foreach ( $zero-cool_block_patterns as $slug => $settings ) {
			register_block_pattern( $slug, $settings );
		}
	
	}
	add_action( 'init', 'zero-cool_register_block_patterns' );
endif;


/*	-----------------------------------------------------------------------------------------------
	GET BLOCK PATTERN MARKUP
	Returns the markup of the block pattern at the specified theme path.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_get_block_pattern_markup' ) ) : 
	function zero-cool_get_block_pattern_markup( $pattern_name ) {

		$path = 'inc/block-patterns/' . $pattern_name . '.php';

		if ( ! locate_template( $path ) ) return;

		ob_start();
		include( locate_template( $path ) );
		return ob_get_clean();

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	BLOCK STYLES
	Register theme specific block styles.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'zero-cool_register_block_styles' ) ) :
	function zero-cool_register_block_styles() {

		if ( ! function_exists( 'register_block_style' ) ) return;

		// Shared: Shaded.
		$supports_shaded_block_style = apply_filters( 'zero-cool_supports_shaded_block_style', array( 'core/columns', 'core/group', 'core/image', 'core/media-text', 'core/social-links' ) );

		foreach ( $supports_shaded_block_style as $block_name ) {
			register_block_style( $block_name, array(
				'name'  	=> 'zero-cool-shaded',
				'label' 	=> esc_html__( 'Shaded', 'zero-cool' ),
			) );
		}

		// Button: Plain
		register_block_style( 'core/button', array(
			'name'  	=> 'zero-cool-plain',
			'label' 	=> esc_html__( 'Plain', 'zero-cool' ),
		) );

		// Columns: Separators
		register_block_style( 'core/columns', array(
			'name'  	=> 'zero-cool-horizontal-separators',
			'label' 	=> esc_html__( 'Horizontal Separators', 'zero-cool' ),
		) );

		// Query Pagination: Vertical separators
		register_block_style( 'core/query-pagination', array(
			'name'  	=> 'zero-cool-vertical-separators',
			'label' 	=> esc_html__( 'Vertical Separators', 'zero-cool' ),
		) );

		// Query Pagination: Top separator
		register_block_style( 'core/query-pagination', array(
			'name'  	=> 'zero-cool-top-separator',
			'label' 	=> esc_html__( 'Top Separator', 'zero-cool' ),
		) );

		// Table: Vertical borders
		register_block_style( 'core/table', array(
			'name'  	=> 'zero-cool-vertical-borders',
			'label' 	=> esc_html__( 'Vertical Borders', 'zero-cool' ),
		) );
		
	}
	add_action( 'init', 'zero-cool_register_block_styles' );
endif;
