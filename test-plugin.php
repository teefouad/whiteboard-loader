<?php

/*
Plugin Name: Whiteboard Loader Tester
Version: 1.0.0
Author: Mostafa Fouad
*/

require('loader/WB_Loader.php');

// should detect plugin URL automatically
WB_Loader::init();
WB_Loader::set_assets_dir(dirname(__FILE__) . '/demos');
WB_Loader::$force_gzip_compression = true;
WB_Loader::$caching_enabled = true;
WB_Loader::$compression_enabled = true;

// should load script-1 on all pages
WB_Loader::load_script('my_script_1', array(
  'src' => 'script-1.js',
));

// should load script-2 on all pages, using a different API
WB_Loader::load_script('my_script_2', array(
  'src' => 'script-2.js',
));

// should load script-3 on all pages, before all other scripts
WB_Loader::load_script('my_script_3', array(
  'priority' => 0,
  'src' => 'script-3.js',
));

// should load script-4 on plugins.php page only
WB_Loader::load_script('my_script_4', array(
  'page' => 'plugins.php',
  'src' => 'script-4.js',
));

// should load script-5 on all pages with dependency on script-5-dep
WB_Loader::register_script('my_script_5_dep', 'script-5-dep.js');

WB_Loader::load_script('my_script_5', array(
  'src' => 'script-5.js',
  'deps' => array(
    'my_script_5_dep',
  )
));

// should load script-6 on all pages with dependency on imagesloaded and wp_media
WB_Loader::load_script('my_script_6', array(
  'src' => 'script-6.js',
  'deps' => array(
    'imagesloaded',
    'wp_media',
  )
));

// should load script-7 on all pages with dependency on script-7-dep-a and script-7-dep-b
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

// should load script-8 on all pages with dep script-8-b which has a dep script-8-a
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

// should load script-9 on post-new.php pages for post type 'post' only
WB_Loader::load_script('my_script_9', array(
  'src' => 'script-9.js',
  'page' => 'post-new.php',
  'post_type' => 'post',
));

// should load script-10 on page with id 2
WB_Loader::load_script('my_script_10', array(
  'src' => 'script-10.js',
  'page' => 2,
));

// should load script-11 on post with id 2 only on the front end
WB_Loader::load_script('my_script_11', array(
  'src' => 'script-11.js',
  'page' => 1,
  'post_type' => 'post',
  'admin' => false
));

// should load script only on single pages, author pages or category pages
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

// should load script on all frontend pages
WB_Loader::load_script('my_script_15', array(
  'src' => 'script-15.js',
  'admin' => false
));

// should create a JavaScript object on the global scope
WB_Loader::localize('my_data', array(
  'src' => 'script-15.js',
  'admin' => false,
  'settings' => array(
    'wburl' => WB_Loader::get_url()
  )
));
