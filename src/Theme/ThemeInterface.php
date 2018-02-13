<?php

namespace Dashifen\WPTB\Theme;

use Dashifen\WPPB\Controller\ControllerInterface;

/**
 * Interface ThemeInterface
 * @package Dashifen\WPTB\Theme
 */
interface ThemeInterface extends ControllerInterface {
	/**
	 * @return void
	 */
	public function initialize(): void;
	
	/**
	 * @return string
	 */
	public function getStylesheetDir(): string;
	
	/**
	 * @return string
	 */
	public function getStylesheetUrl(): string;
	
	/**
	 * @return string
	 */
	public function getTemplateDir(): string;
	
	/**
	 * @return string
	 */
	public function getTemplateUrl(): string;
	
	/**
	 * @param array $scripts
	 */
	public function addScripts(array $scripts): void;
	
	/**
	 * @param string $location
	 * @param string $script
	 * @param array  $dependencies
	 */
	public function addScript(string $location, string $script, array $dependencies = []): void;
	
	/**
	 * @param array $styles
	 */
	public function addStyles(array $styles): void;
	
	/**
	 * @param string $location
	 * @param string $style
	 * @param array  $dependencies
	 */
	public function addStyle(string $location, string $style, array $dependencies = []): void;
	
	/**
	 * @param array $sidebars
	 */
	public function addSidebars(array $sidebars): void;
}
