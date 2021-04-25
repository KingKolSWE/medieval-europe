<?php
/**
 * File-based configuration reader. Multiple configuration directories can be
 * used by attaching multiple instances of this class to [KO7_Config].
 *
 * @package    KO7
 * @category   Configuration
 *
 * @copyright  (c) 2007-2016  Kohana Team
 * @copyright  (c) since 2020 Koseven Team
 * @license    https://koseven.dev/LICENSE
 */
abstract class KO7_Config_File_Reader implements KO7_Config_Reader {

	/**
	 * @var string The directory where config files are located
	 */
	protected $_directory = 'config';

	/**
	 * @var array Cached configurations
	 */
	protected static $_cache = [];

	/**
	 * Creates a new file reader using the given directory as a config source.
	 *
	 * @param string $directory Configuration directory to search
	 */
	public function __construct($directory = null)
	{
		if ($directory !== null)
		{
			$this->_directory = trim($directory, '\/');
		}
	}

	/**
	 * Load and merge all of the configuration files in this group.
	 *
	 * @param string $group Configuration group name
	 * @return array Configuration
	 * @throws KO7_Exception YAML extension/package not loaded
	 */
	public function load($group): array
	{
		// @codeCoverageIgnoreStart
		// Check cache
		$cache_key = $this->_directory.' '.$group;
		if (KO7::$caching && isset(static::$_cache[$cache_key]))
		{
			return static::$_cache[$cache_key];
		}
		// @codeCoverageIgnoreEnd

		if (KO7::$profiling)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Config ('.$this->_directory.')', __FUNCTION__);
		}

		$config = [];
		// Loop through paths. Notice: reverse paths, so system and modules files get overwritten by application files
		foreach (array_reverse(KO7::include_paths()) as $path)
		{
			// Build path
			$file = $path.$this->_directory.DIRECTORY_SEPARATOR.$group;
			$value = false;
			// Try ".php", ".json" and ".yaml" extensions and parse contents with PHP support
			if (file_exists($file.'.php'))
			{
				$value = KO7::load($file.'.php');
			}
			elseif (file_exists($file.'.json'))
			{
				$value = json_decode($this->read_from_ob($file.'.json'), true);
			}
			elseif (file_exists($file.'.yaml'))
			{
				// @codeCoverageIgnoreStart
				if ( ! function_exists('yaml_parse'))
				{
					throw new KO7_Exception('YAML extension/package is required in order to parse YAML files');
				}
				// @codeCoverageIgnoreEnd
				$value = yaml_parse($this->read_from_ob($file.'.yaml'));
			}
			// Merge configurations
			if (is_iterable($value))
			{
				$config = Arr::merge($config, $value);
			}
		}

		// @codeCoverageIgnoreStart
		if (KO7::$caching)
		{
			static::$_cache[$cache_key] = $config;
		}
		// @codeCoverageIgnoreEnd

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $config;
	}

	/**
	 * Read contents from file with output buffering. Used to support `<?php` `?>` tags and code inside configurations.
	 *
	 * @param string $path Path to File
	 * @return string
	 * @codeCoverageIgnore
	 */
	protected function read_from_ob(string $path): string
	{
		// Start output buffer
		ob_start();
		KO7::load($path);
		// Return contents of buffer
		return (string) ob_get_clean();
	}
}
