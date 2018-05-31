<?php
    /* Notificator.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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

    session_start();
    include './RestControllerExternal.php';
    error_reporting(E_ERROR | E_NOTICE);
    
    header("Content-type: application/json");
    header("Access-Control-Allow-Origin: *");
    $response = [];
    $restController = new RestControllerExternal();
    
    if(isset($_REQUEST['operation']))
    {
      switch($_REQUEST['operation'])
      {
          //Restituisce elenco delle dashboard
          case "getDashboardsList":
              $response = $restController->getDashboardsList();
              break;
          
          case "getDashboardWidgets":
              if(isset($_REQUEST['dashboardTitle']))
              {
                  $response = $restController->getDashboardWidgets(urldecode($_REQUEST['dashboardTitle']));
              }
              else
              {
                  $response["detail"] = "missingParams";
              }
              break;
              
          case "getEvents":
              if(isset($_REQUEST['startDate']))
              {
                  $startDate = $_REQUEST['startDate'];
              }
              else
              {
                  $startDate = null;
              }
              
              if(isset($_REQUEST['endDate']))
              {
                  $endDate = $_REQUEST['endDate'];
              }
              else
              {
                  $endDate = null;
              }
              
              if(isset($_REQUEST['dashboardTitle']))
              {
                  $dashboardTitle = $_REQUEST['dashboardTitle'];
              }
              else
              {
                  $dashboardTitle = null;
              }
              
              if(isset($_REQUEST['widgetTitle']))
              {
                  $widgetTitle = $_REQUEST['widgetTitle'];
              }
              else
              {
                  $widgetTitle = null;
              }
              $response = $restController->getEvents(urldecode($startDate), urldecode($endDate), urldecode($dashboardTitle), urldecode($widgetTitle));
              break;    
          
          default:
              $response["detail"] = "operationNotRecognized";
              break;
      }
    }
    else
    {
        $response["detail"] = "noOperationSelected";
    }

    echo json_encode($response);
