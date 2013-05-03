<?php

namespace Kayako;

class StaffAPI {

	/**
	 * The Kayako Staff API End Point e.g http://support.mycompany.com/staffapi/index.php
	 *
	 * @var string
	 * @access private
	 */
	private $endPoint;
	
	/**
	 * The kayako username to get tickets for
	 *
	 * @var string 
	 * @access private
	 */
	private $username;
	
	/**
	 * The kayako password
	 *
	 * @var string
	 * @access private
	 */
	private $password;
	
	/**
	 * Session ID
	 *
	 * @var string
	 * @access private
	 */
	private $session_id = null;
	
	/**
	 * Constructor
	 *
	 */
	public function __construct($endPoint, $username, $password)
	{
		$this->endPoint = $endPoint;
		$this->username = $username;
		$this->password = $password;

		$this->login();
	}
	
	/**
	 * Find a ticket
	 *
	 * @param integer $ticketID
	 * @param integer $requirePosts Whether to include posts aswell. Set 1 to include posts. Default 0
	 * @param integer $requireAttachments Whether to include attachment data. Set 1 to include attachments. Default 0
	 * @param array $limit The offset (start) and number of rorws (limit)
	 * @return SimpleXMLElement|null
	 */
	public function find($ticketID, $requirePosts = 0, $requireAttachments = 0, $limit = array())
	{
		$params = array('sessionid' => $this->session_id, 'ticketid' => $ticketID);
		$params['wantpostsonly'] = (int)$requirePosts;
		$params['wantattachmentdata '] = (int)$requireAttachments;
		
		$response = $this->doRequest('/Tickets/Retrieve/Data', $params);
		
		$tickets = $response->tickets;
		
		if (isset($tickets->ticket)) {
			return $tickets->ticket;
		}
		
		return null;
	}
	
	/**
	 * Find all tickets by specific criteria
	 *
	 * @param array $options An array of options including departmentid, statusid, ownerid, filterid, ticketid
	 * @param integer $requireTickets Whether to include tickets data. Set 1 to include data. Default 0
	 * @param integer $requireAttachments Whether to include ticket attachment data. Set 1 to include data. Default 0
	 * @param array $orderBy Order the tickets by a column (sortby) and order (sortorder)
	 * @param array $limit The offset (start) and number of rorws (limit)
	 */
	public function findAllBy($options = array(), $requireTickets = 0, $requireAttachments = 0, $orderBy = array(), $limit = array())
	{
		$params = array('sessionid' => $this->session_id, 'wantticketdata' => (int)$requireTickets, 'wantattachmentdata ' => (int)$requireAttachments);
		
		$params = array_merge($params, $options);
		$params = array_merge($params, $orderBy);
		$params = array_merge($params, $limit);
		
		$response = $this->doRequest('/Tickets/Retrieve', $params);

		return $response->tickets;
	}
	
	/**
	 * Search for Kayako tickets
	 *
	 * @param array $options The array of options. Which field to search against
	 * @param array $limit The offset (start) and number of rows (limit)
	 * @return SimpleXMLElement
	 */
	public function search($query, $options = array(), $limit = array())
	{
		$params = array('sessionid' => $this->session_id, 'query' => $query);
		
		$params = array_merge($params, $options);
		$params = array_merge($params, $limit);
		
		$response = $this->doRequest('/Tickets/Retrieve/Search', $params);

		return $response->tickets;
	}
	
	/**
	 * Logout of Kayako and delete the session
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$params = array('sessionid' => $this->session_id);
		
		$this->doRequest('/Core/Default/Logout', $params);
		
		$this->session_id = null;
	}
	
	/**
	 * Login to the kayako API
	 *
	 * @return void
	 */
	protected function login()
	{
		$response = $this->doRequest('/Core/Default/Login', array('username' => $this->username, 'password' => $this->password));
		
		$this->session_id = (string)$response->sessionid;
	}
	
	/**
	 * Do a curl request to the kayako api
	 * 
	 * @param string $uri The URI for the request
	 * @param array $params The param
	 * @return SimpleXML A simple XML object
	 */
	protected function doRequest($uri, $params)
	{
		$qsa = http_build_query($params);

		$ch = curl_init ($this->endPoint . '?' . $uri);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $qsa);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		$response = curl_exec ($ch);
		
		$xml = simplexml_load_string($response);
		
		curl_close ($ch);
		
		return $xml;
	}
}