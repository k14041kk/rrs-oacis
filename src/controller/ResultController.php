<?php
namespace adf\controller;

use adf\controller\AbstractController;
use adf\model\ResultGeneration;
use adf\model\ResultTeam;
use adf\model\ResultHelper;
use adf\model\Result2016;

class ResultController extends AbstractController{
  
	public function anyIndex($param= null){
		self::get($param);
	}
	
	public function get($param = null){
		
		//TODO Access the oasis and bring the results
		$simulatorID = $param;
		
		
		//include (Config::$SRC_REAL_URL . 'view/SettingView.php');
		$teams =[];
		
		$maps = Result2016::getMaps();
		
		if($simulatorID=='test'){
			
			//Test
			$teams = Result2016::getTeams();
			
			$key_id = [];
			
			$teams2 = $teams;
			foreach ($teams2 as $key => $value){
				$key_id[$key] = $value->getTotalScore();
			}
			array_multisort ( $key_id , SORT_DESC, $teams2);
			
			$rank = 1;
			foreach ($teams2 as $key => $value){
				
				$teams[$key]->setRank($rank);
				//$value->setRank($rank);
				$rank++;
			}
			
		}else{
			
			$parameterSets= ResultHelper::getParameterSets($simulatorID);
			
			$maps = ResultHelper::getMaps($parameterSets);
			
			$teams = ResultHelper::getTeams($simulatorID, $parameterSets);
			
		}
		
		ResultHelper::calPoints($teams);
		
		echo ResultGeneration::generateHTML('2018', $maps, $teams, false);
  	
  }
  
}