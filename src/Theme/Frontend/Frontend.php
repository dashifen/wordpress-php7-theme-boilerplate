<?php

namespace Dashifen\WPTB\Theme\Frontend;

use Dashifen\WPPB\Component\AbstractComponent;
use Dashifen\WPTB\Theme\ThemeInterface;

/**
 * Class Frontend
 * @package Dashifen\WPTB\Theme\Frontend
 */
class Frontend extends AbstractComponent {
	/**
	 * Frontend constructor.
	 *
	 * @param ThemeInterface $theme
	 */
	public function __construct(ThemeInterface $theme) {
		parent::__construct($theme);
	}
}
