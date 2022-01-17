<?php

	namespace CirclePHP;

	use Curl\Curl;

	class Circle {

		private $api_key;

		public function __construct(string $api_key) {

			$this->api_key = $token;
			$this->curl = new Curl();
			$curl->setHeader('Authorization', "Token {$this->api_key}");
			$this->baseUrl = 'https://app.circle.so/api/v1/';
		}

		function toAscii($str, $replace = [], $delimiter = '-') {
			setlocale(LC_ALL, 'en_US.UTF8');
			# Remove spaces
			if( !empty($replace) ) {
				$str = str_replace((array)$replace, ' ', $str);
			}
			# Remove non-ascii characters
			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
			# Remove non alphanumeric characters and lowercase the result
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			# Remove other unwanted characters
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
			return $clean;
		}

		// Me

		public function me() {

			$this->curl->get($this->baseUrl . '/me');

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Communities

		public function communities() {

			$this->curl->get($this->baseUrl . '/communities');

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function community($community_id, $slug) {

			$this->curl->get($this->baseUrl . "/communities/{$community_id}?slug={$slug}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Space Groups

		public function spaceGroups($community_id) {

			$this->curl->get($this->baseUrl . "/space_groups?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function spaceGroup($space_group_id, $community_id) {

			$this->curl->post($this->baseUrl . "/space_groups/{$space_group_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Space Group Members

		public function addSpaceGroupMember($email, $space_group_id, $community_id) {

			$this->curl->post($this->baseUrl . "/space_group_members?email={$email}&space_group_id={$space_group_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function removeSpaceGroupMember($email, $space_group_id, $community_id) {

			$this->curl->delete($this->baseUrl . "/space_group_members?email={$email}&space_group_id={$space_group_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function spaceGroupMember($email, $space_group_id, $community_id) {

			$this->curl->get($this->baseUrl . "/space_group_member?community_id={$community_id}&email={$email}&space_group_id={$space_group_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Spaces

		public function spaces($community_id, $sort = 'active', $per_page = 60, $page = 1) {

			$this->curl->get($this->baseUrl . "spaces?community_id={$community_id}&sort={$sort}&per_page={$per_page}&page={$page}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function space($space_id, $community_id) {

			$this->curl->get($this->baseUrl . "spaces/{$space_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createSpace($community_id, $space_group_id, $name, $options = []) {

			$params = [
				'is_private' => false,
				'is_hidden_from_non_members' => false,
				'is_hidden' => false,
				'slug' => $this->toAscii($name)
			];

			$params = array_merge($params, $options);

			$this->curl->post($this->baseUrl . "spaces?community_id={{community_id}}&name={$name}&space_group_id={$space_group_id}&" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function destroySpace($space_id) {

			$this->curl->delete($this->baseUrl . "spaces/{$space_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Space Members

		public function addSpaceMember($email, $space_id, $community_id) {

			$this->curl->post($this->baseUrl . "space_members?email={$email}&space_id={$space_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function removeSpaceMember($email, $space_id, $community_id) {

			$this->curl->delete($this->baseUrl . "space_members?email={$email}&space_id={$space_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Posts

		public function posts($space_id, $community_id, $options = []) {

			$params = [
				'sort' => 'latest',
				'per_page' => 60,
				'page' => 1
			];

			$params = array_merge($params, $options);

			$this->curl->get($this->baseUrl . "posts?community_id={$community_id}&space_id={$space_id}&" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function post($post_id, $community_id) {

			$this->curl->get($this->baseUrl . "posts/{$post_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createPost($space_id, $community_id, $name, $body, $options = []) {

			$params = [];

			$params = array_merge($params, $options);

			$this->curl->post($this->baseUrl . "posts/?community_id={$community_id}&space_id={$space_id}" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function destroyPost($post_id, $space_id, $community_id) {

			$this->curl->delete($this->baseUrl . "posts/{$post_id}?community_id={$community_id}&space_id={$space_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		//Comments

		public function comments($space_id, $community_id, $post_id = false) {

			$this->curl->get($this->baseUrl . "comments?community_id={$community_id}&space_id={$space_id}" . ($post_id ? "&post_id={$post_id}" : ''));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function comment($comment_id, $community_id) {

			$this->curl->get($this->baseUrl . "comments/{$comment_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createComment($space_id, $community_id, $body, $post_id = false, $options = []) {

			$params = [];
			if($post_id) $params['post_id'] = $post_id;

			$params = array_merge($params, $options);

			$this->curl->post($this->baseUrl . "comments?community_id={$community_id}&space_id={$space_id}&body={$body}&" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function destroyComment($comment_id, $community_id) {

			$this->curl->delete($this->baseUrl . "comments/{$comment_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}
	}
?>