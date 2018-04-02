<?php

interface RatchetChatConnectionInterface {

	public function getConnection();

	public function getName();

	public function setName($name);

	public function addVisitor($amount, $datetime);


}
