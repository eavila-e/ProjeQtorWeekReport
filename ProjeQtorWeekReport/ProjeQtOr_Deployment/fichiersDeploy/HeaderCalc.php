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

class HeaderCalc{
    
  public $paramMonth;
  public $paramYear;
  public $periodValue;
  public $reqWhere;
  public $work;
  public $planWork;
  public $periodType;
  
  /** 
   * This function calculates the needed header according to the information passed as parameters.
   * 
   * @param $periodType    - defined as 'month' for the entire fonction, the fubction makes sure it is the case
   * @param $paramYear     - year value
   * @param $paramMonth    - month value
   * 
   * @return $header       - the string concatenating year and month
   * 
   * @author Esteban AVILA - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function calculHeader($periodType,$paramYear,$paramMonth){
    if ($periodType=='month') {
      if (!$paramYear) {
        echo '<div style="background: #FFDDDD;font-size:150%;color:#808080;text-align:center;padding:20px">';
        echo i18n('messageNoData',array(i18n('year'))); // TODO i18n message
        echo '</div>';
        if (!empty($cronnedScript))  exit;
        //if (!empty($cronnedScript)) goto end; else exit;
      }
      $time=mktime(0, 0, 0, intval($paramMonth), 1, intval($paramYear));
      $header=i18n(strftime("%B", $time)).strftime(" %Y", $time);
    }
    return $header;
  }

  /** 
   * This function calculates the first header period value to be used.
   * @param $periodType     - defined as 'month' for the entire fonction, the fubction makes sure it is the case
   * @param $paramMonth     - month value
   * @param $initParamMonth - initial month value
   * @param $cptMonth       - month counter __!Enlever??
   * @param $paramYear      - year value
   * 
   * @return $periodValue   - first period value
   * 
   * @author Esteban AVILA  - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function calculFirstPeriodValue($periodType,$paramMonth,$initParamMonth,$cptMonth,$paramYear){
    $periodValue="";
    if ($periodType=='month') {
      $paramMonth=intval($initParamMonth)+$cptMonth;
      if ($paramMonth>12) {$paramYear+=1;$paramMonth=1;}
      if ($paramMonth<10) $paramMonth='0'.$paramMonth;
      $periodValue=$paramYear.$paramMonth;
    }
    return $periodValue;
  }

  /** 
   * This function calculates all the period values according to the number of months wanted by the user.
   * @param $periodValue   - first period value
   * @param $nbMonths      - number of months to show
   * 
   * @return $allPeriods   - array of period values
   * 
   * @author Esteban AVILA - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function allPeriodValues($periodValue,$nbMonths){
    $allPeriods = array();
    $year = substr($periodValue,0,4);
    $month = substr($periodValue,4,2)-1;

    for($i=0;$i<$nbMonths;$i++){

      //If a month is less than 10 we add a '0' to the period value array
      if(intval($month)<10){
        $period = $year.'0'.$month;
      }else{
        $period = $year.$month;
      }
      array_push($allPeriods,$period);
      
      //Advance to next month (and year if needed)
      if ($month>=12) {
        $year=intval($year)+1;
        $month=1;
      }else{
        $month=intval($month)+1;
      }
    }
    return $allPeriods;
  }

  /** 
   * This function calculates the total number of days of the resuested period
   * @param $nbWeeks       - array number of weeks of each month
   * @param $paramMonth    - month value
   * @param $paramYear     - year value
   * @param $nbMonths      - number of months to show
   * 
   * @return $totDays      - total number of days in the requested number of months
   * 
   * @author Esteban AVILA - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function totalDays($nbWeeks,$paramMonth,$paramYear,$nbMonths){
    $totDays = 0;
    for($i=0;$i<$nbMonths;$i++){
      $monthsDays = $nbWeeks[$i]*7;
      $totDays = $totDays+$monthsDays;
    }
    return $totDays;
  }

  /** 
   * This function uses the period valies to get an array with all the headers
   * @param $periodType     - defined as 'month' for the entire fonction, the fubction makes sure it is the case
   * @param $paramMonth     - month value
   * @param $initParamMonth - initial month value
   * @param $paramYear      - year value
   * @param $nbMonths       - number of months to show
   * 
   * @return $allHeaders    - array of headers
   * 
   * @author Esteban AVILA  - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function calculPeriodValueHeaders($periodType,$paramMonth,$initParamMonth,$paramYear,$nbMonths){
    $allHeaders=array();
    $cptMonth=0;
    while($cptMonth<$nbMonths){

      if ($periodType=='month') {
        if($cptMonth==0){
          $paramMonth=intval($paramMonth);
        }else{
          $paramMonth=intval($paramMonth)+1;
        }
        if ($paramMonth>12) {
          $paramYear+=1;
          $paramMonth=1;
        }
        if ($paramMonth<10) $paramMonth='0'.$paramMonth;
        $allHeaders[$cptMonth]=HeaderCalc::calculHeader($periodType,$paramYear,$paramMonth);
      }
      $cptMonth=$cptMonth+1; 

    }
    return $allHeaders;
  }

  /** 
   * This function draws the brackets that contain the headers
   * @param $nbMonths       - number of months to show
   * @param $header         - description of each month
   * @param $periodType     - defined as 'month' for the entire fonction, the fubction makes sure it is the case
   * @param $periodValue    - first period value
   * @param $paramYear      - year value
   * @param $paramMonth     - month value
   * @param $initParamMonth - initial month value
   * @param $nbWeeks        - array number of weeks of each month
   * 
   * @return $resp          - string containing all the brakets needed to display
   * 
   * @author Esteban AVILA  - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function dessinTableau($nbMonths,$header,$periodType,$periodValue,$paramYear,$paramMonth,$initParamMonth,$nbWeeks){
    //Creation du planing de 1, 2, 3 ou 6 mois
    $resp='';
    if($nbMonths==1){
      $allHeaders=HeaderCalc::calculPeriodValueHeaders($periodType,$paramMonth,$initParamMonth,$paramYear,$nbMonths);
      for($i=0; $i<1;$i++){
        if(isset($allHeaders[$i])){
          $resp.='<td colspan="'.$nbWeeks[$i].'" class="reportTableHeader">'.$allHeaders[$i].'</td>';
        }
      }
      return $resp;
    }elseif($nbMonths==2){
      $allHeaders=HeaderCalc::calculPeriodValueHeaders($periodType,$paramMonth,$initParamMonth,$paramYear,$nbMonths);
      for($i=0; $i<2;$i++){
        if(isset($allHeaders[$i])){
          $resp.='<td colspan="'.$nbWeeks[$i].'" class="reportTableHeader">'.$allHeaders[$i].'</td>';
        }
      }
      return $resp;
    }elseif($nbMonths==3){
      $allHeaders=HeaderCalc::calculPeriodValueHeaders($periodType,$paramMonth,$initParamMonth,$paramYear,$nbMonths);
      for($i=0; $i<3;$i++){
        if(isset($allHeaders[$i])){
          $resp.='<td colspan="'.$nbWeeks[$i].'" class="reportTableHeader">'.$allHeaders[$i].'</td>';
        }
      }
      return $resp;
    }elseif($nbMonths==6){
      $allHeaders=HeaderCalc::calculPeriodValueHeaders($periodType,$paramMonth,$initParamMonth,$paramYear,$nbMonths);
      for($i=0; $i<6;$i++){
        if(isset($allHeaders[$i])){
          $resp.='<td colspan="'.$nbWeeks[$i].'" class="reportTableHeader">'.$allHeaders[$i].'</td>';
        }
      }
      return $resp;    
    }
  }

  /** 
   * This function returns an array containing the number of weeks for each month for a certain period
   * @param $initParamMonth   - initial month value
   * @param $nbMonths         - number of months to show
   * @param $paramYear        - year value
   * 
   * @return $nbWeeks         - array containing the number of weeks for each month
   * 
   * @author Esteban AVILA    - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function weeksPerMonth($initParamMonth,$nbMonths,$paramYear){
    
    $nbWeeks=array();
    $currentMonth = intval($initParamMonth);
    $firstDay = 1;
    $year = $paramYear;
    
    $initWeek = 0 ;
    if(date('l',mktime(0,0,0,intval($currentMonth),$firstDay,$year))=='Monday' || date('l',mktime(0,0,0,intval($currentMonth),$firstDay,$year))=='Tuesday'|| date('l',mktime(0,0,0,intval($currentMonth),$firstDay,$year))=='Wednesday'|| date('l',mktime(0,0,0,intval($currentMonth),$firstDay,$year))=='Thursday'){
      $initWeek = date('W',mktime(0,0,0,$currentMonth,$firstDay,$year));
    }else{
      $initWeek = date('W',mktime(0,0,0,intval($currentMonth),$firstDay+7,$year));
    }
    $currentWeek=$initWeek;
    for($i=0;$i<$nbMonths;$i++){
    
      if($currentMonth==12){//Condition to identify if it is the last week of the year

        if(date('l',mktime(0,0,0,$currentMonth,31,$year))=='Thursday' || date('l',mktime(0,0,0,$currentMonth,31,$year))=='Friday' || date('l',mktime(0,0,0,$currentMonth,31,$year))=='Saturday' || date('l',mktime(0,0,0,$currentMonth,31,$year))=='Sunday'){
          
          $nbWeeks[$i]=intval(date('W',mktime(0,0,0,$currentMonth,31,$year)))-intval($currentWeek)+1;
          $currentWeek=1;

        }else{

          $nbWeeks[$i]=intval(date('W',mktime(0,0,0,$currentMonth,31-7,$year)))-intval($currentWeek)+1;
          $currentWeek=1;

        }
      
      }elseif(date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Monday' || date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Tuesday'|| date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Wednesday'|| date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Thursday'){
        
        $nbWeeks[$i]=intval(date('W',mktime(0,0,0,$currentMonth+1,$firstDay,$year)))-intval($currentWeek);
        
        if(date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Monday' || date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Tuesday'|| date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Wednesday'|| date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Thursday'){
          $currentWeek=date('W',mktime(0,0,0,$currentMonth+1,$firstDay,$year)+1); 
        }else{
          $currentWeek=date('W',mktime(0,0,0,$currentMonth+1,$firstDay+7,$year)+1); 
        }

      }else{

        $nbWeeks[$i]=intval(date('W',mktime(0,0,0,$currentMonth+1,$firstDay+7,$year)))-intval($currentWeek);
        
        if(date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Monday' || date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Tuesday'|| date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Wednesday'|| date('l',mktime(0,0,0,$currentMonth+1,$firstDay,$year))=='Thursday'){
          $currentWeek=date('W',mktime(0,0,0,$currentMonth+1,$firstDay,$year)); 
        }else{
          $currentWeek=date('W',mktime(0,0,0,$currentMonth+1,$firstDay+7,$year)); 
        }
        
      }

      if ($currentMonth>=12) {
        $year=$year+1;
        $currentMonth=1;
      }else{
        $currentMonth=$currentMonth+1;
      }
    }
    return $nbWeeks;
  }

  /** 
   * This function returns a 2D array containing an Nth month's information as shown below.
   * @param $currentMonth  - month being processed
   * @param $paramYear     - year value
   * @param $periodValue   - period value
   * @param $nbWeeks       - array containing the number of weeks for each month
   * 
   * @return $monthArray   - 2D array containing an Nth month's information as shown:
   *                         *$monthArray[n] = {$daysInAWeek,$firstDayWeek}
   *                            *-$daysInAWeek = number of labour days in a week
   *                            *-$firstDayWeek = calendar number of the first day of the week
   * 
   * @author Esteban AVILA - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function firstDayOfWeek($currentMonth,$paramYear,$periodValue,$nbWeeks){
    $year = $paramYear;
    $monthArray= array();
    $tempArray=array();
    $daysInAWeek =0;
    $firstDayWeek=0;
    for($i=0;$i<$nbWeeks[$i];$i++){
      $firstDayWeek=HeaderCalc::findFirstDayWeek($currentMonth,$paramYear,$i);
      $daysInAWeek=HeaderCalc::calcDaysInAWeek($periodValue,$firstDayWeek);
      
      $tempArray = array($daysInAWeek,$firstDayWeek);

      array_push($monthArray,$tempArray);
    }
    return $monthArray;
  }

  /** 
   * This function finds the calendar number of the first day of the requested week
   * @param $initParamMonth  - initial month value
   * @param $paramYear       - year value
   * @param $decalage        - helps to take into account which week we are calculating
   *  
   * @return $dayNb          - exact day number where the week begins
   * 
   * @author Esteban AVILA   - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function findFirstDayWeek($initParamMonth,$paramYear,$decalage){
    
    $currentMonth=$initParamMonth;
    $nbDays=date("t", mktime(0, 0, 0, $currentMonth, 1, $paramYear));
    $currentWeek =0;
    $firstDay=1;

    if(date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Monday' || date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Tuesday'|| date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Wednesday'|| date('l',mktime(0,0,0,$initParamMonth,$firstDay,$paramYear))=='Thuresday'){
      $currentWeek = intval(date('W',mktime(0,0,0,$currentMonth,$firstDay,$paramYear)));
    }else{
      $currentWeek = intval(date('W',mktime(0,0,0,$currentMonth,$firstDay+7,$paramYear)));
    }
    $firstDay = date('l',mktime(0,0,0,$currentMonth,1+$decalage*7,$paramYear));
    $dayNb = 0;
    if(1+$decalage*7==1){
      switch($firstDay){
        case 'Monday':
          //echo '';
          $dayNb = 1;
          break;
        case 'Tuesday':
          if($currentMonth==1){
            $currentMonth=13;
          }
          $nbDays=date("t", mktime(0, 0, 0, $currentMonth-1, 1, $paramYear));
          //echo '';
          $dayNb = $nbDays;
          break;
        case 'Wednesday':
          if($currentMonth==1){
            $currentMonth=13;
          }
          $nbDays=date("t", mktime(0, 0, 0, $currentMonth-1, 1, $paramYear));
          //echo '';
          $dayNb = $nbDays-1;
          break;
        case 'Thursday':
          if($currentMonth==1){
            $currentMonth=13;
          }
          $nbDays=date("t", mktime(0, 0, 0, $currentMonth-1, 1, $paramYear));
          //echo '';
          $dayNb = $nbDays-3;
          break;
        case 'Friday':
          if($currentMonth==1){
            $currentMonth=13;
          }
          $nbDays=date("t", mktime(0, 0, 0, $currentMonth-1, 1, $paramYear));
          //echo '';
          $dayNb = $nbDays-4;
          break;
        case 'Saturday':
          if($currentMonth==1){
            $currentMonth=13;
          }
          $nbDays=date("t", mktime(0, 0, 0, $currentMonth-1, 1, $paramYear));
          //echo '';
          $dayNb = $nbDays-5;
          break;
        case 'Sunday' :
          if($currentMonth==1){
            $currentMonth=13;
          }
          $nbDays=date("t", mktime(0, 0, 0, $currentMonth-1, 1, $paramYear));
          //echo '';
          $dayNb = $nbDays-6;
          break;
      }
    }else{
      switch($firstDay){
        case 'Monday':
          echo '';
          $dayNb = 1+$decalage*7;
          break;
        case 'Tuesday':
          echo '';
          $dayNb = $decalage*7;
          break;
        case 'Wednesday':
          echo '';
          $dayNb = $decalage*7-2;
          break;
        case 'Thursday':
          echo '';
          $dayNb =  $decalage*7-3;
          break;
        case 'Friday':
          echo '';
          $dayNb =  $decalage*7-4;
          break;
        case 'Saturday':
          echo '';
          $dayNb =  $decalage*7-5;
          break;
        case 'Sunday' :
          echo '';
          $dayNb =  $decalage*7-6;
          break;
      }
    }
    return $dayNb;
  }

  /** 
   * This function calculates the total number of days in a week, taking into account the holydays
   * @param $periodValue   - String containing the year and month we look at
   * @param $day           - first day number of the week we looking at
   * 
   * @return $daysTotal    - total number of labour days in the week
   * 
   * @author Esteban AVILA - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function calcDaysInAWeek($periodValue,$day){
    $daysTotal = 5;
    for($i=0;$i<5;$i++){
      if(isOffDay(substr($periodValue,0,4) . "-" . substr($periodValue,4,2) . "-" . $day)){
        $daysTotal=$daysTotal-1;
      }
      $day=$day+1;
    }
    return $daysTotal;
  }

  /** 
   * This function returns an array of dates in the form 'Ymd', these days are in the period covered from the $initPeriodDay and the last day
   * @param $nbTotalDays     - total number of days in a period
   * @param $initPeriodDay   - $first day of the period
   * 
   * @return $retArray       - array of dates in the form 'Ymd'
   * 
   * @author Esteban AVILA   - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function writeAllDays($nbTotalDays,$initPeriodDay){
    $retArray = array();

    for($i=0;$i<$nbTotalDays;$i++){
      if($i==0){
        $tempPeriodVal = date('Ymd',mktime(0,0,0,$initPeriodDay[1],$initPeriodDay[0],$initPeriodDay[2]));
        array_push($retArray,$tempPeriodVal);
      }else{
        $tempPeriodVal = date('Ymd',mktime(0,0,0,$initPeriodDay[1],$initPeriodDay[0]+$i,$initPeriodDay[2]));
        array_push($retArray,$tempPeriodVal);
      }
    }
    return $retArray;
  }

  /** 
   * This function finds the first monday of the period, returns an array with the day number, month and year
   * @param $initParamMonth  - initial month value
   * @param $paramYear       - year value
   * 
   * @return $retArray       - array with the number, month and year of the period
   * 
   * @author Esteban AVILA   - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function firstDayOfPeriod($initParamMonth,$paramYear){
    $firstDayOfPeriod = 0;
    $monthOfPeriod = 0;
    $yearOfPeriod = 0;
    $retArray = array();
    $firstDayMonth = date('l',mktime(0,0,0,$initParamMonth,1,$paramYear));
    $nbDaysLastMonth =0;
    if($initParamMonth==1){
      $nbDaysLastMonth=date("t", mktime(0, 0, 0, 12, 1, $paramYear-1));
    }else{
      $nbDaysLastMonth=date("t", mktime(0, 0, 0, $initParamMonth-1, 1, $paramYear));
    }
    
    /*
      3 possible cases
      1) 1st of the month is a monday then we can go return the day, month and year of the begining
      2) 1st of the month is a tuesday, wednesday or thursday then:
        -Look for the number of days in the last month
        -Look for the day that is a monday in the last month
        -return the day, month and year of the found day
      3) 1st of the month is a friday, saturday or sunday
        -Look for the monday in the next week
    */
    if($firstDayMonth=='Monday'){
      $firstDayOfPeriod = 1;
      $monthOfPeriod = $initParamMonth;
      $yearOfPeriod = $paramYear;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Tuesday'){
      $firstDayOfPeriod = $nbDaysLastMonth;
      if($initParamMonth==1){
        $monthOfPeriod=12;
        $yearOfPeriod=$paramYear-1;
      }else{
        $monthOfPeriod=$initParamMonth-1;
        $yearOfPeriod=$paramYear;
      }
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Wednesday'){
      $firstDayOfPeriod = $nbDaysLastMonth-1;
      if($initParamMonth==1){
        $monthOfPeriod=12;
        $yearOfPeriod=$paramYear-1;
      }else{
        $monthOfPeriod=$initParamMonth-1;
        $yearOfPeriod=$paramYear;
      }
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;
    }elseif($firstDayMonth=='Thursday'){
      $firstDayOfPeriod = $nbDaysLastMonth-2;
      if($initParamMonth==1){
        $monthOfPeriod=12;
        $yearOfPeriod=$paramYear-1;
      }else{
        $monthOfPeriod=$initParamMonth-1;
        $yearOfPeriod=$paramYear;
      }
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Friday'){
      $firstDayOfPeriod = 4;
      $monthOfPeriod = $initParamMonth;
      $yearOfPeriod = $paramYear;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Saturday'){
      $firstDayOfPeriod = 3;
      $monthOfPeriod = $initParamMonth;
      $yearOfPeriod = $paramYear;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }else{
      $firstDayOfPeriod = 2;
      $monthOfPeriod = $initParamMonth;
      $yearOfPeriod = $paramYear;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;
    }
  }

  //SCOPE FUNCTIONS: Take care of a one month before and after the 1, 2, 3 or 6 months original period
  //so they have some little variations but have thesame objective as the non-scope functions

  /** 
   * This function finds the first monday of the period, returns an array with the day number, month and year
   * @param $initParamMonth  - initial month value
   * @param $paramYear       - year value
   * 
   * @return $retArray       - array with the number, month and year of the period
   * 
   * @author Esteban AVILA - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function firstDayOfScopePeriod($initParamMonth,$paramYear){
    //We adjust the confguratons :
    $firstDayOfPeriod = 0;
    $monthOfPeriod = 0;
    $yearOfPeriod = 0;

    if ($initParamMonth==1) {
      $yearOfPeriod=$paramYear-1;
      $monthOfPeriod=12;
    }else{
      $monthOfPeriod = $initParamMonth-1;
      $yearOfPeriod = $paramYear;
    }
    
    
    $retArray = array();
    $firstDayMonth = date('l',mktime(0,0,0,$monthOfPeriod,1,$yearOfPeriod));
    $nbDaysLastMonth =0;
    if($monthOfPeriod==1){
      $nbDaysLastMonth=date("t", mktime(0, 0, 0, 12, 1, $yearOfPeriod-1));
    }else{
      $nbDaysLastMonth=date("t", mktime(0, 0, 0, $monthOfPeriod-1, 1, $yearOfPeriod));
    }
    
    /*
      3 possible cases
      1) 1st of the month is a monday then we can go return the day, month and year of the begining
      2) 1st of the month is a tuesday, wednesday or thursday then:
        -Look for the number of days in the last month
        -Look for the day that is a monday in the last month
        -return the day, month and year of the found day
      3) 1st of the month is a friday, saturday or sunday
        -Look for the monday in the next week
    */
    if($firstDayMonth=='Monday'){
      $firstDayOfPeriod = 1;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Tuesday'){
      $firstDayOfPeriod = $nbDaysLastMonth;
      if($monthOfPeriod==1){
        $monthOfPeriod=12;
        $yearOfPeriod=$yearOfPeriod-1;
      }else{
        $monthOfPeriod=$monthOfPeriod-1;
        $yearOfPeriod=$yearOfPeriod;
      }
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Wednesday'){
      $firstDayOfPeriod = $nbDaysLastMonth-1;
      if($monthOfPeriod==1){
        $monthOfPeriod=12;
        $yearOfPeriod=$yearOfPeriod-1;
      }else{
        $monthOfPeriod=$monthOfPeriod-1;
        $yearOfPeriod=$yearOfPeriod;
      }
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;
    }elseif($firstDayMonth=='Thursday'){
      $firstDayOfPeriod = $nbDaysLastMonth-2;
      if($monthOfPeriod===1){
        $monthOfPeriod=12;
        $yearOfPeriod=$yearOfPeriod-1;
      }else{
        $monthOfPeriod=$monthOfPeriod-1;
        $yearOfPeriod=$yearOfPeriod;
      }
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Friday'){
      $firstDayOfPeriod = 4;
      $monthOfPeriod = $monthOfPeriod;
      $yearOfPeriod = $yearOfPeriod;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }elseif($firstDayMonth=='Saturday'){
      $firstDayOfPeriod = 3;
      $monthOfPeriod = $monthOfPeriod;
      $yearOfPeriod = $yearOfPeriod;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;

    }else{
      $firstDayOfPeriod = 2;
      $monthOfPeriod = $monthOfPeriod;
      $yearOfPeriod = $yearOfPeriod;
      $retArray = array($firstDayOfPeriod,$monthOfPeriod,$yearOfPeriod);
      return $retArray;
    }
  }

  /** 
   * Varation of the totalDays function but has to add a month before and after the period in order to find the toal number of days of the scope zone
   * @param $initParamMonth  - initial month value
   * @param $paramYear       - year value
   * @param $nbMonths        - number of months to show
   * 
   * @return $totDays        - total number of days in the requested number of months + 2
   * 
   * @author Esteban AVILA   - esteban.avila-espinosa@insa-lyon.fr
  */
  public static function totalDaysScope($initParamMonth,$paramYear,$nbMonths){
    $firstDayOfPeriod = 0;
    $monthOfPeriod = 0;
    $yearOfPeriod = 0;

    if ($initParamMonth==1) {
      $yearOfPeriod=$paramYear-1;
      $monthOfPeriod=12;
    }else{
      $monthOfPeriod = $initParamMonth;
      $yearOfPeriod = $paramYear;
    }

    $nbWeeks = HeaderCalc::weeksPerMonth($monthOfPeriod,$nbMonths+2,$yearOfPeriod);
    $totDays = 0;

    for($i=0;$i<$nbMonths+2;$i++){
      $monthsDays = $nbWeeks[$i]*7;
      $totDays = $totDays+$monthsDays;
    }
    return $totDays;
  }

}
