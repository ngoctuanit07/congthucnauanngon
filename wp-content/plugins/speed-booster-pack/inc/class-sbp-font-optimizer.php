<?php

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class SBP_Font_Optimizer {
	private $families;
	private $subsets;

	public function __construct() {
		add_action( 'template_redirect', [ $this, 'start_buffer' ] );
	}

	public function start_buffer() {
		if ( ! $this->should_run() ) {
			return;
		}
		ob_start( [ $this, 'process_google_fonts' ] );
	}

	public function process_google_fonts( $html ) {
		preg_match_all( "/<link[^<>\/]+href=['\"?]((https?:)?\/\/fonts\.googleapis\.com\/css\?(.*?))['\"?].*?>/is", $html, $matches );
		if ( ! isset( $matches[1] ) || empty( $matches[1] ) ) {
			return $html;
		}

		$urls = $matches[1];

		// Process each url
		foreach ( $urls as $url ) {
			$attributes = $this->parse_attributes( $url );

			if ( isset( $attributes['family'] ) ) {
				$this->parse_family( $attributes['family'] );
			}

			if ( isset( $attributes['subset'] ) ) {
				$this->parse_subset( $attributes['subset'] );
			}
		}

		$html     = preg_replace( "/<link[^<>\/]+href=['\"?]((https?:)?\/\/fonts\.googleapis\.com\/css\?(.*?))['\"?].*?>/i", '', $html );
		$link_tag = $this->create_tag();
		$html     = str_replace( '</head>', '<link rel="dns-prefetch" href="//fonts.googleapis.com" />' . PHP_EOL . '<link rel="dns-prefetch" href="//fonts.gstatic.com" />' . PHP_EOL . $link_tag . PHP_EOL . '</head>', $html );

		return $html;
	}

	private function should_run() {
		global $sbp_options;

		if ( is_embed() ) {
			return false;
		}

		if ( ! isset( $sbp_options['sbp_optimize_fonts'] ) || ! $sbp_options['sbp_optimize_fonts'] ) {
			return false;
		}

		return true;
	}

	private function parse_attributes( $url ) {
		$url = htmlspecialchars_decode( $url );
		parse_str( parse_url( $url )['query'], $attributes );

		return $attributes;
	}

	private function parse_family( $family ) {
		$families = explode( '|', $family ); // if there is no pipe, explode will return 1 element array
		foreach ( $families as $family ) {
			if ( strpos( $family, ':' ) !== false ) {
				$family                            = explode( ':', $family );
				$name                              = $family[0];
				$this->families[ $name ]['name'] = $name;

				// Explode sizes
				$sizes                            = $family[1];
				$sizes                            = explode( ',', $sizes );
				foreach ($sizes as $size) {
					$this->families[ $name ]['sizes'][] = $size;
				}
			} else {
				$this->families[ $family ]['name']  = $family;
				$this->families[ $family ]['sizes'] = array_merge( $this->families[ $family ]['sizes'], [] );
			}
		}
	}

	private function parse_subset( $subset ) {
		$subsets = explode( ',', $subset );
		foreach ( $subsets as $subset ) {
			$this->subsets[] = $subset;
		}
	}

	private function create_tag() {
		// parse families
		$families = [];
		foreach ( $this->families as $family ) {
			if ( isset( $family['sizes'] ) && ! empty( $family['sizes'] ) ) {
				$family['sizes'] = array_unique($family['sizes']);
				$families[] .= $family['name'] . ":" . implode( ',', $family['sizes'] );
			} else {
				$families[] .= $family['name'];
			}
		}

		$families = implode( '|', $families );

		// parse subsets
		$subsets = implode( ",", array_unique( $this->subsets ) );

		$attributes = []; // Don't put attributes that doesn't exists
		if ($families) {
			$attributes[] = 'family=' . esc_attr($families);
		}
		if ($subsets) {
			$attributes[] = 'subset=' . esc_attr($subsets);
		}
		$attributes[] = 'display=swap';

		return '<link href="https://fonts.googleapis.com/css?' . implode('&', $attributes) . '" rel="stylesheet">';
	}
}

new SBP_Font_Optimizer();