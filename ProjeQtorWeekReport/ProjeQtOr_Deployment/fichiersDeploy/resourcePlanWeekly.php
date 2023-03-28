<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2017 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 * 
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ***********************************************
 *
 *MSG from Esteban AVILA: 
 *
 *  This php file has been developped by Esteban AVILA and uses many lines of code
 *  from the 'projeqtor/report/resourcePlan.php' file from V9.0.6 of ProjeQtOr.
 *
 */

include_once '../tool/projeqtor.php';

$paramProject='';
if (array_key_exists('idProject',$_REQUEST)) {
  $paramProject=trim($_REQUEST['idProject']);
  $paramProject=Security::checkValidId($paramProject); // only allow digits
};
$paramYear='';
if (array_key_exists('yearSpinner',$_REQUEST)) {
	$paramYear=$_REQUEST['yearSpinner'];
	$paramYear=Security::checkValidYear($paramYear);
};
$idOrganization = trim(RequestHandler::getId('idOrganization'));
$paramTeam='';
if (array_key_exists('idTeam',$_REQUEST)) {
  $paramTeam=trim($_REQUEST['idTeam']);
  Security::checkValidId($paramTeam);
}

$paramMonth='';
if (array_key_exists('monthSpinner',$_REQUEST)) {
	$paramMonth=$_REQUEST['monthSpinner'];
  $paramMonth=Security::checkValidMonth($paramMonth);
};

$paramWeek='';
if (array_key_exists('weekSpinner',$_REQUEST)) {
	$paramWeek=$_REQUEST['weekSpinner'];
	$paramWeek=Security::checkValidWeek($paramWeek);
};

$user=getSessionUser();

$periodType=$_REQUEST['periodType']; // not filtering as data as data is only compared against fixed strings
$periodValue='';
if (array_key_exists('periodValue',$_REQUEST))
{
	$periodValue=$_REQUEST['periodValue'];
	$periodValue=Security::checkValidPeriod($periodValue);
}

// Header
$headerParameters="";
if ($paramProject!="") {
  $headerParameters.= i18n("colIdProject") . ' : ' . htmlEncode(SqlList::getNameFromId('Project', $paramProject)) . '<br/>';
}
if ($idOrganization!="") {
  $headerParameters.= i18n("colIdOrganization") . ' : ' . htmlEncode(SqlList::getNameFromId('Organization',$idOrganization)) . '<br/>';
}
if ($paramTeam!="") {
  $headerParameters.= i18n("colIdTeam") . ' : ' . htmlEncode(SqlList::getNameFromId('Team', $paramTeam)) . '<br/>';
}
if ($periodType=='paramYear' or $periodType=='month' or $periodType=='week') {
  $headerParameters.= i18n("year") . ' : ' . $paramYear . '<br/>';
  
}
if ($periodType=='month') {
  $headerParameters.= i18n("month") . ' : ' . $paramMonth . '<br/>';
}
if ( $periodType=='week') {
  $headerParameters.= i18n("week") . ' : ' . $paramWeek . '<br/>';
}

$nbMonths=1;
if ($periodType=='month' and isset($_REQUEST['includeNextMonth']) and !isset($_REQUEST['includeThreeMonth']) and !isset($_REQUEST['includeSixMonth'])) {

  $nbMonths=2;
  $headerParameters.= i18n("colIncludeNextMonth").'<br/>';

}elseif ($periodType=='month' and isset($_REQUEST['includeThreeMonth']) and !isset($_REQUEST['includeSixMonth'])) {

  $nbMonths=3;
  $headerParameters.= i18n("colIncludeThreeMonth").'<br/>';

}elseif ($periodType=='month' and isset($_REQUEST['includeSixMonth'])) {

  $nbMonths=6;
  $headerParameters.= i18n("colIncludeSixMonth").'<br/>';

}

include "header.php";

$initParamMonth=$paramMonth;

$monthsScope = $nbMonths + 2; // Quantity of months we look at in the DB


//ONE TIME LOOP
for ($cptMonth=0;$cptMonth<1;$cptMonth++) {

  if ($periodType=='month') {
    $paramMonth=intval($initParamMonth)+$cptMonth;
    if ($paramMonth>12) {$paramYear+=1;$paramMonth=1;}
    if ($paramMonth<10) $paramMonth='0'.$paramMonth;
    $periodValue=$paramYear.$paramMonth;
  }

$periodValue = HeaderCalc::calculFirstPeriodValue($periodType,$paramMonth,$initParamMonth,$cptMonth,$paramYear);
$periodValues = HeaderCalc::allPeriodValues($periodValue,$monthsScope);
$initPeriodDay = HeaderCalc::firstDayOfPeriod($initParamMonth,$paramYear);
$initPeriodDayScope = HeaderCalc::firstDayOfScopePeriod($initParamMonth,$paramYear);
$nbWeeks=HeaderCalc::weeksPerMonth($initParamMonth,$nbMonths,$paramYear);
$nbTotalWeeks=0;
$nbTotalDays=HeaderCalc::totalDays($nbWeeks,$paramMonth,$paramYear,$nbMonths);
$nbTotalDaysScope=HeaderCalc::totalDaysScope($paramMonth,$paramYear,$nbMonths);
$allDaysArray = HeaderCalc::writeAllDays($nbTotalDays,$initPeriodDay);
$allDaysScopeArray = HeaderCalc::writeAllDays($nbTotalDaysScope,$initPeriodDayScope);

////////// REQUEST where
$where=getAccesRestrictionClause('Activity',false,false,true,true);
$where='('.$where.' or idProject in '.Project::getAdminitrativeProjectList().')';
$where.=($periodType=='week')?" and week='" . $periodValue . "'":'';
if($periodType=='month'){
  if(isset($_REQUEST['includeSixMonth']) or isset($_REQUEST['includeThreeMonth']) or isset($_REQUEST['includeNextMonth'])){
    for($i=0;$i<=$nbMonths+1;$i++){// We got to look for information of 6 months + 2: one before and one after
      if($i==0){
        $where.=" and (month='" . $periodValues[$i] . "' ";
      }elseif($i==$nbMonths+1){
        $where.=" or month='".$periodValues[$i] . "' )";
      }else{
        $where.=" or month='".$periodValues[$i] . "' ";
      } 
    }
  }
}else{
  $where.='';
}
$where.=($periodType=='year')?" and year='" . $periodValue . "'":'';
if ($paramProject!='') {
  $where.=  "and idProject in " . getVisibleProjectsList(true, $paramProject) ;
}
//////////FIN REQ where

$order="";
$work=new Work();
$lstWork=$work->getSqlElementsFromCriteria(null,false, $where, $order);
$result=array();
$projects=array();
$projectsColor=array();
$resources=array();
$capacity=array();
$workDayResource=array();
$realDays=array();
foreach ($lstWork as $work) {
  if (! array_key_exists($work->idResource,$resources)) {
    $resources[$work->idResource]=SqlList::getNameFromId('Affectable', $work->idResource);
    $capacity[$work->idResource]=SqlList::getFieldFromId('Affectable', $work->idResource, 'capacity');
    $result[$work->idResource]=array();
    $realDays[$work->idResource]=array();
    $workDayResource[$work->idResource]=array();
  }
  if (! array_key_exists($work->idProject,$projects)) {
    $projects[$work->idProject]=SqlList::getNameFromId('Project', $work->idProject);
    $projectsColor[$work->idProject]=SqlList::getFieldFromId('Project', $work->idProject, 'color');
  }
  if (! array_key_exists($work->idProject,$result[$work->idResource])) {
    $result[$work->idResource][$work->idProject]=array();
  }
  if (! array_key_exists($work->idProject,$realDays[$work->idResource])) {
    $realDays[$work->idResource][$work->idProject]=array();
  }
  if (! array_key_exists($work->day,$result[$work->idResource][$work->idProject])) {
    $result[$work->idResource][$work->idProject][$work->day]=0;
    $realDays[$work->idResource][$work->idProject][$work->day]='real';
  } 
  if (! array_key_exists($work->day,$workDayResource[$work->idResource])) {
    $workDayResource[$work->idResource][$work->day]=0;
  }
  $result[$work->idResource][$work->idProject][$work->day]+=$work->work;
  $workDayResource[$work->idResource][$work->day]+=$work->work;
}

$planWork=new PlannedWork();
$lstPlanWork=$planWork->getSqlElementsFromCriteria(null,false, $where, $order);
$today=date('Ymd');
foreach ($lstPlanWork as $work) {
  if (! array_key_exists($work->idResource,$resources)) {
    $resources[$work->idResource]=SqlList::getNameFromId('Affectable', $work->idResource);
    $capacity[$work->idResource]=SqlList::getFieldFromId('Affectable', $work->idResource, 'capacity');
    $result[$work->idResource]=array();
    $realDays[$work->idResource]=array();
    $workDayResource[$work->idResource]=array();
  }
  if (! array_key_exists($work->idProject,$projects)) {
    $projects[$work->idProject]=SqlList::getNameFromId('Project', $work->idProject);
    $projectsColor[$work->idProject]=SqlList::getFieldFromId('Project', $work->idProject, 'color');
  }
  if (! array_key_exists($work->idProject,$result[$work->idResource])) {
    $result[$work->idResource][$work->idProject]=array();
  }
  if (! array_key_exists($work->idProject,$realDays[$work->idResource])) {
    $realDays[$work->idResource][$work->idProject]=array();
  }
  if (! array_key_exists($work->day,$result[$work->idResource][$work->idProject])) {
    $result[$work->idResource][$work->idProject][$work->day]=0;
  }
  if (! array_key_exists($work->day,$workDayResource[$work->idResource])) {
    $workDayResource[$work->idResource][$work->day]=0;
  }
  if (! array_key_exists($work->day,$realDays[$work->idResource][$work->idProject]) ) { // Do not add planned if real exists 
    $result[$work->idResource][$work->idProject][$work->day]+=$work->work;
    $workDayResource[$work->idResource][$work->day]+=$work->work;
  } else if ($work->day>date('Ymd')) {
    $result[$work->idResource][$work->idProject][$work->day]+=$work->work;
    $workDayResource[$work->idResource][$work->day]+=$work->work;
    if (isset($realDays[$work->idResource][$work->idProject][$work->day])) {
      unset($realDays[$work->idResource][$work->idProject][$work->day]);
    }
  }
}

if ($periodType=='month') {
  if($initPeriodDay[1]<10){
    if($initPeriodDay[0]<10){
      $startDate=$initPeriodDay[2].'0'.$initPeriodDay[1].'0'.$initPeriodDay[0];
    }else{
      $startDate=$initPeriodDay[2].'0'.$initPeriodDay[1].$initPeriodDay[0];
    }
    
  }else{
    if($initPeriodDay[0]<10){
      $startDate=$initPeriodDay[2].$initPeriodDay[1].'0'.$initPeriodDay[0];
    }else{
      $startDate=$initPeriodDay[2].$initPeriodDay[1].$initPeriodDay[0];
    } 
  }
  
  if (!$paramYear ) {
    echo '<div style="background: #FFDDDD;font-size:150%;color:#808080;text-align:center;padding:20px">';
    echo i18n('messageNoData',array(i18n('year'))); // TODO i18n message
    echo '</div>';
    if (!empty($cronnedScript)) goto end; else exit;
  }
  $time=mktime(0, 0, 0, $paramMonth, 1, $paramYear);
  $header=i18n(strftime("%B", $time)).strftime(" %Y", $time);
  $nbDays=date("t", $time);
}

for($i=0;$i<count($nbWeeks);$i++){
  //Calculates the total number of weeks
  $nbTotalWeeks=$nbTotalWeeks+$nbWeeks[$i];
}
$weekendBGColor='#cfcfcf';
$weekendFrontColor='#555555';
$weekendStyle=' style="background-color:' . $weekendBGColor . '; color:' . $weekendFrontColor . '" ';
$plannedBGColor='#FFFFDD';
$plannedFrontColor='#777777';
$plannedStyle=' style="text-align:center;background-color:' . $plannedBGColor . '; color: ' . $plannedFrontColor . ';" ';

$month=$paramYear.'-'.$paramMonth;
if (checkNoData($result,$month)) continue;

echo "<table width='95%' align='center'>";
echo "<tr><td><table  width='100%' align='left'><tr>";
echo "<td class='reportTableDataFull' style='width:20px;text-align:center;'>1</td>";
echo "<td width='100px' class='legend'>" . i18n('colRealWork') . "</td>";
echo "<td width='5px'>&nbsp;&nbsp;&nbsp;</td>";
echo '<td class="reportTableDataFull" ' . $plannedStyle . '><i>1</i></td>';
echo "<td width='100px' class='legend'>" . i18n('colPlanned') . "</td>";
echo "<td>&nbsp;</td>";
echo "<td class='legend'>" . Work::displayWorkUnit() . "</td>";
echo "<td>&nbsp;</td>";
echo "</tr>";
echo "</table>";

// title
echo '<table width="100%" align="left">';
echo '<tr>';
echo '<td class="reportTableHeader" rowspan="2">' . i18n('Resource') . '</td>';
echo '<td class="reportTableHeader" rowspan="2">' . i18n('Project') . '</td>';
echo HeaderCalc::dessinTableau($nbMonths,$header,$periodType,$periodValue,$paramYear,$paramMonth,$initParamMonth,$nbWeeks);
echo '</tr>';
echo '<tr>';
$days=array();
for($j=0;$j<count($periodValues);$j++){
  $daysInThisMonth = date('t',mktime(0,0,0,substr($periodValues[$j],4,2),1,substr($periodValues[$j],0,4)));
  for($i=1; $i<=$daysInThisMonth;$i++) {
    if ($periodType=='month') {
            
      $day=(($i<10)?'0':'') . $i;
      if (isOffDay(substr($periodValues[$j],0,4) . "-" . substr($periodValues[$j],4,2) . "-" . $day)) {
        $days[$periodValues[$j] . $day]="off";
        $style=$weekendStyle;
      } else {
        $days[$periodValues[$j] . $day]="open";
        $style='';
      }
    }  
  }
}

$firstDay=1;
if(date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Monday' || date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Tuesday'|| date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Wednesday'|| date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Thuresday'){
  $currentWeek = intval(date('W',mktime(0,0,0,$paramMonth,$firstDay,$paramYear)));
}else{
  $currentWeek = intval(date('W',mktime(0,0,0,$paramMonth,$firstDay+7,$paramYear)));
}

$currentMonth=$initParamMonth;
for($i=0; $i<count($nbWeeks);$i++){

  for($j=0;$j<$nbWeeks[$i];$j++){

    if($currentMonth<=12){

      if($j==0 and $currentMonth==$initParamMonth){
        echo '<td class="reportTableColumnHeader" >'.$currentWeek.'</td>';
      }else{
        $currentWeek=$currentWeek+1;
        echo '<td class="reportTableColumnHeader" >'.$currentWeek.'</td>';
              
      } 
    }else{
    
      $currentWeek=1;
      $currentMonth=1;
      echo '<td class="reportTableColumnHeader" >'.$currentWeek.'</td>';
      
    }
          
  }
  $currentMonth=$currentMonth+1;
        
}
echo '<td class="reportTableHeader" >' . i18n('sum'). '</td>';

$globalSum=array();

for ($i=0; $i<$nbTotalDaysScope;$i++) {
  
  //Initializing the global sum var
  $globalSum[$allDaysScopeArray[$i]]=0;

}
asort($resources);
//gautier #4342
if($idOrganization){
  $orga = new Organization($idOrganization);
  $listResOrg=$orga->getResourcesOfAllSubOrganizationsListAsArray();
  foreach ($resources as $idR=>$nameR){ 
    if(! in_array($idR, $listResOrg))unset($resources[$idR]);
  }
}
//AMELIORATION AFFICHAGE
$resourcesProjectsStr = "";

foreach ($resources as $idR=>$nameR) {
  $sumNpw=0;
  $totWeekWork = 0;

  //AMELIORATION AFFICHAGE
  $resourceStr = "";
  $resourceHeaderStr="";
  $res=new Resource($idR);

  if (!$paramTeam or $res->idTeam==$paramTeam) {
	  $sum=array();
	  for ($i=0; $i<$nbTotalDaysScope;$i++) {
      // Initialitiong sum
      $sum[$allDaysScopeArray[$i]]=0;
	  }
    //AFFI
    $rowspan = (count($result[$idR])+1);
    $resourceHeaderStr.= '<tr height="20px"><td class="reportTableLineHeader" style="width:100px;" rowspan="';
    
	  $sortProject=array();
	  foreach ($result[$idR] as $id=>$name) {
	    $sortProject[SqlList::getFieldFromId('Project', $id, 'sortOrder').'#'.$id]=$name;
	  }
	  ksort($sortProject);
	  $tmpprojects=array();
	  foreach ($sortProject as $sortId=>$name) {
	    $split=explode('#', $sortId);
	    $tmpprojects[$split[1]]=$name;
	  }
	  foreach ($tmpprojects as $idP=>$proj) {

      //AFFI
      $projectStr = '';
      $totMonthWork = 0;


	    if (array_key_exists($idP, $projects)) {
        //AFFI
        $projectStr.='<td class="reportTableData" style="width:150px;text-align: left;">' . htmlEncode($projects[$idP]) . '</td>';

	      $lineSum=0;
        $parallelCount =0;
	      for ($i=0; $i<$nbTotalDays;$i++){
          $parallelCount = $i+1;
	        $day=$allDaysArray[$i];
	        $ital=false;

          if (! isset($realDays[$idR][$idP][$day]) and array_key_exists($day,$result[$idR][$idP])) {
	          $style=$plannedStyle;
	          $ital=true;
	        }elseif(array_key_exists($day,$result[$idR][$idP])){
            $style='';
          }
	        
          if($parallelCount%7==0 and $parallelCount!=0){
            if (array_key_exists($day,$result[$idR][$idP])) {
              //AFFI
              $projectStr.= '<td class="reportTableData" ' . $style . ' valign="top">';

              $totWeekWork=$totWeekWork+$result[$idR][$idP][$day];
              //AFFI
              $totMonthWork = $totMonthWork + $totWeekWork;
              $projectStr.=($ital)?'<i>':'';
              $projectStr.=($totWeekWork>0)?$totWeekWork:'';
              $projectStr.=($ital)?'</i>':'';
              $sum[$day]+=$result[$idR][$idP][$day];
              $globalSum[$day]+=$result[$idR][$idP][$day];
              $lineSum+=$result[$idR][$idP][$day];
              $totWeekWork=0;
            }else{
              if($totWeekWork>0){
                //AFFI
                $projectStr.= '<td class="reportTableData" ' . $style . ' valign="top">';
                $projectStr.=($ital)?'<i>':'';
                $projectStr.=$totWeekWork;
                $projectStr.=($ital)?'</i>':'';
                $totMonthWork = $totMonthWork + $totWeekWork;

                $totWeekWork=0;
              }else{
                $style='';
                //AFFI
                $projectStr.= '<td class="reportTableData" ' . $style . ' valign="top">';
               
                $totWeekWork=0;
              }
            }
            //AFFI
            $projectStr.= '</td>';
            

          }else{

            if (array_key_exists($day,$result[$idR][$idP])) {

              $totWeekWork=$totWeekWork+$result[$idR][$idP][$day];
              $sum[$day]+=$result[$idR][$idP][$day];
              $globalSum[$day]+=$result[$idR][$idP][$day];
              $lineSum+=$result[$idR][$idP][$day];
            }
          }
	      }
	      
        //AFFI
        $projectStr.='<td class="reportTableColumnHeader">' . Work::displayWork($lineSum) . '</td>';

	      $ass= new Assignment();
	      $crit=array('idResource'=>$idR, 'idProject'=>$idP);
	      $npw=$ass->sumSqlElementsFromCriteria('notPlannedWork',$crit);
	      $sumNpw+=$npw;

        //AFFI
        $projectStr.='</tr><tr>';
	    }

      //AFFI
      if($totMonthWork>0){
        
        $resourceStr.=$projectStr;
      }else{
        $rowspan = $rowspan -1;
      }
      

	  }

    //AFFI
    $resourceStr.='<td class="reportTableLineHeader" >' . i18n('sum') . '</td>';
   
	  $lineSum=0;
    $sumDay=0;
    $maxWeekWork = 7;
	  for ($i=0; $i<$nbTotalDays; $i++) {
	    $parallelCount = $i+1;
      $style='';
      $day=$allDaysArray[$i];
	    if ($days[$day]=="off") {
        $maxWeekWork= $maxWeekWork-1;
	    }
	    $sumDay=$sumDay+$sum[$allDaysArray[$i]];
	    $day=substr($day,0,4).'-'.substr($day,4,2).'-'.substr($day,6,2);
      
      if($parallelCount%7==0){
        /*if($sumDay<$maxWeekWork && $sumDay<4){
          $style=' style="color:#d4c21c !important;font-weight:bold"';
        }else*/if ($sumDay>$maxWeekWork) {        
	        $style=' style="color:#a05050 !important;font-weight:bold"';
	      }elseif($sumDay*0.9<$maxWeekWork*0.9){
          $style=' style="color:#426f12 !important;font-weight:bold"';
        }
        //AFFI
        $resourceStr.='<td class="reportTableColumnHeader" ' . $style . ' >' . Work::displayWork($sumDay) . '</td>';
        
        $sumDay=0;
        $maxWeekWork=7;
      }
      $lineSum+=$sum[$allDaysArray[$i]];
	  }
    //AFFI
    $resourceStr.='<td class="reportTableHeader">' . Work::displayWork($lineSum) . '</td>'.'</tr>';
    
  }

  //AFFI
  if($rowspan!=1 && $rowspan!=0){
    $resourcesProjectsStr.=$resourceHeaderStr.$rowspan. '">' . htmlEncode($nameR) . '</td>'.$resourceStr;
  }
  
}

echo $resourcesProjectsStr;

echo '<tr><td colspan="' . ($nbDays+3) . '">&nbsp;</td></tr>';
echo '<tr><td class="reportTableHeader" colspan="2">' . i18n('sum') . '</td>';
$lineSum=0;
$globalWeekSum = 0;
for ($i=0; $i<$nbTotalDays;$i++) {
  
  $globalWeekSum = $globalWeekSum + $globalSum[$allDaysArray[$i]];
  $parallelCount = $i+1;
  $style='';
  $day=$allDaysArray[$i];
  if ($days[$day]=="off") {
    $style=$weekendStyle;
  }
  
  if($parallelCount%7==0){
    echo '<td class="reportTableHeader" ' . $style . '>' . $globalWeekSum . '</td>';
    $globalWeekSum=0;
  }
  
  $lineSum+=$globalSum[$allDaysArray[$i]];
  
}
echo '<td class="reportTableHeader">' . Work::displayWork($lineSum) . '</td>';
echo '</tr>';
echo '</table>';
echo '</td></tr></table>';

echo '<br/><br/>';
// END OF LOOP ON MONTH
}

end:
