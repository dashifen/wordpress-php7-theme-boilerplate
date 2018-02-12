<?php

namespace Dashifen\WPTB\Theme;

use Dashifen\WPPB\Controller\ControllerInterface;

interface ThemeInterface extends ControllerInterface {
	/**
	 * @return void
	 */
	public function initialize(): void;
}
