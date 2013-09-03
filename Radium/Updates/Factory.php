<?php
if ( !class_exists('Radium_Updates_Factory') ):

/**
 * A factory that builds instances of other classes from this library.
 *
 * When multiple versions of the same class have been loaded (e.g. Radium_Updates_Checker 1.2
 * and 1.3), this factory will always use the latest available version. Register class
 * versions by calling {@link Radium_Updates_Factory::addVersion()}.
 *
 * At the moment it can only build instances of the Radium_Updates_Checker class. Other classes
 * are intended mainly for internal use and refer directly to specific implementations. If you
 * want to instantiate one of them anyway, you can use {@link Radium_Updates_Factory::getLatestClassVersion()}
 * to get the class name and then create it with <code>new $class(...)</code>.
 */
class Radium_Updates_Factory {
	protected static $classVersions = array();
	protected static $sorted = false;

	/**
	 * Create a new instance of Radium_Updates_Checker.
	 *
	 * @see Radium_Updates_Checker::__construct()
	 *
	 * @param $metadataUrl
	 * @param $pluginFile
	 * @param string $slug
	 * @param int $checkPeriod
	 * @param string $optionName
	 * @return Radium_Updates_Checker
	 */
	public static function buildUpdateChecker($metadataUrl, $pluginFile, $slug = '', $checkPeriod = 12, $optionName = '') {
		$class = self::getLatestClassVersion('Radium_Updates_Checker');
		return new $class($metadataUrl, $pluginFile, $slug, $checkPeriod, $optionName);
	}

	/**
	 * Get the specific class name for the latest available version of a class.
	 *
	 * @param string $class
	 * @return string|null
	 */
	public static function getLatestClassVersion($class) {
		if ( !self::$sorted ) {
			self::sortVersions();
		}

		if ( isset(self::$classVersions[$class]) ) {
			return reset(self::$classVersions[$class]);
		} else {
			return null;
		}
	}

	/**
	 * Sort available class versions in descending order (i.e. newest first).
	 */
	protected static function sortVersions() {
		foreach ( self::$classVersions as $class => $versions ) {
			uksort($versions, array(__CLASS__, 'compareVersions'));
			self::$classVersions[$class] = $versions;
		}
		self::$sorted = true;
	}

	protected static function compareVersions($a, $b) {
		return -version_compare($a, $b);
	}

	/**
	 * Register a version of a class.
	 *
	 * @access private This method is only for internal use by the library.
	 *
	 * @param string $generalClass Class name without version numbers, e.g. 'Radium_Updates_Checker'.
	 * @param string $versionedClass Actual class name, e.g. 'Radium_Updates_Checker_1_2'.
	 * @param string $version Version number, e.g. '1.2'.
	 */
	public static function addVersion($generalClass, $versionedClass, $version) {
		if ( !isset(self::$classVersions[$generalClass]) ) {
			self::$classVersions[$generalClass] = array();
		}
		self::$classVersions[$generalClass][$version] = $versionedClass;
		self::$sorted = false;
	}
}

endif;
