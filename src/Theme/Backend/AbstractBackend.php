<?php

namespace Dashifen\WPTB\Theme\Backend;

use Dashifen\WPPB\Component\Backend\AbstractBackend as PluginBackend;
use Dashifen\WPTB\Theme\ThemeInterface;

/**
 * Class AbstractBackend
 * @package Dashifen\WPTB\Theme\Backend
 */
abstract class AbstractBackend extends PluginBackend {
	/**
	 * AbstractBackend constructor.
	 *
	 * @param ThemeInterface $theme
	 */
	public function __construct(ThemeInterface $theme) {
		parent::__construct($theme, null, null, null);
	}
	
	/**
	 * @return void
	 */
	final public function activate(): void {
		$this->raiseWarning("Unnecessary call to Backend::activate()");
	}
	
	/**
	 * @param string $message
	 */
	final private function raiseWarning(string $message) {
		trigger_error($message, E_WARNING);
	}
	
	/**
	 * @return void
	 */
	final public function deactivate(): void {
		$this->raiseWarning("Unnecessary call to Backend::deactivate()");
	}
	
	/**
	 * @return void
	 */
	final public function uninstall(): void {
		$this->raiseWarning("Unnecessary call to Backend::uninstall()");
	}
}
