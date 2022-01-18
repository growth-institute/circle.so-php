<?php

	namespace CirclePHP;

	use Curl\Curl;

	class Circle {

		private $api_key;
		private $community_id;

		public function __construct(string $api_key) {

			$this->api_key = $api_key;
			$this->curl = new Curl();
			$this->curl->setHeader('Authorization', "Token {$this->api_key}");
			$this->baseUrl = 'https://app.circle.so/api/v1/';
		}

		public function setCommunity($id) {
			$this->community_id = $id;
		}

		public function setSpaceGroup($id) {
			$this->space_group_id = $id;
		}

		public function getCommunity() {
			return $this->community_id;
		}

		public function toAscii($str, $replace = [], $delimiter = '-') {
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

		public function spaceGroups($community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "/space_groups?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function spaceGroup($space_group_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "/space_groups/{$space_group_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Space Group Members

		public function addSpaceGroupMember($email, $space_group_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->post($this->baseUrl . "/space_group_members?email={$email}&space_group_id={$space_group_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function removeSpaceGroupMember($email, $space_group_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "/space_group_members?email={$email}&space_group_id={$space_group_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function spaceGroupMember($email, $space_group_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "/space_group_member?community_id={$community_id}&email={$email}&space_group_id={$space_group_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Spaces

		public function spaces($options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$params = [
				'sort' => 'active',
				'per_page' => 60,
				'page' => 1
			];

			$params = array_merge($params, $options);

			$this->curl->get($this->baseUrl . "spaces?community_id={$community_id}&" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function space($space_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "spaces/{$space_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createSpace($name, $options = [], $space_group_id = 0, $community_id = 0) {

			$space_group_id = $space_group_id ?: $this->space_group_id;
			$community_id = $community_id ?: $this->community_id;
			if(!$community_id || !$space_group_id) return false;

			$params = [
				'is_private' => false,
				'is_hidden_from_non_members' => false,
				'is_hidden' => false,
				'slug' => $this->toAscii($name)
			];

			$params = array_merge($params, $options);
			$name = urlencode($name);

			$this->curl->post($this->baseUrl . "spaces?community_id={$community_id}&name={$name}&space_group_id={$space_group_id}&" . http_build_query($params));

			$response = json_decode($this->curl->response);

			if(isset($response->success) && $response->success == true) {
				$response = $response->space;
			} else {
				$response = false;
			}

			return $response;
		}

		public function destroySpace($space_id) {

			$this->curl->delete($this->baseUrl . "spaces/{$space_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Space Members

		public function addSpaceMember($email, $space_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->post($this->baseUrl . "space_members?email={$email}&space_id={$space_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function removeSpaceMember($email, $space_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "space_members?email={$email}&space_id={$space_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Posts

		public function posts($space_id, $options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

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

		public function post($post_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "posts/{$post_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createPost($space_id, $name, $body, $options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$params = [];
			$params = array_merge($params, $options);

			$name = urlencode($name);
			$body = urlencode($body);

			$this->curl->post($this->baseUrl . "posts/?name={$name}&body={$body}&community_id={$community_id}&space_id={$space_id}&" . http_build_query($params));
			$response = json_decode($this->curl->response);

			if(isset($response->success) && $response->success == true) {
				$response = ['post' => $response->post, 'topic' => $response->topic];
			} else {
				$response = false;
			}

			return $response;
		}

		public function destroyPost($post_id, $space_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "posts/{$post_id}?community_id={$community_id}&space_id={$space_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		//Comments

		public function comments($space_id, $post_id = false, $community_id = 0) {

			$this->curl->get($this->baseUrl . "comments?community_id={$community_id}&space_id={$space_id}" . ($post_id ? "&post_id={$post_id}" : ''));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function comment($comment_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "comments/{$comment_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createComment($post_id, $space_id, $body, $options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$params = [];
			if($post_id) $params['post_id'] = $post_id;

			$params = array_merge($params, $options);

			$body = urlencode($body);

			$this->curl->post($this->baseUrl . "comments?post_id={$post_id}&community_id={$community_id}&space_id={$space_id}&body={$body}&" . http_build_query($params));
			$response = json_decode($this->curl->response);

			if(isset($response->success) && $response->success == true) {
				$response = $response->comment;
			} else {
				$response = false;
			}

			return $response;
		}

		public function destroyComment($comment_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "comments/{$comment_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Members

		public function members($options = []) {

			$params = [
				'sort' => 'latest',
				'per_page' => 10,
				'page' => 1,
				'status' => 'active'
			];

			$params = array_merge($params, $options);

			$this->curl->get($this->baseUrl . "community_members?sort=latest&per_page=2&page=1&" . http_build_query($params));;

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function member($member_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "community_members/{$member_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function inviteMember($email, $name, $options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$params = [];
			$params = array_merge($params, $options);

			$name = urlencode($name);

			$this->curl->post($this->baseUrl . "community_members?email={$email}&name={$name}&community_id={$community_id}&" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function removeMember($email, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "community_members?community_id={$community_id}&email={$email}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function updateMember($member_id, $options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$params = [];
			$params = array_merge($params, $options);

			$this->curl->put($this->baseUrl . "community_members/{$member_id}?community_id={$community_id}&" . http_build_query($params));

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function searchMember($email, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "community_members/search?community_id={$community_id}&email={$email}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Member Tag

		public function memberTags($community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "member_tags?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function memberTag($member_tag_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "member_tags/{$member_tag_id}?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Tagged Members

		public function taggedMembers() {

			$this->curl->get($this->baseUrl . "tagged_members");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function taggedMember($tagged_member_id) {

			$this->curl->get($this->baseUrl . "tagged_members/{$tagged_member_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function tagMember($email, $member_tag_id) {

			$this->curl->post($this->baseUrl . "tagged_members?user_email={$email}&member_tag_id={$member_tag_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function untagMember($email, $member_tag_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "tagged_members?user_email={$email}&member_tag_id={$member_tag_id}&community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Group Messages

		public function startGroupChat($message_body, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->post($this->baseUrl . "messages?community_id={$community_id}&message_body={$message_body}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Direct Messages

		public function sendMessage($email, $message_body, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->post($this->baseUrl . "messages?community_id={{community_id}}&user_email={$email}&message_body={$message_body}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Member Subscriptions

		public function memberSubscriptions($community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "community_member_subscriptions?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Events

		public function events() {

			$this->curl->get($this->baseUrl . "events");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function createEvent($event, $space_id, $community_id = 0) {

			$this->curl->post($this->baseUrl . "events?community_id={$community_id}&space_id={$space_id}", $event);

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function updateEvent($event, $space_id, $community_id = 0) {

			$this->curl->put($this->baseUrl . "events?community_id={$community_id}&space_id={$space_id}", $event);

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Event Attendees

		public function eventAttendees($event_id, $options = [], $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$params = [
				'community_id' => $community_id,
				'event_id' => $event_id
			];

			$params = array_merge($params, $options);

			$this->curl->get($this->baseUrl . "event_attendees");

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function rsvpMember($email, $event_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->post($this->baseUrl . "event_attendees", [
				'community_id' => $community_id,
				'event_id' => $event_id,
				'email' => $email
			]);

			$response = json_decode($this->curl->response);
			return $response;
		}

		public function unrsvpMember($email, $event_id, $community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->delete($this->baseUrl . "event_attendees", [
				'community_id' => $community_id,
				'event_id' => $event_id,
				'email' => $email
			]);

			$response = json_decode($this->curl->response);
			return $response;
		}

		// Member Charges

		public function memberCharges($community_id = 0) {

			$community_id = $community_id ?: $this->community_id;
			if(!$community_id) return false;

			$this->curl->get($this->baseUrl . "community_member_charges?community_id={$community_id}");

			$response = json_decode($this->curl->response);
			return $response;
		}
	}
?>