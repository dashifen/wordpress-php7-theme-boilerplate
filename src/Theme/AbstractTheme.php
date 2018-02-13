<?php

namespace Dashifen\WPTB\Theme;

use Dashifen\WPPB\Controller\AbstractController;
use Dashifen\WPPB\Loader\LoaderInterface;

/**
 * Class AbstractTheme
 * @package Dashifen\WPTB\Theme
 */
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
	 * @var \WP_Theme $theme ;
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
		$this->child = $this->templateDir !== $this->stylesheetDir;
		$this->theme = wp_get_theme();
		
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
			
			$foundFiles[$location] = $this->findFilesInFolder($location, $absFolder, $extension);
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
	 * @param string $location
	 * @param string $folder
	 * @param string $extension
	 *
	 * @return array
	 */
	protected function findFilesInFolder(string $location, string $folder, string $extension): array {
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
				// the array with our filename and the value is both the real
				// path and the web path, the latter of which we construct
				// using string replacement.
				
				$realPath = $file->getRealPath();
				$webPath = $location !== "template"
					? str_replace($this->stylesheetDir, $this->stylesheetUrl, $realPath)
					: str_replace($this->templateDir, $this->templateUrl, $realPath);
				
				$foundFiles[$file->getFilename()] = [
					"realPath" => $realPath,
					"webPath"  => $webPath,
				];
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
	 * @return string
	 */
	public function getName(): string {
		return $this->theme->title;
	}
	
	/**
	 * @return string
	 */
	public function getFilename(): string {
		return $this->theme->stylesheet;
	}
	
	
	/**
	 * @return string
	 */
	public function getSettingsSlug(): string {
		return $this->getSanitizedName();
	}
	
	/**
	 * @return string
	 */
	public function getStylesheetDir(): string {
		return $this->stylesheetDir;
	}
	
	/**
	 * @return string
	 */
	public function getStylesheetUrl(): string {
		return $this->stylesheetUrl;
	}
	
	/**
	 * @return string
	 */
	public function getTemplateDir(): string {
		return $this->templateDir;
	}
	
	/**
	 * @return string
	 */
	public function getTemplateUrl(): string {
		return $this->templateUrl;
	}
	
	/**
	 * @param array $scripts
	 *
	 * @return void
	 * @throws ThemeException
	 */
	public function addScripts(array $scripts): void {
		
		// to add scripts, we get a specifically structured multi-
		// dimensional array.  the value of each index is the location,
		// script's filename, and an array of dependencies.  in a perfect
		// world, or a future version, we may change this to an array of
		// Script objects, but that can wait for now.
		
		foreach ($scripts as $script) {
			list($location, $script, $dependencies) = $script;
			$this->addScript($location, $script, $dependencies);
		}
	}
	
	/**
	 * @param string $location
	 * @param string $script
	 * @param array  $dependencies
	 *
	 * @return void
	 * @throws ThemeException
	 */
	public function addScript(string $location, string $script, array $dependencies = []): void {
		
		// $location tells us if this is a template (parent) or (child)
		// theme script.  that allows us to narrow down the list of scripts
		// that we use to check for $script.  if we can't find $script, we
		// throw a tantrum.
		
		if (!isset($this->scripts[$location][$script])) {
			throw new ThemeException("Unknown script: $script.",
				ThemeException::UNKNOWN_SCRIPT);
		}
		
		$handle = sprintf("%s-%s", $location, $script);
		$file = $this->scripts[$location][$script];
		$version = filemtime($file["realPath"]);
		
		wp_enqueue_script($handle, $file["webPath"], $dependencies, $version, true);
	}
	
	/**
	 * @param array $styles
	 *
	 * @throws ThemeException
	 */
	public function addStyles(array $styles): void {
		
		// this one is basically the same as addScripts, but it uses the
		// list of CSS files that we discovered above to load things.
		
		foreach ($styles as $style) {
			list($location, $style, $dependencies) = $style;
			$this->addStyle($location, $style, $dependencies);
		}
	}
	
	/**
	 * @param string $location
	 * @param string $style
	 * @param array  $dependencies
	 *
	 * @return void
	 * @throws ThemeException
	 */
	public function addStyle(string $location, string $style, array $dependencies = []): void {
		
		if (!isset($this->scripts[$location][$style])) {
			throw new ThemeException("Unknown style: $style.",
				ThemeException::UNKNOWN_STYLE);
		}
		
		$handle = sprintf("%s-%s", $location, $style);
		$file = $this->styles[$location][$style];
		$version = filemtime($file["realPath"]);
		
		wp_enqueue_style($handle, $file["webPath"], $dependencies, $version);
	}
	
	/**
	 * @param array $sidebars
	 *
	 * @return void
	 */
	public function addSidebars(array $sidebars): void {
		$sidebarMarkup = $this->getSidebarMarkup();
		
		foreach ($sidebars as $sidebar => $description) {
			$sidebarId = strtolower(preg_replace("/\W+/", "-", $sidebar));
			
			// we assume that those that use this boilerplate will return
			// the markup that we want around the sidebar header and the
			// sidebar itself.  by merging our name, id, and description
			// into that array, we create the full argument for
			
			register_sidebar(array_merge($sidebarMarkup, [
				"id"          => $sidebarId,
				"name"        => $sidebar,
				"description" => $description,
			]));
		}
	}
	
	/**
	 * @return array
	 */
	protected function getSidebarMarkup(): array {
		
		// this is just a default; we leave it to the user of the
		// boilerplate to override this information if they want to
		// use something else.
		
		return [
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'before_title'  => '<header><h2 class="widget-title">',
			'after_title'   => '</h2></header>',
			'after_widget'  => '</aside>',
		];
	}
}
