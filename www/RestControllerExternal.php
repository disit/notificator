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
         
         $q1 = "SELECT id AS generatorId, generatorOriginalName AS widgetTitle, generatorOriginalType AS metricName, appUsr AS user FROM " . $this->conf["dbName"] . ".eventGenerators WHERE appName = 'Dashboard Manager' AND containerName = '$dashboardTitle' AND val = 1"; 
         //fwrite($file, "Q1: " . $q1 . "\n");
         $rs1 = mysqli_query($this->link, $q1); 
         
         if($rs1)
         {
            $result['data'] = [];
            $result['detail'] = "Ok";
            
            while($row1 = mysqli_fetch_assoc($rs1))
            {
               $genId = $row1['generatorId']; 
               $q2 = "SELECT eventType FROM " . $this->conf["dbName"] . ".events WHERE genId = $genId"; 
               //fwrite($file, "Q2: " . $q2 . "\n");
               $rs2 = mysqli_query($this->link, $q2);
               
               if($rs2)
               {
                   $row1['eventTypes'] = [];
                   while($row2 = mysqli_fetch_assoc($rs2))
                   {
                       array_push($row1['eventTypes'], $row2['eventType']);
                   }
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
                     "WHERE STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') >= STR_TO_DATE('$startDate','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') <= STR_TO_DATE('$endDate','%Y-%m-%d %H:%i:%s') " .
                     "AND generators.appName = 'Dashboard Manager' ";

            if($dashboardTitle != null)
            {
                $query = $query . "AND generators.containerName LIKE '%$dashboardTitle%' ";
            }
            
            if($widgetTitle != null)
            {
                $query = $query . "AND generators.generatorOriginalName LIKE '%$widgetTitle%' ";
            }
            
            //$query = $query . "AND generators.containerName LIKE '%$dashboardTitle%')||(generators.generatorOriginalName LIKE '%$searchText%')||(generators.generatorOriginalType LIKE '%$searchText%')||(generators.appUsr LIKE '%$searchText%')||(evtTypes.eventType LIKE '%$searchText%')) ";
           
            $query = $query . "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
            
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
   
   
   
   
   
   
}//Fine classe
