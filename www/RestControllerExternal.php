<?php
/* Notificator.
   Copyright (C) 2018 DISIT Lab http://www.disit.org - University of Florence

   This program is free softwrulese; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Softwrulese Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied wrulesranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Softwrulese
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

error_reporting(E_ERROR | E_NOTICE);

class RestControllerExternal
{
   private $link;
   private $conf;

   private static function bindParams($stmt, $types, $params)
   {
      $bind = [];
      $bind[] = $types;
      foreach ($params as $key => $value)
      {
         $bind[] = &$params[$key];
      }
      return call_user_func_array([$stmt, 'bind_param'], $bind);
   }
   
   //Constructor
   function __construct() 
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      mysqli_set_charset($this->link, 'utf8');
      mysqli_select_db($this->link, $this->conf["dbName"]);
   }
   
   //Getters
   function getConf()
   {
      return $this->conf;
   }
   
   function getLink()
   {
      return $this->link;
   }
   
   //Setters
   function setConf($conf)
   {
      $this->conf = $conf;
   }
   
   function setLink($link)
   {
      $this->link = $link;
   }
   
   //Business methods
   function getDashboardsList()
   {
      $result = [];
      
      if(!$this->link)
      {
         $result['detail'] = "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query = "SELECT DISTINCT(containerName) AS dashboardTitle, url AS dashboardUrl FROM " . $this->conf["dbName"] . ".eventGenerators WHERE appName = 'Dashboard Manager'"; 
         $rs = mysqli_query($this->link, $query); 
         
         if($rs)
         {
            $result['data'] = [];
            $result['detail'] = "Ok";
            
            while($row = mysqli_fetch_assoc($rs))
            {
               array_push($result['data'], $row);
            }
         }
         else
         {
            $result['detail'] = "queryKo";
         }
         
      }
      
      return $result;
   }
   
   function getDashboardWidgets($dashboardTitle)
   {
      $result = [];
      
      if(!$this->link)
      {
         $result['detail'] = "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         //$file = fopen("C:\dashboardLog.txt", "w");
         
         $q1 = "SELECT id AS generatorId, generatorOriginalName AS widgetTitle, generatorOriginalType AS metricName, appUsr AS user FROM " . $this->conf["dbName"] . ".eventGenerators WHERE appName = 'Dashboard Manager' AND containerName = ? AND val = 1"; 
         //fwrite($file, "Q1: " . $q1 . "\n");
         $stmt1 = mysqli_prepare($this->link, $q1);
         if($stmt1)
         {
            mysqli_stmt_bind_param($stmt1, "s", $dashboardTitle);
            mysqli_stmt_execute($stmt1);
         }
         
         if($stmt1)
         {
            $result['data'] = [];
            $result['detail'] = "Ok";
            
            mysqli_stmt_bind_result($stmt1, $genId, $widgetTitle, $metricName, $appUsr);
            while(mysqli_stmt_fetch($stmt1))
            {
               $row1 = [
                  'generatorId' => $genId,
                  'widgetTitle' => $widgetTitle,
                  'metricName' => $metricName,
                  'user' => $appUsr,
               ];
               $q2 = "SELECT eventType FROM " . $this->conf["dbName"] . ".events WHERE genId = ?"; 
               //fwrite($file, "Q2: " . $q2 . "\n");
               $stmt2 = mysqli_prepare($this->link, $q2);
               if($stmt2)
               {
                  mysqli_stmt_bind_param($stmt2, "i", $genId);
                  mysqli_stmt_execute($stmt2);
               }
                
               if($stmt2)
               {
                   $row1['eventTypes'] = [];
                   mysqli_stmt_bind_result($stmt2, $eventType);
                   while(mysqli_stmt_fetch($stmt2))
                   {
                       $row1['eventTypes'][] = $eventType;
                   }
                   mysqli_stmt_close($stmt2);
               }
               else
               {
                   $row1['eventTypes'] = 'queryKo';
               }
                
                
               array_push($result['data'], $row1);
            }
         }
         else
         {
            $result['detail'] = "queryKo";
         }
      }
      
      return $result;
   }
   
   function getEvents($startDate, $endDate, $dashboardTitle, $widgetTitle)
   {
        $result = [];
      
        if(!$this->link)
        {
           $result['detail'] = "dbConnectionKo";
        }
        else 
        {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            if($startDate == null)
            {
                $startDate = '1900-01-01 00:00:00';
            }
            
            if($endDate == null)
            {
                $endDate = '2900-01-01 00:00:00';
            }
           
            $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                     "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                     "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                     "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                     "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                     "ON events.eventTypeId = evtTypes.id " .
                     "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                     "ON evtTypes.genId = generators.id " .
                     "WHERE STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') >= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') <= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') " .
                     "AND generators.appName = 'Dashboard Manager' ";
            $types = "ss";
            $params = [$startDate, $endDate];

            if($dashboardTitle != null)
            {
                $query = $query . "AND generators.containerName LIKE ? ";
                $types .= "s";
                $params[] = "%" . $dashboardTitle . "%";
            }
            
            if($widgetTitle != null)
            {
                $query = $query . "AND generators.generatorOriginalName LIKE ? ";
                $types .= "s";
                $params[] = "%" . $widgetTitle . "%";
            }
            
            //$query = $query . "AND generators.containerName LIKE '%$dashboardTitle%')||(generators.generatorOriginalName LIKE '%$searchText%')||(generators.generatorOriginalType LIKE '%$searchText%')||(generators.appUsr LIKE '%$searchText%')||(evtTypes.eventType LIKE '%$searchText%')) ";
           
            $query = $query . "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
            
            $stmt = mysqli_prepare($this->link, $query);
            if($stmt)
            {
               self::bindParams($stmt, $types, $params);
               mysqli_stmt_execute($stmt);
            }

            if($stmt)
            {
                $result['data'] = [];
                $result['detail'] = "Ok";

                mysqli_stmt_bind_result(
                   $stmt,
                   $eventId,
                   $eventTime,
                   $eventType,
                   $appName,
                   $appUsr,
                   $genName,
                   $genType,
                   $genContainer,
                   $url
                );
                while(mysqli_stmt_fetch($stmt))
                {
                    $result['data'][] = [
                       'id' => $eventId,
                       'eventTime' => $eventTime,
                       'eventType' => $eventType,
                       'appName' => $appName,
                       'appUsr' => $appUsr,
                       'genName' => $genName,
                       'genType' => $genType,
                       'genContainer' => $genContainer,
                       'url' => $url,
                    ];
                }
                mysqli_stmt_close($stmt);
            }
            else
            {
               $result['detail'] = "queryKo";
            }
        }
        
        return $result;
   }
   
   
   
   
   
   
}//Fine classe
