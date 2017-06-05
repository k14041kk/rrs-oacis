<?php
namespace adf\model;

use JsonSerializable;

/**
 * Team's results managing 
 * */
class ResultTeam implements JsonSerializable{
	
	private $teamName;
	
	private $maps;
	
	private $oldDay;
	
	private $rank;
	
	private $logLink;
	
	private $color;
	
	public function __construct(String $teamName)
	{
		
		$this->teamName = $teamName;
		$this->maps = [];
		
	}
	
	public function getMaps(){
		return $this->maps;
	}
	
	public function addMapResult($mapName, $score, $points){
		
		$data = [];
		$data['score'] = floatval($score);
		$data['points'] = $points;
		
		$this->maps[$mapName] = $data;
		
	}
	
	public function setPoint($mapName, $points){
		$this->maps[$mapName]['points'] = $points;
	}
	
	public function getScore($mapName){
		return $this->maps[$mapName];
	}
	
	public function getTotalScore(){
	
		$result = [];
		$result['score'] = 0;
		$result['points'] = 0;
		
		foreach($this->maps as $name => $value){
			
			$result['score'] += $value['score'];
			$result['points'] += $value['points'];
			
		}
		
		if($oldDay!=null){
			$result['score'] += $oldDay['score'];
			$result['points'] += $oldDay['points'];
		}
		
		return $result;
		
	}
	
	public function getTeamName(){
		return $this->teamName;
	}
	
	public function setRank($rank){
		$this->rank = $rank;
	}
	
	public function getRank(){
		return $this->rank;
	}
	
	public function setLogLink($link){
		$this->logLink = $link;
	}
	
	public function getLogLink(){
		return $this->logLink;
	}
	
	/***
	 * Set Background Color
	 * @param int $color 0, 1, 2, 3 : white, blue, silver, gold
	 */
	public function setColorType($color){
		$this->color = $color;
	}
	
	public function getColorType(){
		return $this->color;
	}
	
	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
	
}

