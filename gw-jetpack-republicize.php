<?php
/**
 * Plugin Name: JetPack Republicize
 * Plugin URI: http://www.guahanweb.com
 * Description: Support for republicizing posts through JetPack Publicize module
 * Version: 1.0
 * Tested With: 4.5
 * Author: Garth Henson <garth@guahanweb.com>
 * Author URI: http://www.guahanweb.com
 * License: GPLv2 or later
 * Text Domain: gw-jetpack-republicize
 * Domain Path: /languages
 */

namespace GW\JetPack\Republicize;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\GW\JetPack\Republicize\Plugin')):

class Plugin {
  static public function instance() {
    static $instance;

    if (null === $instance) {
      require_once __DIR__ . '/includes/config.php';

      $instance = new Plugin();
      $instance->config = Config::instance();
      $instance->includes();
      $instance->listen();
    }

    return $instance;
  }

  public function includes() {
    // Admin Settings Page
    // require_once $this->config->plugin_dir . '/includes/settings.php';

    // JetPack actions
    require_once $this->config->plugin_dir . '/includes/jetpack.php';
  }

  public function listen() {
    register_activation_hook($this->config->file, array($this, 'activate'));
    register_deactivation_hook($this->config->file, array($this, 'deactivate'));
  }

  public function activate() {
    $version = get_option('gw_jetpack_republicize_version');
    if ($version !== $this->config->version) {
      // Custom install stuff here if we have a migration
      // require_once $this->config->plugin_dir . 'includes/install.php'
      update_option('gw_jetpack_republicize_version', $this->config->version);
      do_action('gw_jetpack_republicize-activate');
    }
  }

  public function deactivate() {
    delete_option('gw_jetpack_republicize_version');
    do_action('gw_jetpack_republicize-deactivate');
  }

  public function deactivate() {

  }
}

function gw_jetpack_republicize_init() {
  return Plugin::instance();
}

$gw_jetpack_republicize = gw_jetpack_republicize_init();

endif;
