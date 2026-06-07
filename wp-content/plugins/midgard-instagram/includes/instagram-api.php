<?php

/**
 * source: https://github.com/doctorconceptual/php-instagram-graph-sdk/blob/master/src/instagram.php
 * This is a basic SDK for using Instagram Graph API.
 * I basically wrote this SDK when I found that Instagram is going to shutdown their legacy API
 * and encourage to use "Instagram Basic Display API" instead.
 */

namespace FF\Midgard\Instagram;

class InstagramAPI
{

	/**
	 * Get Instagram media on WordPress using the current Instagram (Facebook) API
	 *
	 * @param $token // Info on how to retrieve the token: https://www.gsarigiannidis.gr/instagram-feed-api-after-june-2020/
	 * @param $user // User ID can be found using the Facebook debug tool: https://developers.facebook.com/tools/debug/accesstoken/
	 * @param int $limit // Add a limit to prevent excessive calls.
	 * @param string $fields // More options here: https://developers.facebook.com/docs/instagram-basic-display-api/reference/media
	 * @param array $restrict // Available options: IMAGE, VIDEO, CAROUSEL_ALBUM
	 *
	 * @return array|mixed // Use it like that (minimal example): get_instagram_media(TOKEN, USER_ID);
	 */
	public static function get_media(
		$token,
		$user,
		$limit = 10,
		$fields = 'media_url,permalink,media_type,caption,timestamp',
		$restrict = ['IMAGE']
	) {
		// The request URL. see: https://developers.facebook.com/docs/instagram-basic-display-api/reference/user
		$request_url = 'https://graph.instagram.com/' . $user . '?fields=media&access_token=' . $token;

		// Prepare the data variable and set it as an empty array.
		$data = [];
		// Make the request
		$response      = wp_safe_remote_get($request_url);
		$response_body = '';
		if (is_array($response) && !is_wp_error($response)) {
			$response_body = json_decode($response['body']);
		}
		if ($response_body && isset($response_body->media->data)) {
			$i = 0;
			// Get each media item from it's ID and push it to the $data array.
			foreach ($response_body->media->data as $media) {
				if ($limit > $i) {
					$request_media_url = 'https://graph.instagram.com/' . $media->id . '?fields=' . $fields . '&access_token=' . $token;
					$media_response    = wp_safe_remote_get($request_media_url);
					if (is_array($media_response) && !is_wp_error($media_response)) {
						$media_body = json_decode($media_response['body'], true);
					}
					if (in_array($media_body['media_type'], $restrict, true)) {
						$i++;
						$data[] = $media_body;
					}
				}
			}
		}

		// refresh the token
		self::refresh_token($token);

		$output = $data;

		return $output;
	}

	/**
	 * Get my user ID from the given security token
	 */
	public static function get_my_id($token) {
		$response = wp_safe_remote_get('https://graph.instagram.com/me?access_token=' . $token);

		if (is_array($response) && !is_wp_error($response)) {
			$body = json_decode($response['body']);
			return $body;
		}

		return null;
	}

	// Refresh the token to make sure it never expires (see: https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens#refresh-a-long-lived-token)
	public static function refresh_token($token) {
		wp_safe_remote_get('https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $token);
	}
}