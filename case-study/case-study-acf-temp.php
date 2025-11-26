<?php
/**
 * Case Study Template – ACF + Shortcodes
 */

class CaseStudyTMP1 {

	private static $acf_data = null;

	public function __construct() {
		add_action( 'init', [ $this, 'register_shortcodes' ] );
	}

	public function register_shortcodes() {
		add_shortcode( 'case_study_banner',   [ $this, 'get_banner' ] );
		add_shortcode( 'case_study_section_2',[ $this, 'get_section_2' ] );
		add_shortcode( 'case_study_section_3',[ $this, 'get_section_3' ] );
		add_shortcode( 'case_study_section_4',[ $this, 'get_section_4' ] );
		add_shortcode( 'case_study_section_5',[ $this, 'get_section_5' ] );
		add_shortcode( 'case_study_section_6',[ $this, 'get_section_6' ] );
	}

	/**
	 * Load ACF data once from Flexible Content: case_study_template
	 */
	private function run_data_once() {
		if ( self::$acf_data !== null ) {
			return;
		}

		self::$acf_data = [];

		if ( have_rows( 'case_study_template' ) ) {
			while ( have_rows( 'case_study_template' ) ) {
				the_row();

				if ( get_row_layout() === 'case_study_template_metabox' ) {

					// Banner
					self::$acf_data['banner_h1_heading']  = get_sub_field( 'banner_h1_heading' );
					self::$acf_data['banner_right_image'] = get_sub_field( 'banner_right_image' );

					// Section 2
					self::$acf_data['section_2_heading_text']   = get_sub_field( 'section_2_heading_text' );
					self::$acf_data['section_2_heading_tag']    = get_sub_field( 'section_2_heading_tag' );
					self::$acf_data['section_2_body_content']   = get_sub_field( 'section_2_body_content' );

					// Section 3
					self::$acf_data['section_3_heading_text']   = get_sub_field( 'section_3_heading_text' );
					self::$acf_data['section_3_heading_tag']    = get_sub_field( 'section_3_heading_tag' );
					self::$acf_data['section_3_button_text']    = get_sub_field( 'section_3_button_text' );
					self::$acf_data['section_3_button_url']     = get_sub_field( 'section_3_button_url' );

					// Section 4
					self::$acf_data['section_4_heading_text']   = get_sub_field( 'section_4_heading_text' );
					self::$acf_data['section_4_heading_tag']    = get_sub_field( 'section_4_heading_tag' );
					self::$acf_data['section_4_body_content']   = get_sub_field( 'section_4_body_content' );
					self::$acf_data['section_4_right_image']    = get_sub_field( 'section_4_right_image' );

					// Section 5
					self::$acf_data['section_5_heading_text']   = get_sub_field( 'section_5_heading_text' );
					self::$acf_data['section_5_heading_tag']    = get_sub_field( 'section_5_heading_tag' );
					self::$acf_data['section_5_body_content']   = get_sub_field( 'section_5_body_content' );
					self::$acf_data['section_5_cards']          = get_sub_field( 'section_5_cards' );

					// Section 6
					self::$acf_data['section_6_heading_text']   = get_sub_field( 'section_6_heading_text' );
					self::$acf_data['section_6_heading_tag']    = get_sub_field( 'section_6_heading_tag' );
					self::$acf_data['section_6_carousel_images']= get_sub_field( 'section_6_carousel_images' );
					self::$acf_data['section_6_body_content']   = get_sub_field( 'section_6_body_content' );

					break; // Only use the first matching layout
				}
			}
		}
	}

	/**
	 * Small helper to safely render a heading tag
	 */
	private function render_heading( $text, $tag = 'h2', $class = '' ) {
		$text = trim( (string) $text );
		if ( $text === '' ) {
			return '';
		}

		$allowed = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		if ( ! in_array( strtolower( $tag ), $allowed, true ) ) {
			$tag = 'h2';
		}

		$class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';

		return sprintf(
			'<%1$s%3$s>%2$s</%1$s>',
			esc_html( $tag ),
			esc_html( $text ),
			$class_attr
		);
	}

	/* -------------------------------------------------------------- *
	 * Banner
	 * -------------------------------------------------------------- */

	public function get_banner( $atts ) {
		$this->run_data_once();

		$atts = shortcode_atts( [
			'param' => '', // heading | image
		], $atts );

		$data = self::$acf_data;

		if ( $atts['param'] === 'heading' ) {
			return esc_html( $data['banner_h1_heading'] ?? '' );
		}

		if ( $atts['param'] === 'image' ) {
			$img = $data['banner_right_image'] ?? [];
			$url = $img['url'] ?? '';
			$alt = $img['alt'] ?? '';

			if ( ! $url ) {
				return '';
			}

			return sprintf(
				'<img src="%1$s" alt="%2$s" class="cs-banner-right-img" />',
				esc_url( $url ),
				esc_attr( $alt )
			);
		}

		return '';
	}

	/* -------------------------------------------------------------- *
	 * Section 2 – Heading + WYSIWYG
	 * -------------------------------------------------------------- */

	public function get_section_2( $atts ) {
		$this->run_data_once();

		$atts = shortcode_atts( [
			'param' => '', // heading | body
		], $atts );

		$data = self::$acf_data;

		if ( $atts['param'] === 'heading' ) {
			$text = $data['section_2_heading_text'] ?? '';
			$tag  = $data['section_2_heading_tag'] ?? 'h2';
			return $this->render_heading( $text, $tag, 'cs-sec2-heading' );
		}

		if ( $atts['param'] === 'body' ) {
			$body = $data['section_2_body_content'] ?? '';
			return $body ? wp_kses_post( $body ) : '';
		}

		return '';
	}

	/* -------------------------------------------------------------- *
	 * Section 3 – Heading + Button text/url
	 * -------------------------------------------------------------- */

	public function get_section_3( $atts ) {
		$this->run_data_once();

		$atts = shortcode_atts( [
			'param' => '', // heading | button_text | button_url
		], $atts );

		$data = self::$acf_data;

		if ( $atts['param'] === 'heading' ) {
			$text = $data['section_3_heading_text'] ?? '';
			$tag  = $data['section_3_heading_tag'] ?? 'h2';
			return $this->render_heading( $text, $tag, 'cs-sec3-heading' );
		}

		if ( $atts['param'] === 'button_text' ) {
			return esc_html( $data['section_3_button_text'] ?? '' );
		}

		if ( $atts['param'] === 'button_url' ) {
			return esc_url( $data['section_3_button_url'] ?? '' );
		}

		return '';
	}

	/* -------------------------------------------------------------- *
	 * Section 4 – Heading + WYSIWYG + Right Image
	 * -------------------------------------------------------------- */

	public function get_section_4( $atts ) {
		$this->run_data_once();

		$atts = shortcode_atts( [
			'param' => '', // heading | body | image
		], $atts );

		$data = self::$acf_data;

		if ( $atts['param'] === 'heading' ) {
			$text = $data['section_4_heading_text'] ?? '';
			$tag  = $data['section_4_heading_tag'] ?? 'h2';
			return $this->render_heading( $text, $tag, 'cs-sec4-heading' );
		}

		if ( $atts['param'] === 'body' ) {
			$body = $data['section_4_body_content'] ?? '';
			return $body ? wp_kses_post( $body ) : '';
		}

		if ( $atts['param'] === 'image' ) {
			$img = $data['section_4_right_image'] ?? [];
			$url = $img['url'] ?? '';
			$alt = $img['alt'] ?? '';

			if ( ! $url ) {
				return '';
			}

			return sprintf(
				'<img src="%1$s" alt="%2$s" class="cs-sec4-right-img" />',
				esc_url( $url ),
				esc_attr( $alt )
			);
		}

		return '';
	}

	/* -------------------------------------------------------------- *
	 * Section 5 – Heading + Body + Cards
	 * -------------------------------------------------------------- */

	public function get_section_5( $atts ) {
		$this->run_data_once();

		$atts = shortcode_atts( [
			'param' => '', // heading | body | cards
		], $atts );

		$data = self::$acf_data;

		if ( $atts['param'] === 'heading' ) {
			$text = $data['section_5_heading_text'] ?? '';
			$tag  = $data['section_5_heading_tag'] ?? 'h2';
			return $this->render_heading( $text, $tag, 'cs-sec5-heading' );
		}

		if ( $atts['param'] === 'body' ) {
			$body = $data['section_5_body_content'] ?? '';
			return $body ? wp_kses_post( $body ) : '';
		}

		if ( $atts['param'] === 'cards' ) {
			$cards = $data['section_5_cards'] ?? [];
			if ( empty( $cards ) ) {
				return '';
			}

			ob_start(); ?>
			<div class="cs-sec5-cards">
				<?php foreach ( $cards as $card ) :
					$title   = $card['card_heading_text']   ?? '';
					$tag     = $card['card_heading_tag']    ?? 'h3';
					$content = $card['card_body_content']   ?? '';
					?>
					<article class="cs-sec5-card">
						<?php
						if ( $title ) {
							echo $this->render_heading( $title, $tag, 'cs-sec5-card-title' );
						}
						if ( $content ) {
							echo '<div class="cs-sec5-card-body">' . wp_kses_post( $content ) . '</div>';
						}
						?>
					</article>
				<?php endforeach; ?>
			</div>
			<?php
			return ob_get_clean();
		}

		return '';
	}

	/* -------------------------------------------------------------- *
	 * Section 6 – Heading + Carousel Images + Body
	 * -------------------------------------------------------------- */

	public function get_section_6( $atts ) {
		$this->run_data_once();

		$atts = shortcode_atts( [
			'param' => '', // heading | carousel | body
		], $atts );

		$data = self::$acf_data;

		if ( $atts['param'] === 'heading' ) {
			$text = $data['section_6_heading_text'] ?? '';
			$tag  = $data['section_6_heading_tag'] ?? 'h2';
			return $this->render_heading( $text, $tag, 'cs-sec6-heading' );
		}

		if ( $atts['param'] === 'carousel' ) {
			$slides = $data['section_6_carousel_images'] ?? [];
			if ( empty( $slides ) ) {
				return '';
			}

			ob_start(); ?>
			<swiper-container
				class="cs-sec6-swiper"
				slides-per-view="auto"
				space-between="24"
				loop="true"
				speed="8000"
				autoplay-delay="0"
				autoplay-disable-on-interaction="false"
				free-mode="true"
			>
				<?php foreach ( $slides as $slide ) :
					$img     = $slide['slide_image'] ?? [];
					$url     = $img['url'] ?? '';
					$alt     = $img['alt'] ?? '';
					$caption = $slide['slide_caption'] ?? '';

					if ( ! $url ) {
						continue;
					}
					?>
					<swiper-slide class="cs-sec6-slide">
						<img src="<?php echo esc_url( $url ); ?>"
							 alt="<?php echo esc_attr( $alt ); ?>">
						<?php if ( $caption ) : ?>
							<p class="cs-sec6-slide-caption">
								<?php echo esc_html( $caption ); ?>
							</p>
						<?php endif; ?>
					</swiper-slide>
				<?php endforeach; ?>
			</swiper-container>
			<?php
			return ob_get_clean();
		}


		if ( $atts['param'] === 'body' ) {
			$body = $data['section_6_body_content'] ?? '';
			return $body ? wp_kses_post( $body ) : '';
		}

		return '';
	}
}

new CaseStudyTMP1();
