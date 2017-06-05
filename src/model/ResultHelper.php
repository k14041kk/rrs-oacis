<?php
namespace adf\model;

use \adf\model\ResultTeam;

class ResultHelper{
	
	public static function getParameterSets(String $simulatorID){
		
		$rawData = file_get_contents('http://0.0.0.0:3000/simulators/'.$simulatorID . '.json');
		
		$json = json_decode($rawData);
		
		return $json->parameter_sets;
		
	}
	
	public static function getMaps(array $parameterSets){
		
		
		$maps = [];
		
		for ($i = 0 ; $i < count($parameterSets); $i++) {
			
			$m = explode("_", $parameterSets[$i]->v->MAP)[0];
			
			$maps[] = $m;
			
		}
		
		
		return array_merge(array_unique($maps));
		
	}
	
	public static function getTeams(String $simulatorID ,array $parameterSets){
		
		$teams = [];
		
		for ($i = 0 ; $i < count($parameterSets); $i++) {
			
			$m = explode("_", $parameterSets[$i]->v->MAP)[0];
			
			$teamName = explode("_", $parameterSets[$i]->v->A)[0];
			
			$team;
			
			if(! isset($teams[$teamName])){
				$teams[$teamName] = new ResultTeam($teamName);
			}
			
			$team = $teams[$teamName];
			
			//Score
			$parameterID = $parameterSets[$i]->id;
			$runs = self::getRuns($parameterID);
			
			//Fast 1
			$runID = $runs[0]->id;
			
			$run = self::getRun($runID);
			
			$score = self::get_stdout_txt($simulatorID, $parameterID, $runID);
			
			$team->addMapResult($m, $score, 100);
			
			
		}
		
		$key_id = [];
		
		foreach ($teams as $key => $value){
			$key_id[$key] = $value->getTotalScore();
		}
		array_multisort ( $key_id , SORT_DESC, $teams);
		
		$rank = 1;
		foreach ($teams as $key => $value){
			$value->setRank($rank);
			$rank++;
		}
		
		return $teams;
		
	}
	
	public static function getRuns(String $parameterSetID){
		
		$rawData = file_get_contents('http://0.0.0.0:3000/parameter_sets/'.$parameterSetID. '.json');
		
		$json = json_decode($rawData);
		
		return $json->runs;
		
	}
	
	public static function getRun(String $runID){
		
		$rawData = file_get_contents('http://0.0.0.0:3000/runs/'.$runID. '.json');
		
		$json = json_decode($rawData);
		
		return $json->runs;
		
	}
	
	public static function get_stdout_txt($simulatorID, $parameterSetID, $runID){
		
		return rand(50,70);
		
		$rawData = 
		file_get_contents('http://0.0.0.0:3000/Result_development/'.$simulatorID.'/'.$parameterSetID.'/'.$runID.'/_stdout.txt');
	
		//get rescue score
		$rawData2 =
		@file_get_contents('http://0.0.0.0:3000/Result_development/'.$simulatorID.'/'.$parameterSetID.'/'.$runID.'/score.txt');
		
		if($rawData2!=null)$rawData = $rawData2;
		
		return $rawData;
		
	}
	
	public static function calPoints(array $teams){
		
		//TODO 
		
		$SMkj = [];
		
		$maps = [];
		
		$MAX = [];
		
		$AVERAGE = [];
		
		$STEP_SIZE = 0;
		
		$MSS= [];
		
		$MAP_Scores = [];
		
		foreach ($teams as $key => $value){
			$maps = $value->getMaps();
			$STEP_SIZE++;
		}
		
		$SDC = $STEP_SIZE;
		
		$STEP_SIZE *= 2;
		
		//$SDC = 4;
		//(2)
		$MSkj = $SDC * 2;
		
		foreach ($maps as $key => $value){
			
			foreach ($teams as $key1 => $value1){
				
				$score = $value1->getScore($key);
				
				if($MAX[$key]<$score['score']){
					$MAX[$key] = $score['score'];
				}
				
				if(!isset($MAP_Scores[$key])){
					$MAP_Scores[$key] = [];
				}
				
				$MAP_Scores[$key][] = $score['score'];
				
			}
			
			$AVERAGE[$key]= array_sum($MAP_Scores[$key]) / count($MAP_Scores[$key]);
		
			//(1)
			$SMkj[$key] = $MAX[$key] - (($MAX[$key]-$AVERAGE[$key])*2 );
			
			$STEP[$key] = [];
			
			$MSS[$key] = [];
			
			for($i=1; $i<=$STEP_SIZE;$i++){
				
				//(3)
				$zore = ($MSkj*($MSkj-$i));
				if($zore==0)$zore=1;
				
				$mss = ($MAX[$key]-$SMkj[$key])/$zore;
				
				$MSS[$key][] = $mss * 100;
				
				//var_dump($SMkj[$key]);
				
				
			}
			
			rsort($STEP[$key]);
			
			foreach ($teams as $key1 => $value1){
				
				$point = 0;
				
				$tScore = $value1->getScore($key)['score'];
				
				for($i=1; $i<=$STEP_SIZE;$i++){
					
					if($i==$STEP_SIZE && $point==0){
						//(5)
						$point = $STEP_SIZE; 
					}else if($MSS[$key][$i] < $tScore && $tScore < $MSS[$key][$i+1]){
						//(4)
						$point = $i;
					}
					
					
					
				}
				
				echo $key . ' ' .$key1. " ! <br>";
				
				for($i=1; $i<=$STEP_SIZE;$i++){
					echo $MSS[$key][$i] . " | ";
				}
				
				//var_dump($MSS[$key]);
				echo '<br> <br>';
				
				$point = $STEP[$key][$value1->getRank()-1];
				
				$point = round($point,2);
				
				//echo $key . " : " . $point;
				
				//var_dump($STEP[$key]);
				
				$value1->setPoint($key,$point);
				
			}
			
		}
		
		
				
		
	}
	
	
}