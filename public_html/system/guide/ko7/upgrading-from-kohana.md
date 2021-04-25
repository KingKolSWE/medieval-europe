# Upgrading from Kohana

If you were using 3.3.x version, there are 7 breaking changes that may affect you, be aware.

- **Kohana_Kohana_Exception**, all functions that received parameter Exception $e have been replaced to just $e. If you are extending the class verify you have the same.
- **Kohana_URL**, now function site has a new parameter `$subdomain = NULL`, if you are extending the class and this function add it.
- **Module encrypt**, now encryption works as a module, if you are using new Encrypt or similar you need to enable the module in your bootstrap ex: `'encrypt'       => MODPATH.'encrypt',` 
- **MySQL driver** has been removed. If you are still using it, please install MySQLi driver and then edit your `config/database.php` and then set as `'type'       => 'MySQLi'`
- **APC driver** has been removed. If you are still using it, please install APCu driver and then edit your `config/cache.php` and configure apcu.
- **Memcache driver** has been removed. If you are still using it, please install Memcached driver and then edit/create your `config/cache.memcached.php` and configure memcached.
- **Class rename** All classes have bee renamed from `Kohana_*` to `KO7_*` since Version 4.0, but don't panic, all `Kohana_*` classes are still available. See [Backwards Module.](#kohana-backwards-compatibility-module)

## New modules included

- Encrypt (separated from system)
- Pagination
- Kohana (Backwards Compatibility Module)


## Kohana Backwards Compatibility Module
The Kohana Backwards Compatibility Module ensures all `Kohana_*` class names will still be available after upgrade. These class callings should be replaced with `KO7_*`. They are deprecated since version 4.0.0 and will be removed in further versions.

To ensure full compatibility, the module get's loaded before all other modules and classes are even available right after the autoloader function.

### Common mistakes using this module

- Make sure that inside `bootstrap.php` everywhere before `spl_autoload_register`, you don't use `Kohana_* Classes` as they won't be available.
Everywhere after that you are free to use them, as they will be loaded there.

- If you included files from `SYSPATH` directly via `require` or something, you have to Update them to use new Koseven class/folder name.

- Koseven searches for Kohana files inside `MODPATH . 'kohana'`. So don't rename the module folder and also check if it is inside your `MODPATH` (in case you have overwritten your `MODPATH`)
