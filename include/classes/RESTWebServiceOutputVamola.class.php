<?php
/************************************************************************/
/* AChecker                                                             */
/************************************************************************/
/* Copyright (c) 2008 by Greg Gay, Cindy Li                             */
/* Adaptive Technology Resource Centre / University of Toronto			    */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* RESTWebServiceOutput
* Class to generate error report in REST format 
* @access	public
* @author	Cindy Qi Li
* @package checker
*/
if (!defined("AC_INCLUDE_PATH")) die("Error: AC_INCLUDE_PATH is not defined.");

include_once(AC_INCLUDE_PATH.'classes/DAO/ChecksDAO.class.php');
include_once(AC_INCLUDE_PATH.'classes/DAO/UserLinksDAO.class.php');
include_once(AC_INCLUDE_PATH.'classes/DAO/GuidelinesDAO.class.php');
include_once(AC_INCLUDE_PATH.'classes/DAO/UserDecisionsDAO.class.php');

class RESTWebServiceOutputVamola {

	// all private
	var $errors;                    // parameter. array of errors
	var $userLinkID;                // parameter. user_links.user_link_id
	var $guidelineArray;            // parameter. array of guidelines
	
	var $output;                    // final web service output
	
	// REST templates
	var $rest_main =
'<summary>
	<status>{STATUS}<stauts>
	<sessionID>{SESSIONID}<sessionID>
	<NumOfErrors>{NUMOFERRORS}</NumOfErrors>
	<NumOfLikelyProblems>{NUMOFLIKELYPROBLEMS}</NumOfLikelyProblems>
	<NumOfPotentialProblems>{NUMOFPOTENTIALPROBLEMS}</NumOfPotentialProblems>

	<guidelines>
{GUIDELINES}
	</guidelines>
</summary>

<checks>
{RESULTS}
</checks>
';
	
	var $rest_guideline =
'		<guideline>{GUIDELINE}</guideline>
';
	
	var $rest_result = 
'	<check>
		<type>{RESULTTYPE}</type>
		<checkId>{CHECKID}</checkId>
		<result>{RESULT}</result>
		<lineNum>{LINENUM}</lineNum>
		<columnNum>{COLUMNNUM}</columnNum>
		<errorMsg>&lt;a href=&quot;{BASE_HREF}checker/suggestion.php?id={CHECK_ID}&quot;
               onclick=&quot;popup(\'{BASE_HREF}checker/suggestion.php?id={CHECK_ID}\'); return false;&quot; 
               title=&quot;{TITLE}&quot; target=&quot;_new&quot;&gt;{ERRORMSG}</a>
        </errorMsg>
		<errorSourceCode>{ERRORSOURCECODE}</errorSourceCode>
		{REPAIR}
		{DECISION}
	</check> 
';
	
	var $rest_repair = '<repair>{REPAIR}</repair>';
	
	var $rest_decision_questions =
'<sequenceID>{SEQUENCEID}</sequenceID>
        <decisionPass>{DECISIONPASS}</decisionPass>
		<decisionFail>{DECISIONFAIL}</decisionFail>
';
	
	var $rest_decision_made =
'		<decisionMade>{DECISIONMADE}</decisionMade>
		<decisionMadeDate>{DECISIONMADEDATE}</decisionMadeDate>
';



//MB xml per il monitor

		//versione 1
		/* 
		var $rest_vamola_type=
		"\n<type id=\"{TYPE}\">
			<success>{SUCCESS}</success>
			<fail>{FAIL}</fail>";
		var $rest_vamola_type_close= 
		"\n</type>";
		var $rest_vamola_req=
		"\n		<req id=\"{REQ}\">
				<success>{SUCCESS}</success>
				<fail>{FAIL}</fail>";
		var $rest_vamola_req_close= 
		"\n		</req>";
		var $rest_vamola_gruppo=
		 "\n			<gruppo id=\"{GRUPPO}\">
						<success>{SUCCESS}</success>
						<fail>{FAIL}</fail>";
		var $rest_vamola_gruppo_close=
		 "\n			</gruppo>";
		
		
		var $rest_vamola_check=
		 "\n				<check id=\"{CHECK}\">
							<success>{SUCCESS}</success>
							<fail>{FAIL}</fail>
		  \n				</check>";
	    */
		
		//versione 2
		
		var $header="<summary> <url>{URL}</url> <date>{DATE}<date> </summary>";
		
		var $rest_vamola_type=
		"\n<type id=\"{TYPE}\" success=\"{SUCCESS}\" fail=\"{FAIL}\">";
		var $rest_vamola_type_close= 
		"\n</type>";
		
		var $rest_vamola_req=
		"\n		<req id=\"{REQ}\" success=\"{SUCCESS}\" fail=\"{FAIL}\">";
		var $rest_vamola_req_close= 
		"\n		</req>";
		
		var $rest_vamola_gruppo=
		 "\n			<group id=\"{GRUPPO}\" success=\"{SUCCESS}\" fail=\"{FAIL}\">";
		 
		var $rest_vamola_gruppo_close=
		 "\n			</group>";
		
		
		var $rest_vamola_check=
		 "\n				<check id=\"{CHECK}\" success=\"{SUCCESS}\" fail=\"{FAIL}\" ></check>";
		
		//versione 3 
		/*
		var $rest_vamola_type=
		"\n<type> <id>{TYPE}</id> <success>{SUCCESS}</success> <fail>{FAIL}</fail>";
		var $rest_vamola_type_close= 
		"\n</type>";
		
		var $rest_vamola_req=
		"\n		<req><id>{REQ}</id> <success>{SUCCESS}</success> <fail>{FAIL}</fail>";
		var $rest_vamola_req_close= 
		"\n		</req>";
		
		var $rest_vamola_gruppo=
		 "\n			<gruppo><id>{GRUPPO}</id> <success>{SUCCESS}</success> <fail>{FAIL}</fail>";
		 
		var $rest_vamola_gruppo_close=
		 "\n			</gruppo>";
		
		
		var $rest_vamola_check=
		 "\n				<check><id>{CHECK}</id> <success>{SUCCESS}</success> <fail>{FAIL}</fail></check>";
		*/
		 

	
	/**
	* public
	* $errors: an array, output of AccessibilityValidator -> getValidationErrorRpt
	* $user_link_id: user link id
	* $guideline_array: an array of guideline ids
	*/
	function RESTWebServiceOutputVamola($errors, $userLinkID, $guidelineArray)
	{
//		debug($errors);exit;
		$this->errors = $errors;
		$this->userLinkID = $userLinkID;
		$this->guidelineArray = $guidelineArray;
		
		$this->generateRESTRptVamola();
	}
	
	
	private function getIdReq($id)
	{
		if($id >=995 && $id <=1059 || $id >=1069 && $id <=1070)
			return 1;
		else if($id >=1060 && $id <=1068 || $id >=1071 && $id <=1072)
			return 2;
		else if($id >=3000 && $id <=3019)
			return 3;
		else if($id ==4000)
			return 4;
		else if($id >=5000 && $id <=5028)
			return 5;
		else if($id >=6000 && $id <=6011)
			return 6;
		else if($id==7000)
			return 7;
		else if($id==1073)
			return 11;
		else if($id >=12000 && $id <=12032)
			return 12;
		else if($id >=14000 && $id <=14005)
			return 14;
		else if($id >=18000 && $id <=18001)
			return 18;	
		else if($id >=21000 && $id <=21023)
			return 21;
		else
			return '';		
		
		
	}
	
	private function getIdGroup($id)
	{
		if($id >=1005 && $id <=1014)
			return "1.a";
		else if($id >=1015 && $id <=1042)
			return "1.b";
		else if($id ==1042)
			return "1.c";
		else if($id ==1043)
			return "1.d";	
		else if($id >=1044 && $id <=1047)
			return "1.e";
		else if($id >=995 && $id <=1004)
			return "1.f";	
		else if($id >=1048 && $id <=1054)
			return "1.g";
		else if($id >=1055 && $id <=1058)
			return "1.h";
		else if($id ==1059)
			return "1.i";
		else if($id ==1069)
			return "1.l";
		else if($id ==1070)
			return "1.m";

		
		else if($id >=1060 && $id <=1063 || $id >=1067 && $id <=1068)
			return "2.a";
		else if($id >=1064 && $id <=1065)
			return "2.b";
		else if($id ==1066)
			return "2.c";
		else if($id >=1071 && $id <=1072)
			return "2.d";

			
		else if($id >=3000 && $id <=3016)
			return "3.a";
		else if($id >=3017 && $id <=3019)
			return "3.b";		
		
		else if($id ==4000)
			return "4.a";
			
		else if($id >=5000 && $id <=5025)
			return "5.a";
		else if($id ==5026)
			return "5.b";
		else if($id >=5027 && $id <=5028)
			return "5.c";
			
		else if($id >=6000 && $id <=6001 || $id >=6004 && $id <=6011)
			return "6.a";
		else if($id >=6002 && $id <=6003)
			return "6.b";
			
		else if($id ==7000)
			return "7.a";
			
		else if($id ==1073)
			return "11.a";
			
		else if($id >=12000 && $id <=12031)
			return "12.a";
		else if($id ==12032)
			return "12.b";	
			
		else if($id >=14000 && $id <=14005)
			return "14.a";
			
		else if($id >=18000 && $id <=18001)
			return "18.a";

			
		else if($id >=21000 && $id <=21007)
			return "21.a";
		else if($id >=21008 && $id <=21013)
			return "21.b";
		else if($id >=21014 && $id <=21023)
			return "21.c";
			
		else 
			return '';	
	}	
	
	
	
	private function generateRESTRptVamola()
	{

		
		$checksDAO = new ChecksDAO();
		$userDecisionsDAO = new UserDecisionsDAO();
		
		
		//MB aggrego i dati per l'output rest
		foreach ($this->errors as $error)
		{
			
			$check_id=$error['check_id'];
			
			$row_check = $checksDAO->getCheckByID($check_id);
			
			$type=$row_check['confidence'];

			if(!isset($rest_result[$type]))
			{
				
				$rest_result[$type]['success']=0;
				$rest_result[$type]['fail']=0;
			}

			
			if(!isset($rest_result[$type]['requisiti'][$this->getIdReq($check_id)]))
			{
				
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['success']=0;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['fail']=0;
			}
			
			
			if(!isset($rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]))
			{
				
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['success']=0;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['fail']=0;
			}
			
			
			
			if(!isset($rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['checks'][$error['check_id']]))
			{
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['checks'][$error['check_id']]['success']=0;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['checks'][$error['check_id']]['fail']=0;
			}
					
			
			if($error['result']=="success")
			{
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['checks'][$error['check_id']]['success']++;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['success']++;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['success']++;
				$rest_result[$type]['success']++;		
			}
			else if($error['result']=="fail")
			{
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['checks'][$error['check_id']]['fail']++;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['gruppi'][$this->getIdGroup($check_id)]['fail']++;
				$rest_result[$type]['requisiti'][$this->getIdReq($check_id)]['fail']++;
				$rest_result[$type]['fail']++;	
			}
			
		}
		
		//creo il file xml		
		
		//asort($rest_result);
		foreach($rest_result as $nome_tipo=>$tipo)
		{
				/*
				echo("<p>type=".$nome."</p>");
				echo("<p>succes=".$stuff['success']."</p>");
				echo("<p>fail=".$stuff['fail']."</p>");
				*/
				
				$result .= str_replace(array('{TYPE}', 
										 '{SUCCESS}',
										 '{FAIL}'),
			                      array($nome_tipo,
			                      		$tipo['success'] ,
			                      		$tipo['fail']),
			                      $this->rest_vamola_type);
				
				//asort($stuff["requisiti"]);
				foreach($tipo["requisiti"] as $nome_req=>$req)
				{
					/*
					echo("<p>req=".$nome2."</p>");
					echo("<p>success=".$stuff2['success']."</p>");
					echo("<p>fail=".$stuff2['fail']."</p>");
					*/
					$result .= str_replace(array('{REQ}', 
										 '{SUCCESS}',
										 '{FAIL}'),
			                      array($nome_req,
			                      		$req['success'] ,
			                      		$req['fail']),
			                      $this->rest_vamola_req);

			       //foreach per i gruppi
			       //asort($stuff2["gruppi"]);
			       foreach($req['gruppi'] as $nome_gruppo=>$gruppo)
			       {
			       	$result .= str_replace(array('{GRUPPO}', 
										 '{SUCCESS}',
										 '{FAIL}'),
			                      array($nome_gruppo,
			                      		$gruppo['success'] ,
			                      		$gruppo['fail']),
			                      $this->rest_vamola_gruppo);
					 //asort($stuff3["checks"]);
			         foreach($gruppo['checks'] as $nome_check=>$check)
			         {
			         	$result .= str_replace(array('{CHECK}', 
										 '{SUCCESS}',
										 '{FAIL}'),
			                      array($nome_check,
			                      		$check['success'] ,
			                      		$check['fail']),
			                      $this->rest_vamola_check);
			         }
			                                
			        $result .= $this->rest_vamola_gruppo_close;              
			                      
			       }
			       
			       
			       $result .=$this->rest_vamola_req_close;                         
				}
				
				$result .=$this->rest_vamola_type_close;
		}		

	//metto l'intestazione	
	$result=str_replace(array("{URL}", "{DATE}"), 
			                             array($_GET['uri'], date("Y-m-d")), 
			                             $this->header) . $result;
	$this->output=$result;	
		
	}
	
	
	/**
	* private
	* main process to generate report in html format
	*/
	private function generateRESTRpt()
	{
		$num_of_errors = 0;
		$num_of_likely_problems = 0;
		$num_of_potential_problems = 0;
		
		//MB
		$num_errori_da_correggere = 0;
		$num_controlli_manuali = 0;
		$num_errori_potenziali_relativi_ai_requisiti = 0;
		$num_errori_potenziali_generali = 0;
		//~MB
		
		$checksDAO = new ChecksDAO();
		$userDecisionsDAO = new UserDecisionsDAO();
		
		
		
		
		// generate section details
		foreach ($this->errors as $error)
		{ // generate each error result
			$result_type = '';
			$repair = '';
			$decision = '';
			$decision_questions = '';
			$decision_made = '';
			
			$row_check = $checksDAO->getCheckByID($error["check_id"]);

			if ($row_check["confidence"] == KNOWN)
			{ // only known errors have <repair> 
				$num_of_errors++;
				$result_type = _AC('error');
				
				$repair = str_replace('{REPAIR}', 
				                      htmlspecialchars(_AC($row_check["how_to_repair"]), ENT_QUOTES), 
				                      $this->rest_repair);
			}
			else 
			{
				// generate user's decision. only likely and potential problems have decisions to make
				$row_userDecision = $userDecisionsDAO->getByUserLinkIDAndLineNumAndColNumAndCheckID($this->userLinkID, $error["line_number"], $error["col_number"], $error['check_id']);
				
				if ($row_userDecision['decision'] == AC_DECISION_PASS || $row_userDecision['decision'] == AC_DECISION_FAIL)
				{
					if ($row_userDecision['decision'] == AC_DECISION_PASS) $decision_text = _AC('pass');
					if ($row_userDecision['decision'] == AC_DECISION_FAIL) $decision_text = _AC('fail');
					
					$decision_made = str_replace(array('{DECISIONMADE}', 
					                                   '{DECISIONMADEDATE}'),
					                             array($decision_text, 
					                                   $row_userDecision['last_update']),
					                             $this->rest_decision_made);
				}
			
				if ($row_check["confidence"] == LIKELY)
				{
					$result_type = _AC('likely_problem');
					
					if ($row_userDecision['decision'] == AC_NO_DECISION || $row_userDecision['decision'] == AC_DECISION_FAIL) 
						$num_of_likely_problems++;
					
				}

				if ($row_check["confidence"] == POTENTIAL)
				{
					$result_type = _AC('potential_problem');
					
					if ($row_userDecision['decision'] == AC_NO_DECISION || $row_userDecision['decision'] == AC_DECISION_FAIL)
						$num_of_potential_problems++;
					
				}
				
				$decision_questions = str_replace(array('{SEQUENCEID}', '{DECISIONPASS}', '{DECISIONFAIL}'),
				                                  array($row_userDecision['sequence_id'], _AC($row_check['decision_pass']), _AC($row_check['decision_fail'])),
				                                  $this->rest_decision_questions);
				                                  
				$decision = $decision_questions . $decision_made;
				// end of generating user's decision
			}
			
			//MB
			if ($row_check["confidence"] == "10")
			{ // only known errors have <repair> 
				$num_errori_da_correggere++;
		
				//$result_type = _AC('error');
				$result_type = "Errore da Correggere";
				$repair = str_replace('{REPAIR}', 
				                      htmlspecialchars(_AC($row_check["how_to_repair"]), ENT_QUOTES), 
				                      $this->rest_repair);
			}
			elseif ($row_check["confidence"] == "11")
			{ // only known errors have <repair> 
				$num_controlli_manuali++;
		
				//$result_type = _AC('error');
				$result_type = "Controllo Manuale";
				$repair = str_replace('{REPAIR}', 
				                      htmlspecialchars(_AC($row_check["how_to_repair"]), ENT_QUOTES), 
				                      $this->rest_repair);
			}
			elseif ($row_check["confidence"] == "12")
			{ // only known errors have <repair> 
				$num_errori_potenziali_relativi_ai_requisiti++;
		
				//$result_type = _AC('error');
				$result_type = "Errore Potenziale relativo ai requisiti";
				$repair = str_replace('{REPAIR}', 
				                      htmlspecialchars(_AC($row_check["how_to_repair"]), ENT_QUOTES), 
				                      $this->rest_repair);
			}
			elseif ($row_check["confidence"] == "13")
			{ // only known errors have <repair> 
				$num_errori_potenziali_generali++;
				//$result_type = _AC('error');
				$result_type = "Errore Potenziale Generale";
				$repair = str_replace('{REPAIR}', 
				                      htmlspecialchars(_AC($row_check["how_to_repair"]), ENT_QUOTES), 
				                      $this->rest_repair);
			}
			//~MB
			
			$result .= str_replace(array('{RESULTTYPE}', 
										 '{CHECKID}',
										 '{RESULT}',
			                             '{LINENUM}', 
			                             '{COLUMNNUM}', 
			                             '{BASE_HREF}', 
			                             '{CHECK_ID}', 
			                             '{TITLE}',
			                             '{ERRORMSG}',
			                             '{ERRORSOURCECODE}', 
			                             '{REPAIR}', 
			                             '{DECISION}'),
			                      array($result_type,
			                      		$error["check_id"] ,
			                      		$error["result"] ,
			                            $error["line_number"], 
			                            $error["col_number"], 
			                            AC_BASE_HREF, 
			                            $error['check_id'], 
			                            _AC("suggest_improvements"),
			                            htmlspecialchars(_AC($row_check['err']), ENT_QUOTES),
			                            htmlspecialchars($error["html_code"], ENT_QUOTES),
			                            $repair,
			                            $decision),
			                      $this->rest_result);
		}
		
		// retrieve session id
		$userLinksDAO = new UserLinksDAO();
		$row = $userLinksDAO->getByUserLinkID($this->userLinkID);
		$sessionID = $row['last_sessionID'];
		
		// generate guidelines
		$guidelinesDAO = new GuidelinesDAO();
		
		foreach ($this->guidelineArray as $gid)
		{
			$row_guideline = $guidelinesDAO->getGuidelineByIDs($gid);
			$guidelines .= str_replace('{GUIDELINE}', $row_guideline[0]['title'], $this->rest_guideline);
		}
		
		// find out result status: pass, fail, conditional pass
		//MB
		if ($num_of_errors > 0 || $num_errori_da_correggere > 0)
		{
			$status = _AC('fail');
		}
		else if ($num_of_likely_problems + $num_of_potential_problems > 0 || $num_controlli_manuali + $num_errori_potenziali_generali + $num_errori_potenziali_relativi_ai_requisiti > 0 )
		{
			$status = _AC('conditional_pass');
		}
		else
		{
			$status = _AC('pass');
		}
		
		// generate final output
		$this->output = str_replace(array('{STATUS}', 
		                                  '{SESSIONID}', 
				                          '{NUMOFERRORS}', 
		                                  '{NUMOFLIKELYPROBLEMS}', 
		                                  '{NUMOFPOTENTIALPROBLEMS}', 
		                                  '{GUIDELINES}',
		                                  '{RESULTS}'),
		                            array($status,
		                                  $sessionID,
		                                  $num_of_errors,
		                                  $num_of_likely_problems,
		                                  $num_of_potential_problems,
		                                  $guidelines,
		                                  $result), 
		                            $this->rest_main);
	}
	
	/** 
	* public
	* return final web service output
	* parameters: none
	* author: Cindy Qi Li
	*/
	public function getWebServiceOutput()
	{
		return $this->output;
	}
	
	/** 
	* public
	* return error report in html
	* parameters: $errors: errors array
	* author: Cindy Qi Li
	*/
	public static function generateErrorRpt($errors)
	{
		// initialize error codes. Note that all errors reported in REST need to be defined here.
		$errorCodes['AC_ERROR_EMPTY_URI'] = 401;
		$errorCodes['AC_ERROR_INVALID_URI'] = 402;
		$errorCodes['AC_ERROR_EMPTY_WEB_SERVICE_ID'] = 403;
		$errorCodes['AC_ERROR_INVALID_WEB_SERVICE_ID'] = 404;
		$errorCodes['AC_ERROR_SEQUENCEID_NOT_GIVEN'] = 405;
		
		// error template in REST format
		$rest_error = 
'<errors>
	<totalCount>{TOTOAL_COUNT}</totalCount>
{ERROR_DETAIL}
</errors>
';
	
		$rest_error_detail = 
'	<error code="{ERROR_CODE}">
		<message>{MESSAGE}</message>
	</error>
';
		if (!is_array($errors)) return false;
		
		foreach ($errors as $err)
		{
			$error_detail .= str_replace(array("{ERROR_CODE}", "{MESSAGE}"), 
			                             array($errorCodes[$err], _AC($err)), 
			                             $rest_error_detail); 
		}
			                            
		return str_replace(array('{TOTOAL_COUNT}', '{ERROR_DETAIL}'), 
		                   array(count($errors), $error_detail),
		                   $rest_error);
	}

	/** 
	* public
	* return success in REST
	* parameters: none
	* author: Cindy Qi Li
	*/
	public static function generateSuccessRpt()
	{
		$rest_success = 
'<summary>
	<status>success</status>
</summary>
';
		
		return $rest_success;
	}
}
?>  
