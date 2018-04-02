<?php


use Ratchet\ConnectionInterface;

require_once "RatchetChatRepositoryInterface.php";

class RatchetChatRepository implements RatchetChatRepositoryInterface {
	/**
	 * All the connected clients
	 *
	 * @var SplObjectStorage
	 */
	private $clients;

	/**
	 * ChatRepository Constructor
	 */
	public function __construct() {
		$this->clients = new SplObjectStorage;
	}

	public function getClientByName($name) {
		foreach ($this->clients as $client)
		{
			if ($client->getName() === $name)
				return $client;
		}

		return null;
	}

	public function getClientByConnection(ConnectionInterface $conn)
	{
		foreach ($this->clients as $client)
		{
			if ($client->getConnection() === $conn)
				return $client;
		}

		return null;
	}

	public function addClient(ConnectionInterface $conn) {
		$this->clients->attach(
			new RatchetChatConnection($conn, $this)
		);
	}

	public function removeClient(ConnectionInterface $conn) {
		$client = $this->getClientByConnection($conn);

		if ($client !== null)
			$this->clients->detach($client);
	}

	public function getClients(){
		return $this->clients;
	}

	public function getClientNames(){
		$arr = array();
		foreach ($this->clients as $client){
			// Put all clients in the array excluding the server's
			if ($client->getName() != "adminPanel"){
				$arr[] = $client->getName();
			}
		}
		return $arr;
	}
}