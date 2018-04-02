<?php


use Ratchet\ConnectionInterface;

interface RatchetChatRepositoryInterface {

	public function getClientByName($name);

	public function getClientByConnection(ConnectionInterface $conn);

	public function addClient(ConnectionInterface $conn);

	public function removeClient(ConnectionInterface $conn);

	public function getClients();

	public function getClientNames();
}
