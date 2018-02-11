<?php

namespace Dashifen\WPTB\Theme;

use Dashifen\WPPB\Component\Backend\BackendInterface;
use Dashifen\WPPB\Controller\AbstractController;
use Dashifen\WPPB\Loader\LoaderException;
use Dashifen\WPPB\Loader\LoaderInterface;

abstract class AbstractTheme extends AbstractController implements ThemeInterface {
	/**
	 * @var string $templateDir
	 */
	protected $templateDir;
	
	/**
	 * @var
	 */
	protected $templateUrl;

	/**
	 * @var string $stylesheetDir
	 */
	protected $stylesheetDir;
	
	/**
	 * @var string $url
	 */
	protected $stylesheetUrl;
	
	/**
	 * @var bool $child
	 */
	protected $child = false;
	
	/**
	 * @var array $scripts
	 */
	protected $scripts = [];
	
	/**
	 * @var array $styles
	 */
	protected $styles = [];
	
	/**
	 * @var \WP_Theme $theme;
	 */
	protected $theme;
	
	/**
	 * AbstractTheme constructor.
	 *
	 * @param LoaderInterface $loader
	 *
	 * @throws ThemeException
	 */
	public function __construct(LoaderInterface $loader) {
		$this->templateDir = get_template_directory();
		$this->templateUrl = get_template_directory_uri();
		$this->stylesheetDir = get_stylesheet_directory();
		$this->stylesheetUrl = get_stylesheet_directory_uri();
		$this->theme = wp_get_theme();
		
		$this->child = $this->templateDir !== $this->stylesheetDir;
		
		parent::__construct($this->theme->version, $loader);
		
		$this->loadScripts();
		$this->loadStyles();
	}
	
	/**
	 * @param string $folder
	 * @param bool   $folderForBoth
	 *
	 * @return void
	 * @throws ThemeException
	 */
	protected function loadScripts(string $folder = "", bool $folderForBoth = false): void {
		$this->scripts = $this->findFiles("js", $folder, $folderForBoth);
	}
	
	/**
	 * @param string $extension
	 * @param string $folder
	 * @param bool   $folderForBoth
	 *
	 * @return array
	 * @throws ThemeException
	 */
	protected function findFiles(string $extension, string $folder, bool $folderForBoth): array {
		$absFolders = $this->findFolders($folder, $folderForBoth);
		
		$foundFiles = [];
		foreach ($absFolders as $location => $absFolder) {
			if (!is_dir($absFolder)) {
				throw new ThemeException("Unable to find $absFolder.",
					ThemeException::NOT_A_FOLDER);
			}
			
			$foundFiles[$location] = $this->findFilesInFolder($absFolder, $extension);
		}
	
		return $foundFiles;
	}
	
	/**
	 * @param string $folder
	 * @param bool   $folderForBoth
	 *
	 * @return array
	 */
	protected function findFolders(string $folder = "", bool $folderForBoth = false): array {
		
		// for both styles and scripts, we need to grab an array of folders
		// in which we look for files.  if our folder for both flag is set,
		// then we look inside that folder in both the template (parent) and
		// (child) theme.  otherwise, we use it only for the child.
		
		$foundFolders = [];
		if ($this->child) {
			$foundFolders["template"] = $folderForBoth
				? $this->templateDir . "/$folder"
				: $this->templateDir;
		}
		
		$foundFolders["theme"] = $this->stylesheetDir . "/$folder";
		return $foundFolders;
	}
	
	/**
	 * @param string $folder
	 * @param string $extension
	 *
	 * @return array
	 */
	protected function findFilesInFolder(string $folder, string $extension): array {
		$dirIterator = new \RecursiveDirectoryIterator($folder);
		$iteratorIterator = new \RecursiveIteratorIterator($dirIterator);
		
		// using our iterator iterator, we can get a list of files.  those
		// files are instances of the SplFileInfo object.  that means we
		// can easily identify the file's extension, filename, and path
		// using that objects methods.
		
		$foundFiles = [];
		foreach ($iteratorIterator as $file) {
			/** @var \SplFileInfo $file */
			
			if ($file->getExtension() === $extension) {
			
				// so, if we find a file that has the right extension, then
				// we want to add it to our array of $foundFiles.  we index
				// the array with our filename and the value is the full path
				// to it.
				
				$foundFiles[$file->getFilename()] = $file->getRealPath();
			}
		}
	
		return $foundFiles;
	}
	
	/**
	 * @param string $folder
	 * @param bool   $folderForBoth
	 *
	 * @return void
	 * @throws ThemeException
	 */
	protected function loadStyles(string $folder = "", bool $folderForBoth = false): void {
		$this->styles = $this->findFiles("css", $folder, $folderForBoth);
	}
	
	/**
	 * @return void
	 * @throws LoaderException
	 */
	public function initialize(): void {
		
		// initializing a theme is as simple as defining its frontend
		// hooks and then attaching them to WordPress.  at this time,
		// we're assuming (temporarily) that we're not doing things to
		// the WordPress backend because this is a theme and not a
		// plugin.
		
		$this->defineFrontendHooks();
		$this->attachHooks();
	}
	
	/*
	 * UTILITY FUNCTIONS
	 * Here's the bread and butter of this object's purpose:  to help theme
	 * authors do common tasks without having to worry about the exact syntax
	 * over and over again.  these methods are protected; we don't need the
	 * WordPress ecosystem at large to know about them.
	 */
	
	protected function addScripts(array $scripts): void {
		
		// to add scripts, we get an array indexed by the scripts we're
		// adding and the values at those indices are the dependencies for
		// the scripts.  so something like ["theme.js" => ["jquery"]] would
		// indicate that we want to load the theme.js file which depends on
		// jquery.
		
		foreach ($scripts as $script => $dependencies) {
			$this->addScript($script, $dependencies);
		
		
		
		}
		
		
	}
	
	
	
	/*
	 * BOILERPLATE FUNCTIONS
	 * The following are all inherited from the plugin boilerplate.  Not
	 * all of them are pertinent to themes, but many of them are abstract
	 * so we want to do something with them to avoid forcing those using
	 * this boilerplate to do so.
	 */
	
	public function getName(): string {
		return $this->theme->title;
	}
	
	public function getFilename(): string {
		return $this->theme->stylesheet;
	}
	
	public function getSettingsSlug(): string {
		return $this->getSanitizedName();
	}
	
	final public function getBackend(): ?BackendInterface {
		$this->raiseWarning(__METHOD__);
		return null;
	}

	final protected function defineBackendHooks(): void {
		$this->raiseWarning(__METHOD__);
	}
	
	protected function raiseWarning(string $method): bool {
		
		// it's tempting to make this method final as well as the prior
		// two.  but, since we're putting an English error message in here,
		// it seems better to leave it as something themes can overwrite
		// and run through the i18n API should they need to.
		
		return trigger_error("Inappropriate use of $method in theme.", E_WARNING);
	}
}
