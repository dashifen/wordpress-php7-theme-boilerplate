<?php

namespace Dashifen\WPTB\Theme\Frontend;

use Dashifen\WPPB\Component\ComponentInterface;
use Dashifen\WPTB\Theme\ThemeInterface;

/**
 * Interface FrontendInterface
 * @package Dashifen\WPTB\Theme\Frontend
 */
interface FrontendInterface extends ComponentInterface {
	/**
	 * @return ThemeInterface
	 */
	public function getTheme(): ThemeInterface;
}
