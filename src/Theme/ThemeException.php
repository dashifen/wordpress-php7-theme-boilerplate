<?php

namespace Dashifen\WPTB\Theme;

use Dashifen\Exception\Exception;

class ThemeException extends Exception {
	public const NOT_A_FOLDER = 1;
	public const UNKNOWN_SCRIPT = 2;
	public const UNKNOWN_STYLE = 3;
}
