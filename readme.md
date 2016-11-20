# Whiteboard Loader

Whiteboard Loader is a PHP class that makes it easy to load JavaScript and CSS files on any page on a WordPress powered website.

This class allows you to load files on specific pages and post types, automatically load dependencies, set loading priorities and make any data available to your script or stylesheet that you can normally only get from the server side of WordPress.

It works on the WordPress admin dashboard pages and the front pages of the website as well. Files concatenation, compression and caching provide a robust performance and a short load time for your assets.

<br><br>

# How to Use
You can use Whiteboard Loader in your theme or plugin by importing the class file.
```php
require_once('loader/WB_Loader.php');
```
Before using any of its methods, the loader must be initialized. Use the static method [`init`](#init-loader_url-) to do this:
```php
WB_Loader::init();
```
**Note:** All methods will have no effect if called after the action [`admin_enqueue_scripts`](https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts) takes place.

<br><br>

# Properties
### caching_enabled
( *Boolean* )  

Specifies whether caching should be enabled. Default value is `true`.

---

### compression_enabled
( *Boolean* )  

Specifies whether compression should be enabled. Default value is `true`.

---

### force_gzip_compression
( *Boolean* )  

Forces GZIP compression. Default value is `false`.

<br><br>

# Methods
### init( $loader_url )
( *Returns: Loader instance* )  

Initializes the loader. This method must be called before using any of the other loader methods.  

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $loader_url | String | By default, the loader will try to guess its URL but you can manually set the base URL for the loader by passing it to the `init` method. |

---

### register_style( $stylesheet_name, $args )
( *Returns: Loader instance* )  

Registers a stylesheet file or group of stylesheet files to be loaded on a specific page.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $stylesheet_name | String | Stylesheet name. This should be unique for each stylesheet. |
| $args | Array | Arguments array. |

#### Arguments array  
* **src** - required  
  ( *String | Array* )  
  Path to the CSS file. If this stylesheet consists of a group of CSS files, use an array. It's recommended to use directories instead of URLs or you might run into caching issues.

* **page**   
  ( *String* )  
  Hook suffix of the page. If you need to load the style on more than one page, use an array.   
  To load the style file on all pages, use an empty string `''`. To load the style on a specific admin page, use the page file name (`edit.php`, `options-general.php` ... etc). To load the style on a specific frontend page, use the post id and set *admin* to `false`. Default value is an empty string.

* **post_type**   
  ( *String | Array | Boolean* )  
  Post type slug. Filter specified page(s) using post type. If you need to specify more than one post type, use an array. Default value is `false`.

* **admin**   
  ( *Boolean* )  
  Whether the stylesheet is to be loaded in the admin area. Default value is `true`.

* **deps**   
  ( *String | Array | Boolean* )  
  Array of names of any stylesheets that this stylesheet depends on; stylesheets that must be loaded before this stylesheet. Set to `false` or omit if there are no dependencies.

* **priority**   
  ( *Integer* )  
  Used to specify the order in which the stylesheets associated with a particular handle are loaded. Lower numbers correspond with earlier loading, and stylesheets with the same priority are loaded in the order in which they were added to the queue. Default value is `99`.

* **report**   
  ( *Boolean* )  
  If set to `true`, places a comment in the output if the stylesheet is not found. Default value is `true`.

---

### register_script( $script_name, $args )
( *Returns: Loader instance* )  

Registers a script file or group of script files to be loaded on a specific page.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $script_name | String | Script name. This should be unique for each script. |
| $args | Array | Arguments array. |

#### Arguments array  
*  **src** - required  
  ( *String | Array* )  
  Path to the JS file. If this script consists of a group of JS files, use an array. It's recommended to use directories instead of URLs or you might run into caching issues.

* **page**   
  ( *String* )  
  Hook suffix of the page. If you need to load the script on more than one page, use an array.   
  To load the script file on all pages, use an empty string `''`. To load the script on a specific admin page, use the page file name (`edit.php`, `options-general.php` ... etc). To load the script on a specific frontend page, use the post id and set *admin* to `false`. Default value is an empty string.

* **post_type**   
  ( *String | Array | Boolean* )  
  Post type slug. Filter specified page(s) using post type. If you need to specify more than one post type, use an array. Default value is `false`.

* **admin**   
  ( *Boolean* )  
  Whether the script is to be loaded in the admin area. Default value is `true`.

* **deps**   
  ( *String | Array | Boolean* )  
  Array of names of any scripts that this script depends on; scripts that must be loaded before this script. Set to `false` or omit if there are no dependencies. You can also use names of WordPress scripts such as `jquery` or `mediaelement`.

* **priority**   
  ( *Integer* )  
  Used to specify the order in which the scripts associated with a particular handle are loaded. Lower numbers correspond with earlier loading, and scripts with the same priority are loaded in the order in which they were added to the queue. Default value is `99`.

* **report**   
  ( *Boolean* )  
  If set to `true`, places a comment in the output if the script is not found. Default value is `true`.

---

### register_asset( $asset_type, $asset_name, $args )
( *Returns: Loader instance* )  

Registers an asset file or group of asset files to be loaded on a specific page.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $asset_type | String | Type of the asset file, `script` or `style`. |
| $asset_name | String | Asset name. This should be unique for each asset. |
| $args | Array | Arguments array. |

#### Arguments array  
* **src** - required  
  ( *String | Array* )  
  Path to the asset file. If this asset consists of a group of files, use an array. It's recommended to use directories instead of URLs or you might run into caching issues.

* **page**   
  ( *String* )  
  Hook suffix of the page. If you need to load the asset on more than one page, use an array.   
  To load the asset file on all pages, use an empty string `''`. To load the asset on a specific admin page, use the page file name (`edit.php`, `options-general.php` ... etc). To load the asset on a specific frontend page, use the post id and set *admin* to `false`. Default value is an empty string.

* **post_type**   
  ( *String | Array | Boolean* )  
  Post type slug. Filter specified page(s) using post type. If you need to specify more than one post type, use an array. Default value is `false`.

* **admin**   
  ( *Boolean* )  
  Whether the asset is to be loaded in the admin area. Default value is `true`.

* **deps**   
  ( *String | Array | Boolean* )  
  Array of names of any other assets that this asset depends on. Set to `false` or omit if there are no dependencies.

* **priority**   
  ( *Integer* )  
  Used to specify the order in which the assets associated with a particular handle are loaded. Lower numbers correspond with earlier loading, and assets with the same priority are loaded in the order in which they were added to the queue. Default value is `99`.

* **report**   
  ( *Boolean* )  
  If set to `true`, places a comment in the output if the asset is not found. Default value is `true`.

---

### load_style( $stylesheet_name, $args = array() )
( *Returns: Loader instance* )  

Loads registered stylesheet file(s) on a specific page.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $stylesheet_name | String | Stylesheet name. This is the unique name that was assigned to the stylesheet using `register_style` function. |
| $args | Array | Arguments array, accepts the same keys as `register_style`. |

---

### load_script( $script_name, $args = array() )
( *Returns: Loader instance* )  

Loads registered script file(s) on a specific page.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $script_name | String | Script name. This is the unique name that was assigned to the script using `register_script` function. |
| $args | Array | Arguments array, accepts the same keys as `register_script`. |

---

### load_asset( $asset_type, $asset_name, $args = array() )
( *Returns: Loader instance* )  

Loads registered asset file(s) of the same type on a specific page.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $asset_type | String | Type of the asset file, `script` or `style`. |
| $asset_name | String | Asset name. This is the unique name that was assigned to the asset using `register_asset` function. |
| $args | Array | Arguments array, accepts the same keys as `register_asset`. |

---

### is_registered_style( $stylesheet_name )
( *Returns: Boolean* )  

Checks whether a stylesheet is registered.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $stylesheet_name | String | This is the unique name that was assigned to the stylesheet using `register_style` function. |

---

### is_registered_script( $script_name )
( *Returns: Boolean* )  

Checks whether a script is registered.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $script_name | String | This is the unique name that was assigned to the script using `register_script` function. |

---

### is_registered_asset( $asset_type, $asset_name )
( *Returns: Boolean* )  

Checks whether an asset is registered.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $asset_type | String | Type of the asset file, `script` or `style`. |
| $asset_name | String | Asset name. This is the unique name that was assigned to the asset using `register_asset` function. |

---

### get_style_args( $stylesheet_name )
( *Returns: Array* )  

Retrieves the arguments array that is assigned to a specific stylesheet. Returns arguments array for the stylesheet. If the stylesheet is not registered, it will return false.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $stylesheet_name | String | This is the unique name that was assigned to the stylesheet using `register_style` function. |

---

### get_script_args( $script_name )
( *Returns: Array* )  

Retrieves the arguments array that is assigned to a specific script. Returns arguments array for the script. If the script is not registered, it will return false.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $script_name | String | This is the unique name that was assigned to the script using `register_script` function. |

---

### get_asset_args( $asset_type, $asset_name )
( *Returns: Array* )  

Retrieves the arguments array that is assigned to a specific asset. Returns arguments array for the asset. If the asset is not registered, it will return false.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $asset_type | String | Type of the asset file, `script` or `style`. |
| $asset_name | String | Asset name. This is the unique name that was assigned to the asset using `register_asset` function. |

---

### localize( $name, $data )
( *Returns: Loader instance* )  

Localizes a registered script with data for a JavaScript variable.

This lets you offer properly localized translations of any strings used in your script. This is necessary because WordPress currently only offers a localization API in PHP, not directly in JavaScript.

Though localization is the primary use, it can be used to make any data available to your script or stylesheet that you can normally only get from the server side of WordPress. Stylesheets should use placeholders in mustache format.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $name | String | The name of the variable which will contain the data.   Note that this should be unique to both the script and to the plugin or theme. Thus, the value here should be properly prefixed with the slug or another unique value, to prevent conflicts.   However, as this is a JavaScript object name, it cannot contain dashes. Use underscores or camelCasing. |
| $data | Array | The data itself. The data can be either a single- or multi- (as of WP 3.3) dimensional array. Like json_encode(), the data will be a JavaScript object if the array is an associate array (a map), otherwise the array will be a JavaScript array. |

---

### get_url()
( *Returns: String* )  

Retrieves loader URL.

---

### get_relative_dir( $dir )
( *Returns: String* )  

Returns a path relative to the loader file.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $dir | String | Path to the directory which you want to get a relative path to. |

---

### set_assets_dir( $dir )
( *Returns: Loader instance* )  

Sets assets directory.

| Parameter | Type | Description |
| :----- | :----- | :----- |
| $dir | String | Assets directory. |

<br><br>

# Usage Examples

We will start by initializing the loader. We will let the loader guess its own URL by not passing anything to the `init` function.

```php
require('loader/WB_Loader.php');
WB_Loader::init();
```

We will be using demo script files placed in the `demo` directory, to make things easier we will let the loader know about this.

```php
// This allows using paths relative to the plugin, not to the loader class
WB_Loader::set_assets_dir(dirname(__FILE__) . '/demos');
```

Last thing, we will enable caching and compression.

```php
WB_Loader::$force_gzip_compression = true;
WB_Loader::$caching_enabled = true;
WB_Loader::$compression_enabled = true;
```

### Load a script on all pages
```php
// loads script-1 on all pages
WB_Loader::load_script('my_script_1', array(
  'src' => 'script-1.js',
));

// loads script-2 on all pages, using a different API
WB_Loader::load_script('my_script_2', 'script-2.js');

```

### Load a script with a high priority
```php
// loads script-3 on all pages, before all other scripts
// higher priorities < 0 < lower priorities
WB_Loader::load_script('my_script_3', array(
  'priority' => 0,
  'src' => 'script-3.js',
));
```

### Load a script on the plugins page
```php
// loads script-4 on plugins.php page only
WB_Loader::load_script('my_script_4', array(
  'page' => 'plugins.php',
  'src' => 'script-4.js',
));
```

### Load a script with dependencies
```php
// loads script-5 on all pages with dependency on script-5-dep
WB_Loader::register_script('my_script_5_dep', 'script-5-dep.js');

WB_Loader::load_script('my_script_5', array(
  'src' => 'script-5.js',
  'deps' => array(
    'my_script_5_dep',
  )
));

// loads script-6 on all pages with dependency on 'imagesloaded' and 'wp_media'
WB_Loader::load_script('my_script_6', array(
  'src' => 'script-6.js',
  'deps' => array(
    'imagesloaded',
    'wp_media',
  )
));

// loads script-7 on all pages with dependency on script-7-dep-a and script-7-dep-b
WB_Loader::register_script('my_script_7_dep_a', 'script-7-dep-a.js');
WB_Loader::register_script('my_script_7_dep_b', array(
  'src' => 'script-7-dep-b.js',
));

WB_Loader::load_script('my_script_7', array(
  'src' => 'script-7.js',
  'deps' => array(
    'my_script_7_dep_a',
    'my_script_7_dep_b',
  ),
));

// loads script-8 on all pages with dep script-8-b which has a dep script-8-a
WB_Loader::register_script('my_script_8_a', array(
  'src' => 'script-8-dep-a.js',
));

WB_Loader::register_script('my_script_8_b', array(
  'src' => 'script-8-dep-b.js',
  'deps' => array(
    'my_script_8_a',
  ),
));

WB_Loader::load_script('my_script_8', array(
  'src' => 'script-8.js',
  'deps' => array(
    'my_script_8_b',
  ),
));
```

### Load a script on the front pages
```php
// loads script on all frontend pages
WB_Loader::load_script('my_script_15', array(
  'src' => 'script-15.js',
  'admin' => false
));
```

### Load a script on a specific post type
```php
// loads script-9 on post-new.php pages for post type 'post' only
WB_Loader::load_script('my_script_9', array(
  'src' => 'script-9.js',
  'page' => 'post-new.php',
  'post_type' => 'post',
));

// loads script-12 only on single pages, author pages or category pages
WB_Loader::load_script('my_script_12', array(
  'src' => 'script-12.js',
  'page' => 'single',
  'admin' => false
));

WB_Loader::load_script('my_script_13', array(
  'src' => 'script-13.js',
  'page' => 'author',
  'admin' => false
));

WB_Loader::load_script('my_script_14', array(
  'src' => 'script-14.js',
  'page' => 'category',
  'admin' => false
));
```

### Load a script on a specific page
```php
// loads script-10 on page with id 2
WB_Loader::load_script('my_script_10', array(
  'src' => 'script-10.js',
  'page' => 2,
));

// loads script-11 on post with id 2 only on the front end
WB_Loader::load_script('my_script_11', array(
  'src' => 'script-11.js',
  'page' => 1,
  'post_type' => 'post',
  'admin' => false
));
```

### Passing data to a script
```php
// creates a JavaScript object on the global scope
WB_Loader::localize('my_data', array(
  'admin' => false,
  'settings' => array(
    'wburl' => WB_Loader::get_url()
  )
));

WB_Loader::load_script('my_script_16', array(
  'src' => 'script-16.js',
));
```

This will create a JavaScript object on the global scope which is available to all scripts.

```javascript
console.log('wb_url value is ' + my_data.settings.wb_url);

```

### Passing data to a stylesheet
```php
// also creates a JavaScript object on the global scope
WB_Loader::localize('myThemeData', array(
  'header' => array(
    'textColor' => '#5bc',
    'background' => 'white',
  )
));

WB_Loader::load_style('my_theme_style', array(
  'src' => 'theme.css',
));
```

This will create a JavaScript object on the global scope which is available to all scripts. This object is also available to all stylesheets that were loaded using Whiteboard.

```css
#header {
  color: [[myThemeData.header.textColor]];
  background: [[myThemeData.header.background]];
}
```
