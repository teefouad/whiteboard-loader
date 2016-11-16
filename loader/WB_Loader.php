<?php

/**
 * Whiteboard Loader is a PHP class that makes it easy to load JavaScript and CSS
 * files on any page on a WordPress powered website. It works on the WordPress
 * admin dashboard pages and the front pages of the website as well.
 *
 * @author  Mostafa Fouad
 * @link    https://github.com/teefouad/whiteboard-loader
 * @version 1.0.1
 */

/**
 * Note: Methods prefixed with 'wb_' are for internal use only.
 */

session_start();

require_once('functions.php');

if (!class_exists('WB_Loader')) {

  class WB_Loader {

    public static
      /**
       * Specifies whether caching should be enabled. Default value is 'true'.
       * @var boolean
       */
      $caching_enabled = true,

      /**
       * Specifies whether compression should be enabled. Default value is 'true'.
       * @var boolean
       */
      $compression_enabled = true,

      /**
       * Forces GZIP compression. Default value is 'false'.
       * @var boolean
       */
      $force_gzip_compression = false;

    protected static
      $__instance               = NULL,
      $__loader_url             = NULL,
      $__loader_ver             = '1.0.0',
      $__styles_slug            = 'wb_styles',
      $__scripts_slug           = 'wb_scripts',
      $__registered_styles      = array(),
      $__registered_scripts     = array(),
      $__loaded_styles          = array(),
      $__loaded_scripts         = array(),
      $__enqueued_styles        = array(),
      $__enqueued_scripts       = array(),
      $__enqueued_styles_deps   = array(),
      $__enqueued_scripts_deps  = array(),
      $__localization_data      = array(),
      $__assets_directory       = '.';

    /**
     * Prevent direct object creation.
     */
    final private function __construct() {}

    /**
     * Prevent object cloning.
     */
    final private function __clone() {}

    /**
     * Returns new or existing instance.
     * @return Instance
     */
    final public static function getInstance() {
      if (static::$__instance !== NULL) {
        return static::$__instance;
      }

      static::$__instance = new static();

      return static::$__instance;
    }

    /* ======================================================================= */
    /* PUBLIC API
    /* ======================================================================= */

    /**
     * Initializes the loader. This method must be called before using any of the other loader methods.
     * @param   boolean   $loader_url   Loader URL. By default, the loader will try to guess its URL but you can manually set the base URL for the loader by passing it to the 'init' method.
     * @return  Instance
     */
    public static function init($loader_url = false) {
      if ($loader_url !== false) {
        self::$__loader_url = $loader_url;
      } else {
        self::$__loader_url = self::wb_get_url();
      }

      add_action('wp_enqueue_scripts', 'WB_Loader::wb_enqueue_assets', 99);
      add_action('admin_enqueue_scripts', 'WB_Loader::wb_enqueue_assets', 99);

      return self::getInstance();
    }

    /**
     * Registers a stylesheet file or group of stylesheet files to be loaded on a specific page.
     * @param  string   $stylesheet_name    Stylesheet name. This should be unique for each stylesheet.
     * @param  array    $args               Arguments array.
     *                                      - src (string | array) [required]
     *                                          Path to the CSS file. If this stylesheet consists of a group of CSS files, use an array. It's recommended to use directories instead of URLs or you might run into caching issues.
     *                                      - page (string) [optional]
     *                                          Hook suffix of the page. If you need to load the style on more than one page, use an array. To load the style file on all pages, use an empty string ''. To load the style on a specific admin page, use the page file name (edit.php, options-general.php ... etc). To load the style on a specific frontend page, use the post id and set 'admin' to 'false'. Default value is an empty string.
     *                                      - post_type (string | array | boolean) [optional]
     *                                          Post type slug. Filter specified page(s) using post type. If you need to specify more than one post type, use an array. Default value is 'false'.
     *                                      - admin (boolean) [optional]
     *                                          Whether the stylesheet is to be loaded in the admin area. Default value is 'true'.
     *                                      - deps (string | array | boolean) [optional]
     *                                          Array of names of any stylesheets that this stylesheet depends on; stylesheets that must be loaded before this stylesheet. Set to false or omit if there are no dependencies.
     *                                      - priority (integer) [optional]
     *                                          Used to specify the order in which the stylesheets associated with a particular handle are loaded. Lower numbers correspond with earlier loading, and stylesheets with the same priority are loaded in the order in which they were added to the queue. Default value is 99.
     *                                      - report (boolean) [optional]
     *                                          If set to 'true', places a comment in the output if the stylesheet is not found. Default value is 'true'.
     * @return Instance
     */
    public static function register_style($stylesheet_name, $args) {
      return self::register_asset('style', $stylesheet_name, $args);
    }

    /**
     * Registers a script file or group of script files to be loaded on a specific page.
     * @param  string   $script_name        Script name. This should be unique for each script.
     * @param  array    $args               Arguments array.
     *                                      - src (string | array) [required]
     *                                          Path to the JS file. If this script consists of a group of JS files, use an array. It's recommended to use directories instead of URLs or you might run into caching issues.
     *                                      - page (string) [optional]
     *                                          Hook suffix of the page. If you need to load the script on more than one page, use an array. To load the script file on all pages, use an empty string ''. To load the script on a specific admin page, use the page file name (edit.php, options-general.php ... etc). To load the script on a specific frontend page, use the post id and set 'admin' to 'false'. Default value is an empty string.
     *                                      - post_type (string | array | boolean) [optional]
     *                                          Post type slug. Filter specified page(s) using post type. If you need to specify more than one post type, use an array. Default value is 'false'.
     *                                      - admin (boolean) [optional]
     *                                          Whether the script is to be loaded in the admin area. Default value is 'true'.
     *                                      - deps (string | array | boolean) [optional]
     *                                          Array of names of any scripts that this script depends on; scripts that must be loaded before this script. Set to false or omit if there are no dependencies. You can also use names of WordPress scripts such as 'jquery' or 'mediaelement'.
     *                                      - priority (integer) [optional]
     *                                          Used to specify the order in which the scripts associated with a particular handle are loaded. Lower numbers correspond with earlier loading, and scripts with the same priority are loaded in the order in which they were added to the queue. Default value is 99.
     *                                      - report (boolean) [optional]
     *                                          If set to 'true', places a comment in the output if the script is not found. Default value is 'true'.
     * @return Instance
     */
    public static function register_script($script_name, $args) {
      return self::register_asset('script', $script_name, $args);
    }

    /**
     * Registers an asset file or group of asset files to be loaded on a specific page.
     * @param   string  $asset_type         Type of the asset file, 'script' or 'style'.
     * @param   string  $asset_name         Asset name. This should be unique for each asset.
     * @param   array   $args               Arguments array.
     *                                      - src (string | array) [required]
     *                                          Path to the asset file. If this asset consists of a group of files, use an array. It's recommended to use directories instead of URLs or you might run into caching issues.
     *                                      - page (string) [optional]
     *                                          Hook suffix of the page. If you need to load the asset on more than one page, use an array. To load the asset file on all pages, use an empty string ''. To load the asset on a specific admin page, use the page file name (edit.php, options-general.php ... etc). To load the asset on a specific frontend page, use the post id and set 'admin' to 'false'. Default value is an empty string.
     *                                      - post_type (string | array | boolean) [optional]
     *                                          Post type slug. Filter specified page(s) using post type. If you need to specify more than one post type, use an array. Default value is 'false'.
     *                                      - admin (boolean) [optional]
     *                                          Whether the asset is to be loaded in the admin area. Default value is 'true'.
     *                                      - deps (string | array | boolean) [optional]
     *                                          Array of names of any other assets that this asset depends on. Set to false or omit if there are no dependencies.
     *                                      - priority (integer) [optional]
     *                                          Used to specify the order in which the assets associated with a particular handle are loaded. Lower numbers correspond with earlier loading, and assets with the same priority are loaded in the order in which they were added to the queue. Default value is 99.
     *                                      - report (boolean) [optional]
     *                                          If set to 'true', places a comment in the output if the asset is not found. Default value is 'true'.
     * @return Instance
     */
    public static function register_asset($asset_type, $asset_name, $args) {
      if (is_string($args)) {
        $args = array('src' => $args);
      }

      $args = wp_parse_args($args, array(
        'src'       => '',
        'page'      => '',
        'post_type' => false,
        'admin'     => true,
        'deps'      => array(),
        'priority'  => 99,
        'report'    => true,
      ));

      $args['src'] = self::wb_normalize_slashes(
        self::wb_standardize_slashes(
          self::$__assets_directory . '/' . $args['src']
        )
      );

      self::${'__registered_' . $asset_type . 's'}[$asset_name] = $args;

      return self::getInstance();
    }

    /**
     * Loads a registered stylesheet on a specific page. For this function to work correctly, it must be called before the 'admin_enqueue_scripts' action takes place.
     * @param  string   $stylesheet_name    Stylesheet name. This is the unique name that was assigned to the stylesheet using 'register_style' function.
     * @param  array    $args               Arguments array.
     * @return Instance
     */
    public static function load_style($stylesheet_name, $args = array()) {
      return self::load_asset('style', $stylesheet_name, $args);
    }

    /**
     * Loads a registered script on a specific page. For this function to work correctly, it must be called before the 'admin_enqueue_scripts' action takes place.
     * @param  string   $script_name        Script name. This is the unique name that was assigned to the script using 'register_script' function.
     * @param  array    $args               Arguments array.
     *
     * @return Instance
     */
    public static function load_script($script_name, $args = array()) {
      return self::load_asset('script', $script_name, $args);
    }

    /**
     * Loads a registered asset file on a specific page. For this function to work correctly, it must be called before the 'admin_enqueue_scripts' action takes place.
     * @param   string  $asset_type         Type of the asset file, 'script' or 'style'.
     * @param   string  $asset_name         Asset name. This is the unique name that was assigned to the asset using 'register_asset' function.
     * @param   array   $args               Arguments array.
     * @return Instance
     */
    public static function load_asset($asset_type, $asset_name, $args = array()) {
      if (!self::is_registered_asset($asset_type, $asset_name)) {
        self::register_asset($asset_type, $asset_name, $args);
      }

      self::${'__loaded_' . $asset_type . 's'}[$asset_name] = self::${'__registered_' . $asset_type . 's'}[$asset_name];

      return self::getInstance();
    }

    /**
     * Checks whether a stylesheet is registered.
     * @param   string    $stylesheet_name    This is the unique name that was assigned to the stylesheet using 'register_style' function.
     * @return  boolean                       True if the stylesheet is registered, false if it is not.
     */
    public static function is_registered_style($stylesheet_name) {
      return self::is_registered_asset('style', $stylesheet_name);
    }

    /**
     * Checks whether a script is registered.
     * @param   string    $script_name    This is the unique name that was assigned to the script using 'register_script' function.
     * @return  boolean                   True if the script is registered, false if it is not.
     */
    public static function is_registered_script($script_name) {
      return self::is_registered_asset('script', $script_name);
    }

    /**
     * Checks whether an asset is registered.
     * @param   string    $asset_type   Type of the asset file, 'script' or 'style'.
     * @param   string    $asset_name   This is the unique name that was assigned to the asset using 'register_asset' function.
     * @return  boolean                 True if the asset is registered, false if it is not.
     */
    public static function is_registered_asset($asset_type, $asset_name) {
      return isset(self::${'__registered_' . $asset_type . 's'}[$asset_name]);
    }

    /**
     * Retrieves the arguments array that is assigned to a specific stylesheet.
     * @param   string    $stylesheet_name    This is the unique name that was assigned to the stylesheet using 'register_style' function.
     * @return  array                         Arguments array for the stylesheet. If the stylesheet is not registered, it will return false.
     */
    public static function get_style_args($asset_name) {
      return self::get_asset_args('style', $asset_name);
    }

    /**
     * Retrieves the arguments array that is assigned to a specific script.
     * @param   string    $script_name    This is the unique name that was assigned to the script using 'register_script' function.
     * @return  array                     Arguments array for the script. If the script is not registered, it will return false.
     */
    public static function get_script_args($asset_name) {
      return self::get_asset_args('script', $asset_name);
    }

    /**
     * Retrieves the arguments array that is assigned to a specific asset.
     * @param   string    $asset_type   Type of the asset file, 'script' or 'style'.
     * @param   string    $asset_name   This is the unique name that was assigned to the asset using 'register_asset' function.
     * @return  array                   Arguments array for the asset. If the asset is not registered, it will return false.
     */
    public static function get_asset_args($asset_type, $asset_name) {
      if (!self::is_registered_asset($asset_type, $asset_name)) {
        return false;
      }

      return self::${'__registered_' . $asset_type . 's'}[$asset_name];
    }

    /**
     * Localizes a registered script with data for a JavaScript variable. This lets you offer properly localized translations of any strings used in your script. This is necessary because WordPress currently only offers a localization API in PHP, not directly in JavaScript. Though localization is the primary use, it can be used to make any data available to your script or stylesheet that you can normally only get from the server side of WordPress. Stylesheets should use placeholders in mustache format.
     * @param  string   $name   The name of the variable which will contain the data. Note that this should be unique to both the script and to the plugin or theme. Thus, the value here should be properly prefixed with the slug or another unique value, to prevent conflicts. However, as this is a JavaScript object name, it cannot contain dashes. Use underscores or camelCasing.
     * @param  array    $data   The data itself. The data can be either a single- or multi- (as of WP 3.3) dimensional array. Like json_encode(), the data will be a JavaScript object if the array is an associate array (a map), otherwise the array will be a JavaScript array.
     * @return Instance
     */
    public static function localize($name, $data) {
      self::$__localization_data[] = array(
        'name' => $name,
        'data' => $data,
      );

      return self::getInstance();
    }

    /**
     * Retrieves loader URL.
     * @return string Loader URL.
     */
    public static function get_url() {
      return self::$__loader_url;
    }

    /**
     * Returns a path relative to the loader file.
     * @param   string   $dir  Path to the directory which you want to get a relative path to.
     * @return  string         The relative directory path.
     */
    public static function get_relative_dir($dir) {
      return wb_get_relative_path(dirname(__FILE__), $dir);
    }

    /**
     * Sets assets directory.
     * @param string $dir Assets directory.
     */
    public static function set_assets_dir($dir) {
      self::$__assets_directory = self::get_relative_dir($dir);

      return self::getInstance();
    }

    /* ======================================================================= */
    /* PRIVATE
    /* ======================================================================= */

    /**
     * Enqueues loaded assets. This is basically where all the magic happens.
     */
    public static function wb_enqueue_assets() {
      $styles_hash = '';
      $scripts_hash = '';

      foreach (array('style', 'script') as $asset_type) {
        /* sort by priority */
        self::wb_sort_assets($asset_type);

        /* enqueue deps */
        foreach (self::${'__loaded_' . $asset_type . 's'} as $asset_name => $asset_args) {
          if (self::wb_should_enqueue_asset($asset_type, $asset_args)) {
            self::wb_enqueue_asset_deps($asset_type, $asset_args);
            self::${'__enqueued_' . $asset_type . 's'}[$asset_name] = $asset_args;
          }
        }

        ${$asset_type . 's_hash'} = array_reduce(self::${'__enqueued_' . $asset_type . 's'}, function($hash, $next) {
          if (is_array($next['src'])) {
            foreach ($next['src'] as $src) {
              $hash .= $src;
            }
          } else {
            $hash .= $next['src'];
          }

          return $hash;
        }, ${$asset_type . 's_hash'});
      }

      if (count(self::$__enqueued_styles) > 0) {
        wp_enqueue_style(
          self::$__styles_slug,
          self::$__loader_url . '/styles.php?' . md5($styles_hash),
          self::$__enqueued_styles_deps,
          self::$__loader_ver,
          'all'
        );
      }

      if (count(self::$__enqueued_scripts) > 0) {
        wp_enqueue_script(
          self::$__scripts_slug,
          self::$__loader_url . '/scripts.php?' . md5($scripts_hash),
          self::$__enqueued_scripts_deps,
          self::$__loader_ver,
          true
        );
      }

      $_SESSION['wb_loader_data'] = array(
        'localization_data'       => self::$__localization_data,
        'enqueued_styles'         => self::$__enqueued_styles,
        'enqueued_scripts'        => self::$__enqueued_scripts,
        'caching_enabled'         => self::$caching_enabled,
        'compression_enabled'     => self::$compression_enabled,
        'force_gzip_compression'  => self::$force_gzip_compression
      );

      self::wb_localize();
    }

    /**
     * Sorts loaded assets based on priority.
     * @param   string    $asset_type   Type of the loaded assets, 'script' or 'style'.
     */
    public static function wb_sort_assets($asset_type) {
      usort(self::${'__loaded_' . $asset_type . 's'}, function($a, $b) {
        if ($a['priority'] === $b['priority']) {
          return 0;
        }

        return $a['priority'] < $b['priority'] ? -1 : 1;
      });
    }

    /**
     * Enqueues dependencies of each loaded asset.
     * @param   string    $asset_type   Type of the asset file, 'script' or 'style'.
     * @param   array     $asset_args   Asset arguments array.
     */
    public static function wb_enqueue_asset_deps($asset_type, $asset_args) {
      $registered_assets = self::${'__registered_' . $asset_type . 's'};

      if (!empty($asset_args['deps'])) {
        if (!is_array($asset_args['deps'])) {
          $asset_args['deps'] = array($asset_args['deps']);
        }

        foreach ($asset_args['deps'] as $dep) {
          if (isset($registered_assets[$dep]) && $registered_assets[$dep]['src']) {
            if (!isset(self::${'__enqueued_' . $asset_type . 's'}[$dep])) {
              if (!empty($registered_assets[$dep]['deps'])) {
                self::wb_enqueue_asset_deps($asset_type, $registered_assets[$dep]);
              }

              self::${'__enqueued_' . $asset_type . 's'}[$dep] = $registered_assets[$dep];
            }
          } else
          if ($dep === 'wp_media') {
            wp_enqueue_media();
          } else {
            self::${'__enqueued_' . $asset_type . 's_deps'}[] = $dep;
          }
        }
      }
    }

    /**
     * Checks whether a specific asset should be enqueued.
     * @param   string    $asset_type   Type of the asset file, 'script' or 'style'.
     * @param   array     $asset_args   Asset arguments array.
     */
    public static function wb_should_enqueue_asset($asset_type, $asset_args) {
      global $hook_suffix, $post;

      $allowed_page = $asset_args['page'];
      $allowed_post_type = $asset_args['post_type'];

      if ($allowed_post_type && !is_array($allowed_post_type)) {
        $allowed_post_type = array($allowed_post_type);
      }

      if (is_admin() && $asset_args['admin'] === true) {
        return (
          empty($allowed_page) ||
          $allowed_page == $post->ID ||
          (is_string($allowed_page) && $allowed_page === $hook_suffix) ||
          (is_object($allowed_page) && $allowed_page->suffix === $hook_suffix)
        ) && (
          $allowed_post_type === false ||
          in_array($post->post_type, $allowed_post_type)
        );
      } else
      if (!is_admin() && $asset_args['admin'] === false) {
        return (
          empty($allowed_page) ||
          $allowed_page == $post->ID ||
          in_array($allowed_page, array(
            '404', 'admin', 'archive', 'attachment', 'author', 'category', 'comments_popup', 'customize_preview', 'date', 'day', 'feed', 'front_page', 'home', 'month', 'page', 'page_template', 'paged', 'preview', 'search', 'single', 'singular', 'sticky', 'tag', 'tax', 'time', 'trackback', 'year'
          )) && call_user_func('is_' . $allowed_page)
        ) && (
          $allowed_post_type === false ||
          in_array($post->post_type, $allowed_post_type)
        );
      }

      return false;
    }

    /**
     * Localizes assets with data.
     */
    public static function wb_localize() {
      foreach (self::$__localization_data as $ld) {
        wp_localize_script(self::$__scripts_slug, $ld['name'], $ld['data']);
      }
    }

    /**
     * Generates and returns loader URL.
     * @return string Loader URL.
     */
    public static function wb_get_url() {
      $wb_dirname         = untrailingslashit(dirname(__FILE__));
      $wb_std_dirname     = self::wb_standardize_slashes($wb_dirname);
      $wb_std_dirname     = self::wb_normalize_slashes($wb_std_dirname);
      $wp_content_dir     = self::wb_standardize_slashes(WP_CONTENT_DIR);
      $wb_relative_url    = str_replace($wp_content_dir, '', $wb_std_dirname);
      $wb_loader_url      = content_url() . $wb_relative_url;

      return $wb_loader_url;
    }

    /**
     * Standardizes slashes in a given path.
     * @param  string $str Path to process.
     * @return string      Standardized path.
     */
    public static function wb_standardize_slashes($str) {
      return str_replace('\\', '/', $str);
    }

    /**
     * Removes double slashes from a given path.
     * @param  string $str Path to process.
     * @return string      Cleaned path.
     */
    public static function wb_normalize_slashes($str) {
      return preg_replace('|/+|', '/', $str);
    }

  }

}
