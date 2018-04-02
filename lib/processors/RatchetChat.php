<?php

// The Handler within the socket

require_once ROOT.DS.'lib/handlers/RatchetChatConnection.php';
require_once ROOT.DS.'lib/models/RatchetChatRepository.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class RatchetChat implements MessageComponentInterface{
	/**
	 * The chat repository
	 *
	 * @var RatchetChatRepository
	 */
	protected $sessionRepository;

	/**
	 * Chat Constructor
	 */
	public function __construct() {
		$this->sessionRepository = new RatchetChatRepository;
	}

	/**
	 * Called when a connection is opened
	 *
	 * @param ConnectionInterface $conn
	 * @return void
	 */
	public function onOpen(ConnectionInterface $conn) {
		$this->sessionRepository->addClient($conn);
	}

	/**
	 * Called when a message is sent through the socket
	 *
	 * @param ConnectionInterface $conn
	 * @param string $msg
	 * @return void
	 */
	public function onMessage(ConnectionInterface $conn, $msg) {
		// Parse the json
		$data = $this->parseMessage($msg);
		$currClient = $this->sessionRepository->getClientByConnection($conn);

		// Distinguish between the actions
		if ($data->action === "setname") {

			// currClient - Send it back to the Current sender...
			$currClient->setName($data->username);

			// Send it to the server
			$client = $this->sessionRepository->getClientByName("adminPanel");

			// Check if the server was already there or not...
			if ($client !== null){
				$client->getClientList();
			}

		} else if ($data->action === "add") {
			// Send it to the server
			$client = $this->sessionRepository->getClientByName("adminPanel");
			// Check if the server was already there or not...
			if ($client !== null){
				$client->addVisitor($data->amount, $data->datetime);
			}
		}
	}

	private function parseMessage($msg) {
		return json_decode($msg);
	}

	public function onClose(ConnectionInterface $conn) {
		// Fully Remove the Client from the List

		$currClient = $this->sessionRepository->getClientByConnection($conn);

		foreach ($this->sessionRepository->getClients() as $client){

			// the sender cannot receive the message back and it will only be send to LuisEdwardMiranda
			if ($currClient->getName() !== $client->getName() && $client->getName() === "adminPanel"){

				// Remove the Client in the list
				$this->sessionRepository->removeClient($conn);

				// Send the server a new List.
				$client->getClientList();
			}
		}

		// Double Remove
		$this->sessionRepository->removeClient($conn);
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "The following error occured: " . $e->getMessage();

		$client = $this->sessionRepository->getClientByConnection($conn);

		// We want to fully close the connection
		if ($client !== null) {
			$client->getConnection()->close();
			$this->sessionRepository->removeClient($conn);
		}
	}
}