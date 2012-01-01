<?php
/*
 * Google / Oauth2 PHP  
 *
 * @package		Google Oauth2
 * @author		Dwi Setiyadi / @dwisetiyadi
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://dwi.web.id
 * @version		1
 * Last changed	2 Jan, 2012
 */

// ------------------------------------------------------------------------

/**
 * This class object
 */
class Google {
	var $cid;
	var $csecret;
	var $redirect_uri;
	var $scope;
	var $response_type = 'code';
	var $ccode;
	var $endpoint = 'https://accounts.google.com/o/oauth2';

	/**
	 * Constructor
	 * Configure API setting
	 */
	function Google($params = array()) {
		$this->initialize($params);
	}
	
	function initialize($params = array()) {
		if (isset($params['client_id'])) $this->cid = $params['client_id'];
		if (isset($params['client_secret'])) $this->csecret = $params['client_secret'];
		if (isset($params['redirect_uri'])) $this->redirect_uri = $params['redirect_uri'];
		if (isset($params['scope'])) $this->scope = 'https://www.google.com/'.$params['scope'];
		if (isset($params['response_type'])) $this->response_type = $params['response_type'];
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Assigns a code value to request access token from Google.com
	 *
	 * @access public
	 * @return void
	 */
	function setcode($code = '') {
		$this->ccode = $code;
	}

	// --------------------------------------------------------------------

	/**
	 * Autorize
	 *
	 * Authorization process to Google.com
	 *
	 * @access public
	 * @return void
	 */
	function authorize() {
		$uri = trim($this->endpoint, '/').'/auth?';
		$uri .= 'client_id='.$this->cid.'&';
		$uri .= 'redirect_uri='.$this->redirect_uri.'&';
		$uri .= 'scope='.$this->scope.'&';
		$uri .= 'response_type='.$this->response_type;
		return $uri;
	}

	// --------------------------------------------------------------------

	/**
	 * Access Token
	 *
	 * Get access token value
	 * Return object with three value when success authorize
	 * And return void when no failed authorize.
	 *
	 * To access it, use:
	 * [CLASS OBJECT]->token()->access_token for access token
	 * [CLASS OBJECT]->token()->expires_in for access token
	 * [CLASS OBJECT]->token()->refresh_token for refresh token code, this is use for refreshing a new access token
	 *
	 * @access public
	 * @return object
	 * @return void
	 */
	function token() {
		$uri = trim($this->endpoint, '/').'/token';
		$attachment['code'] = $this->ccode;
		$attachment['client_id'] = $this->cid;
		$attachment['client_secret'] = $this->csecret;
		$attachment['redirect_uri'] = $this->redirect_uri;
		$attachment['grant_type'] = 'authorization_code';
		
		$response = $this->post($uri, $attachment);
		if (isset($response)) {
			if ( ! empty($response)) {
				$response = json_decode($response);
			}
			
			return $response;
		} else {
			return;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Refresh Access Token
	 *
	 * Get refresh access token after expired access token
	 * Return object with three value when success refresh authorize
	 * And return void when no failed refresh authorize.
	 *
	 * To access it, use:
	 * [CLASS OBJECT]->refreshtoken($refreshtoken)->access_token for access token
	 * [CLASS OBJECT]->refreshtoken($refreshtoken)->expires_in for access token
	 * [CLASS OBJECT]->refreshtoken($refreshtoken)->refresh_token for refresh token code, this is use for refreshing a new access token
	 *
	 * @access public
	 * @param string refreshtoken
	 * @return object
	 * @return void
	 */
	function refreshtoken($refreshtoken = '') {
		$uri = '/token';
		$attachment['client_id'] = $this->cid;
		$attachment['client_secret'] = $this->csecret;
		$attachment['refresh_token'] = $refreshtoken;
		$attachment['grant_type'] = 'refresh_token';
		
		$response = $this->post($uri, $attachment);
		if (isset($response)) {
			if ( ! empty($response)) {
				$response = json_decode($response);
			}
			
			return $response;
		} else {
			return;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Data cURL Console
	 * @access public
	 * @param string url
	 * @param array attachment
	 * @return string
	 */
	public function call($url = '') {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		ob_start();
		$result = curl_exec($ch);
		if ($result === false) {
			$result = curl_error($ch);
		}
		ob_end_clean();
		curl_close($ch);
		
		return $result;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Post Data cURL Console
	 * @access public
	 * @param string url
	 * @param array attachment
	 * @return string
	 */
	public function post($url = '', $attachment = array()) {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		ob_start();
		$result = curl_exec($ch);
		if ($result === false) {
			$result = curl_error($ch);
		}
		ob_end_clean();
		curl_close($ch);
		
		return $result;
	}
}
?>