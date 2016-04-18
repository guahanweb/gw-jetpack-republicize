<?php
namespace GW\JetPack\Republicize;

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\GW\JetPack\Republicize\JetPack')):

class JetPack {
  static public function instance() {
    static $instance;

    if (null === $instance) {
      $instance = new JetPack();
      $instance->config = Config::instance();
      $instance->listen();
    }

    return $instance;
  }

  public function __construct() {}

  public function unpublicize($post_id) {
    global $wpdb;

    $post_id = intval($post_id);
    $all_post_meta = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
    if (count($all_post_meta) != 0) {
      foreach ($all_post_meta as $post_meta) {
        $meta_key = $post_meta->meta_key;
        if (preg_match('/_wpas_(done_all|mess|skip_)/', $meta_key)) {
          delete_post_meta($post_id, $meta_key);
        }
      }
    }
  }

  public function addClearLink() {
    global $post;
    if (!current_user_can('publish_posts') || !is_object($post)) {
      return;
    }

    if (isset($_GET['post']) && get_post_meta($post->ID, '_wpas_done_all', true) == 1 && get_post_status($post->ID) !== 'publish') {
      $type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
      $action = 'clear_jetpack_published';
      $notifyUrl = wp_nonce_url(admin_url('edit.php?post_type='.$type.'&action='.$action.'&post='.absint($_GET['post'])), 'clear_jetpack_published_'.$_GET['post']);
      printf('<div id="jetpack-clear-action"><a class="submitjetpackclear jetpackclear" href="%s">%s</a></div>',
        $notifyUrl,
        __('Clear JetPack Publicized Status', $this->config->domain)
      );
    }
  }

  public function listen() {
    add_action('post_submitbox_start', array($this, 'addClearLink'));
  }
}

endif;
