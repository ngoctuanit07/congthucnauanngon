<?php

// Security control for vulnerability attempts
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/*--------------------------------------------------------------------------------------------------------
    MINIFIER - increase your page load speed by minifying JavaScript and HTML
    ---------------------------------------------------------------------------------------------------------*/


class SBP_HTML_Minifier {
	// Minify settings
	protected $minify_css = true;
	protected $minify_js = false;
	protected $info_comment = true;
	protected $remove_comments = true;

	// Minify variables
	protected $html;

	public function __construct( $html ) {
		if ( ! empty( $html ) ) {
			$this->parseHTML( $html );
		}
	}

	public function parseHTML( $html ) {
		$this->html = $this->minifyHTML( $html );

		if ( $this->info_comment ) {
			$this->html .= "\n" . $this->bottomComment( $html, $this->html );
		}
	}

	protected function minifyHTML( $html ) {
		$pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
		preg_match_all( $pattern, $html, $matches, PREG_SET_ORDER );
		$overriding = false;
		$raw_tag    = false;
		// Variable reused for output
		$html = '';
		foreach ( $matches as $token ) {
			$tag = ( isset( $token['tag'] ) ) ? strtolower( $token['tag'] ) : null;

			$content = $token[0];

			if ( is_null( $tag ) ) {
				if ( ! empty( $token['script'] ) ) {
					$strip = $this->minify_js;
				} elseif ( ! empty( $token['style'] ) ) {
					$strip = $this->minify_css;
				} elseif ( $content == '<!--sbp-html-minifier no minifier-->' ) {
					$overriding = ! $overriding;

					// Don't print the comments
					continue;
				} elseif ( $this->remove_comments ) {
					if ( ! $overriding && $raw_tag != 'textarea' ) {
						// Remove any HTML comments, except MSIE conditional comments
						$content = preg_replace( '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content );
					}
				}
			} else {
				if ( $tag == 'pre' || $tag == 'textarea' ) {
					$raw_tag = $tag;
				} elseif ( $tag == '/pre' || $tag == '/textarea' ) {
					$raw_tag = false;
				} else {
					if ( $raw_tag || $overriding ) {
						$strip = false;
					} else {
						$strip = true;
						// Remove all empty attributes, except action, alt, content, src
						$content = preg_replace( '/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/',
							'$1',
							$content );
						// Remove all space before the end of self-closing XHTML tags
						// JavaScript excluded
						$content = str_replace( ' />', '/>', $content );
					}
				}
			}

			if ( $strip ) {
				$content = $this->removeWhiteSpace( $content );
			}

			$html .= $content;
		}

		return $html;
	}

	protected function removeWhiteSpace( $str ) {
		$str = str_replace( "\t", ' ', $str );
		$str = str_replace( "\n", '', $str );
		$str = str_replace( "\r", '', $str );

		while ( stristr( $str, '  ' ) ) {
			$str = str_replace( '  ', ' ', $str );
		}

		return $str;
	}

	protected function bottomComment( $raw, $minified ) {
		$raw      = strlen( $raw );
		$minified = strlen( $minified );
		$savings  = ( $raw - $minified ) / $raw * 100;
		$savings  = round( $savings, 2 );

		return '<!-- HTML minified; size reduced ' . $savings . '% (from ' . $raw . ' bytes down to ' . $minified . ' bytes) -->';
	}

	public function __toString() {
		return $this->html;
	}
}

function sbp_html_minifier_finish( $html ) {
	return new SBP_HTML_Minifier( $html );
}

function sbp_html_minifier_start() {
	ob_start( 'sbp_html_minifier_finish' );
}

add_action( 'get_header', 'sbp_html_minifier_start' );
