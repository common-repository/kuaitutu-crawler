<?php
/**
 * Plugin Name: 快兔兔AI采集器-Auto Post
 * Description: 快兔兔AI采集器远程Auto Post插件，为避免拖慢网站，采集任务需要下载客户端执行： <a href="https://kuaitutu.com" target="_blank">下载地址</a>
 * Author: kuaitutu
 * Author URI: https://kuaitutu.com/about
 * Version: 1.4
 * Plugin URI: http://kuaitutu.com
 */

function kuaitutu_auth_handler( $user ) {
	global $wp_json_basic_auth_error;
	$wp_json_basic_auth_error = null;
	if ( ! empty( $user ) ) {
		return $user;
	}
	if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
		return $user;
	}

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];

	remove_filter( 'determine_current_user', 'kuaitutu_auth_handler', 20 );

	$user = wp_authenticate( $username, $password );

	add_filter( 'determine_current_user', 'kuaitutu_auth_handler', 20 );

	if ( is_wp_error( $user ) ) {
		$wp_json_basic_auth_error = $user;
		return null;
	}

	$wp_json_basic_auth_error = true;

	return $user->ID;
}
add_filter( 'determine_current_user', 'kuaitutu_auth_handler', 20 );

function kuaitutu_auth_error( $error ) {
	// Passthrough other errors
	if ( ! empty( $error ) ) {
		return $error;
	}

	global $wp_json_basic_auth_error;

	return $wp_json_basic_auth_error;
}
add_filter( 'rest_authentication_errors', 'kuaitutu_auth_error' );
