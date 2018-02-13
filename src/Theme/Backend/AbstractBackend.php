<?php

namespace Dashifen\WPTB\Theme\Backend;

use Dashifen\WPPB\Component\Backend\AbstractBackend as PluginBackend;
use Dashifen\WPTB\Theme\ThemeInterface;
use Dashifen\WPTB\Theme\Backend\Activator\Activator;
use Dashifen\WPTB\Theme\Backend\Deactivator\Deactivator;
use Dashifen\WPTB\Theme\Backend\Uninstaller\Uninstaller;

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
		
		// since many (most?) themes don't need to hook events to these,
		// we've stubbed them in the boilerplate so that the actual themes
		// made from it don't need to worry about them.  but, if someone
		// wants to use them, they can always extend things and overwrite
		// the stubs.
		
		parent::__construct($theme, new Activator($theme),
			new Deactivator($theme), New Uninstaller($theme));
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
