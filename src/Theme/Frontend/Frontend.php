<?php

namespace Dashifen\WPTB\Theme\Frontend;

use Dashifen\WPPB\Component\AbstractComponent as PluginComponent;
use Dashifen\WPTB\Theme\ThemeInterface;

/**
 * Class Frontend
 * @package Dashifen\WPTB\Theme\Frontend
 */
abstract class AbstractFrontend extends PluginComponent implements FrontendInterface {
	/**
	 * @var ThemeInterface $theme;
	 */
	protected $controller;
	
	/**
	 * Frontend constructor.
	 *
	 * @param ThemeInterface $theme
	 */
	public function __construct(ThemeInterface $theme) {
		parent::__construct($theme);
	}
	
	/**
	 * @return ThemeInterface
	 */
	public function getTheme(): ThemeInterface {
		
		// this function is provided to, essentially, make it less
		// clear that this property is named "controller" from the plugin
		// boilerplate and let people think of it as if it's a theme.
		
		return $this->controller;
	}
}
