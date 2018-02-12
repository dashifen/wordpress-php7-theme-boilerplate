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
}
