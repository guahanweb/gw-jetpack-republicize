<?php
namespace GW\JetPack\Republicize;

if (!class_exists('\GW\JetPack\Republicize\Config')):

class Config {
  private $data;

  static public function instance() {
    static $instance;

    if (null === $instance) {
      $instance = new Config();
      $instance->setup();
    }

    return $instance;
  }

  public function __get($k) {
    if (isset($this->data->$k)) {
      return $this->data->$k;
    }
    return null;
  }

  public function __set($k, $v) {
    $this->data->$k = $v;
  }

  public function setup() {
    define('GW_JETPACK_REPUBLICIZE_VERSION', '1.0');
    define('GW_JETPACK_REPUBLICIZE_DOMAIN', 'gw-jetpack-republicize');
    define('GW_JETPACK_REPUBLICIZE_OPTIONS', 'gw_jetpack_republicize');
    $this->globals();
  }

  public function globals() {
    $this->data = new \stdClass();

    $this->version = GW_JETPACK_REPUBLICIZE_VERSION;
    $this->domain = GW_JETPACK_REPUBLICIZE_DOMAIN;
    $this->options = GW_JETPACK_REPUBLICIZE_OPTIONS;

    $this->file = dirname(dirname(__FILE__)) . '/gw_jetpack_republicize.php';
    $this->plugin_dir = plugin_dir_path($this->file);
    $this->plugin_url = plugin_dir_url($this->file);
    $this->settings_path = 'gw-jetpack-republicize/includes/settings.php';
    $this->settings_url = get_admin_url(null, 'options-general.php?page=' . $this->settings_path);
  }
}

function gw_jetpack_republicize_config_init() {
  return Config::instance();
}

$gw_jetpack_republicize_config = gw_jetpack_republicize_config_init();

endif;
