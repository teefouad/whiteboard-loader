<?php

function wb_enqueue_assets($asset_type) {
  session_start();

  if (!isset($_SESSION['wb_loader_data'])) {
    die('No data');
  }

  $data = $_SESSION['wb_loader_data'];
  $content = '';
  $filename = '';
  $timestamp = 0;

  foreach ($data['enqueued_' . $asset_type . 's'] as $asset_name => $asset_args) {
    $asset = wb_enqueue_asset($asset_type, $asset_name, $asset_args, $data['localization_data']);
    $content .= $asset['content'];
    $filename .= $asset_name;
    $timestamp = max($timestamp, $asset['timestamp']);
  }

  /* headers */
  header('Content-Type: ' . ($asset_type === 'script' ? 'application/x-javascript' : 'text/css') . '; charset=UTF-8');
  header('Content-Length: ' . strlen($content));

  /* compress */
  if ($data['compression_enabled']) {
    $content = wb_use_compression($content, $data['force_gzip_compression']);
  }

  /* caching */
  if ($data['caching_enabled']) {
    wb_use_cache($filename, $timestamp);
  }

  session_unset();
  session_destroy();

  echo $content;
}

function wb_enqueue_asset($asset_type, $asset_name, $asset_args, $localization_data) {
  $return = array(
    'content'   => array(),
    'timestamp' => 0,
  );

  if (!empty($asset_args['src'])) {
    if (!is_array($asset_args['src'])) {
      $asset_args['src'] = array($asset_args['src']);
    }

    foreach ($asset_args['src'] as $n => $src) {
      if (file_exists($src) || strpos($src, 'http://') === 0 || strpos($src, 'https://') === 0) {
        $fc = @file_get_contents($src);

        if ($fc === false) {
          $fc = '/* error loading file: ' . $src . ' */';
        } else {
          $fc .= $asset_type === 'script' ? ';' : '';
          $return['timestamp'] = max($return['timestamp'], @filemtime($src));
        }

        /* handle CSS placeholders */
        foreach ($localization_data as $data_set) {
          wb_replace_css_vars($data_set['name'], $data_set['data'], $fc);
        }

        /* fix relative urls */
        if ($asset_type === 'style') {
          $i = 0;
          $j = 0;

          while ($i = strpos($fc, 'url(', $j)) {
            $j = strpos($fc, ')', $i);
            $i += 4;
            $u = trim(substr($fc, $i, $j - $i), '\'"');

            if (strpos($u, 'http://') === false && strpos($u, 'https://') === false && strpos($u, 'data:') === false) {
              $u = dirname(wb_get_relative_path(__FILE__, $src)) . '/' . $u;
            }

            $fc = substr($fc, 0, $i) . $u . substr($fc, $j);
            $j = strpos($fc, ')', $i);
          }
        }

        $return['content'][$asset_name . '-' . $n] = $fc;
      } else {
        if ($asset_args['report'] === true) {
          $return['content'][$asset_name . '-' . $n] = '/* file not found: ' . $src . ' */';
        } else {
          $return['content'][$asset_name . '-' . $n] = '';
        }
      }

      $return['content'][$asset_name . '-' . $n] .= "\r\n\r\n";
    }
  }

  $return['content'] = implode('', $return['content']);

  return $return;
}

function wb_replace_css_vars($prefix, $data, &$css) {
  foreach ($data as $k => $v) {
    if (is_string($v)) {
      $css = str_replace('[[' . $prefix . '.' . $k . ']]', $v, $css);
    } else {
      if (is_array($v)) {
        wb_replace_css_vars($prefix . '.' . $k, $v, $css);
      }
    }
  }
}

function wb_get_relative_path($from, $to) {
  $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
  $from = str_replace('\\', '/', $from);
  $from = explode('/', $from);

  $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
  $to = str_replace('\\', '/', $to);
  $to = explode('/', $to);

  $rel_path = $to;

  foreach ($from as $depth => $dir) {
    if ($dir === $to[$depth]) {
      array_shift($rel_path);
    } else {
      $remaining = count($from) - $depth;

      if ($remaining > 1) {
        $pad_length = (count($rel_path) + $remaining - 1) * -1;
        $rel_path = array_pad($rel_path, $pad_length, '..');
        break;
      } else {
        $rel_path[0] = './' . $rel_path[0];
      }
    }
  }

  return implode('/', $rel_path);
}

function wb_use_compression($content, $force_gzip = false) {
  if (ini_get('zlib.output_compression') || ini_get('output_handler') === 'ob_gzhandler' || !isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    return $content;
  }

  header('Vary: Accept-Encoding'); // handle proxies

  if (stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false && function_exists('gzdeflate') && !$force_gzip) {
    $encoded = gzdeflate($content, 3);
    header('Content-Encoding: deflate');
    header('Content-Length: ' . strlen($encoded));
    return $encoded;
  } else
  if (stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('gzencode')) {
    $encoded = gzencode($content, 3);
    header('Content-Encoding: gzip');
    header('Content-Length: ' . strlen($encoded));
    return $encoded;
  }

  return $content;
}

function wb_use_cache($file, $timestamp, $use_etag = true) {
  $expires_offset = 31536000; // 1 year
  $gmt_mtime = gmdate('r', $timestamp);

  header('Cache-Control: public, max-age=' . $expires_offset);
  header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $expires_offset) . ' GMT');
  header('Last-Modified: ' . $gmt_mtime);
  header('Pragma: cache');

  if ($use_etag) {
    header('ETag: "' . md5($timestamp . $file) . '"');

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
      if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] === $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) === md5($timestamp . $file)) {
        header('HTTP/1.1 304 Not Modified');
      }
    }
  }
}
