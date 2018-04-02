<?php

use Ratchet\ConnectionInterface;

require_once "RatchetChatConnectionInterface.php";

class RatchetChatConnection implements RatchetChatConnectionInterface {

	private $connection;
	private $name;
	private $repository;

	public function __construct(ConnectionInterface $conn, RatchetChatRepositoryInterface $repository, $name = "") {
		$this->connection = $conn;
		$this->name = $name;
		$this->repository = $repository;
	}

	public function getConnection(){
		return $this->connection;
	}

	public function setName($name){
		// If name not found return
		if ($name === "") return;

		// Check if the name exists already
		if ($this->repository->getClientByName($name) !== null) {
			$this->send([
				'action'   => 'setname',
				'success'  => false,
				'username' => $this->name
			]);
			return;
		}

		// Save the new name
		$this->name = $name;

		// When the server connects or reconnects to the server
		// It will receive all of the client List.
		if($name === "adminPanel"){
			$this->getClientList();
		}

		$this->send([
			'action'   => 'setname',
			'success'  => true,
			'username' => $this->name
		]);
	}

	// Server - Send disableCommand
	public function addVisitor($amount, $datetime){
		$this->send([
			'action'    => 'add',
			'amount'    => $amount,
			'datetime'  => $datetime
		]);
	}

	public function getClientList(){
		// Send the Feedback into the clients
		$this->send([
			'action'   => 'clientList',
			'clients'  => json_encode($this->repository->getClientNames())
		]);
	}

	public function getName(){
		return $this->name;
	}

	private function send(array $data){
		$this->connection->send(json_encode($data));
	}
}