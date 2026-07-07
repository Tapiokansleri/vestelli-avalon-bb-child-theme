<?php
/**
 * GitHub Release Updater
 *
 * Checks GitHub Releases for theme updates and integrates with WordPress update mechanism.
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class VA_GitHub_Updater {

  private $theme_slug = 'vestelli-avalon';
  private $github_repo = 'Tapiokansleri/vestelli-avalon-bb-child-theme';
  private $transient_key = 'va_github_update_check';
  private $cache_hours = 12;

  public function __construct() {
    add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
    add_filter( 'themes_api', array( $this, 'theme_info' ), 10, 3 );
    add_action( 'upgrader_process_complete', array( $this, 'clear_cache' ), 10, 2 );
  }

  /**
   * Check GitHub for a newer release.
   */
  public function check_for_update( $transient ) {
    if ( empty( $transient->checked ) ) {
      return $transient;
    }

    $current_version = wp_get_theme( $this->theme_slug )->get( 'Version' );
    $remote = $this->get_remote_version();

    if ( $remote && version_compare( $current_version, $remote['version'], '<' ) ) {
      $transient->response[ $this->theme_slug ] = array(
        'theme'       => $this->theme_slug,
        'new_version' => $remote['version'],
        'url'         => $remote['url'],
        'package'     => $remote['zip_url'],
      );
    }

    return $transient;
  }

  /**
   * Provide theme info for the WordPress updates detail popup.
   */
  public function theme_info( $result, $action, $args ) {
    if ( $action !== 'theme_information' || ! isset( $args->slug ) || $args->slug !== $this->theme_slug ) {
      return $result;
    }

    $remote = $this->get_remote_version();
    if ( ! $remote ) {
      return $result;
    }

    $theme = wp_get_theme( $this->theme_slug );

    return (object) array(
      'name'           => $theme->get( 'Name' ),
      'slug'           => $this->theme_slug,
      'version'        => $remote['version'],
      'author'         => $theme->get( 'Author' ),
      'homepage'       => 'https://github.com/' . $this->github_repo,
      'sections'       => array(
        'description'  => $theme->get( 'Description' ),
        'changelog'    => nl2br( esc_html( $remote['changelog'] ) ),
      ),
      'download_link'  => $remote['zip_url'],
    );
  }

  /**
   * Clear cached data after an update.
   */
  public function clear_cache( $upgrader, $options ) {
    if ( $options['type'] === 'theme' && isset( $options['themes'] ) && in_array( $this->theme_slug, $options['themes'], true ) ) {
      delete_transient( $this->transient_key );
    }
  }

  /**
   * Fetch the latest release info from GitHub (cached).
   */
  private function get_remote_version() {
    $cached = get_transient( $this->transient_key );
    if ( $cached !== false ) {
      return $cached;
    }

    $url = 'https://api.github.com/repos/' . $this->github_repo . '/releases/latest';

    $response = wp_remote_get( $url, array(
      'timeout' => 10,
      'headers' => array(
        'Accept' => 'application/vnd.github.v3+json',
      ),
    ) );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
      return false;
    }

    $release = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $release['tag_name'] ) ) {
      return false;
    }

    $version = ltrim( $release['tag_name'], 'v' );

    // Prefer a .zip asset named vestelli-avalon.zip; fall back to GitHub source zip
    $zip_url = $release['zipball_url'];
    if ( ! empty( $release['assets'] ) ) {
      foreach ( $release['assets'] as $asset ) {
        if ( $asset['content_type'] === 'application/zip' && strpos( $asset['name'], 'vestelli-avalon' ) !== false ) {
          $zip_url = $asset['browser_download_url'];
          break;
        }
      }
    }

    $data = array(
      'version'   => $version,
      'zip_url'   => $zip_url,
      'url'       => $release['html_url'],
      'changelog' => isset( $release['body'] ) ? $release['body'] : '',
    );

    set_transient( $this->transient_key, $data, $this->cache_hours * HOUR_IN_SECONDS );

    return $data;
  }
}

new VA_GitHub_Updater();
