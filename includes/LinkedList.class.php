<?php

class LinkedList {
	private $firstElement = NULL;
	private $lastElement = NULL;

	public function __construct($collection = array()){
		if($collection == NULL)
			return;

		if(!empty($collection)){
			foreach($collection as $value)
				$this->add($value);
		}
	}

	public function add($value){
		$next = new Element($value);

		if($this->getFirst() == NULL){
			$this->firstElement = $next;
		} else {
			$last = $this->getLast();
			
			$last->setNext($next);
			$next->setPrevious($last);

			$this->lastElement = $next;
		}
	}

	public function remove($toRemove){
		$next = $this->getFirst();
		while($next->hasNext()){
			if($next->getValue() == $toRemove->getValue()){
				$ePrevious = $next->getPrevious();
				$eNext = $next->getNext();

				if($eNext == NULL && $ePrevious == NULL){
					$this->firstElement = NULL;
					$this->lastElement = NULL;
				}

				if($ePrevious == null)
					$this->firstElement = $eNext;


				if($eNext == NULL)
					$this->lastElement = $ePrevious;

				$ePrevious->setNext($eNext);
				$eNext->setPrevious($ePrevious);

				return true;
			}
		}

		return false;
	}

	public function getFirst(){
		return $this->firstElement;
	}

	public function getLast(){
		return $this->lastElement;
	}
}

class Element {
	private $next;
	private $previous;
	private $value;

	public function __construct($value){
		$this->value = $value;
	}

	public function setNext($next){
		$this->next = $next;
	}

	public function setPrevious($previous){
		$this->previous = $previous;
	}

	public function getValue(){
		return $this->value;
	}

	public function getNext(){
		return $this->next;
	}

	public function getPrevious(){
		return $this->previous;
	}

	public function hasNext(){
		return $next != NULL;
	}
}