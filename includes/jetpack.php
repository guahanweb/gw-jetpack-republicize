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
    $all_post_meta = get_post_meta($post_id);
    if (count($all_post_meta) != 0) {
      foreach ($all_post_meta as $meta_key => $meta_array) {
        $meta_key = $post_meta->meta_key;
        if (preg_match('/_wpas_(done_all|mess|skip_)/', $meta_key)) {
          delete_post_meta($post_id, $meta_key);
        }
      }
    }
  }

  public function clearPublished() {
    if (empty($_REQUEST['post'])) {
      wp_die(__('Invalid post ID or action', $this->config->domain));
    }

    $id = isset($_REQUEST['post']) ? absint($_REQUEST['post']) : '';
    check_admin_referer('clear_jetpack_published_' . $id);

    $this->unpublicize($id);
    wp_redirect(admin_url('post.php?action=edit&post=' . $id));
    exit;
  }

  public function addClearLink() {
    global $post;
    if (!current_user_can('publish_posts') || !is_object($post)) {
      echo 'fail';
      return;
    }

    if (isset($_GET['post']) && get_post_meta($post->ID, '_wpas_done_all', true) == 1 && get_post_status($post->ID) !== 'publish') {
      $query = array(
        'post=' . absint($_GET['post']),
        'action=clear_jetpack_published'
      );

      if (isset($_GET['post_type'])) {
        $query[] = 'post_type=' . $post->post_type;
      }

      echo '<pre>' . print_r($query, true) . '</pre>';

      $notifyUrl = wp_nonce_url(admin_url('edit.php?' . implode('&', $query)), 'clear_jetpack_published_' . $_GET['post']);
      printf('<div id="jetpack-clear-action"><a class="submitjetpackclear jetpackclear" href="%s">%s</a></div>',
        esc_url($notifyUrl),
        __('Clear JetPack Publicized Status', $this->config->domain)
      );
    }
  }

  public function listen() {
    add_action('post_submitbox_start', array($this, 'addClearLink'));
    add_action('admin_action_clear_jetpack_published', array($this, 'clearPublished'));
  }
}

function gw_jetpack_republicize_jetpack_init() {
  return JetPack::instance();
}

$gw_jetpack_republicize_jetpack = gw_jetpack_republicize_jetpack_init();

endif;
