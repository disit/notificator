<?php
require 'sso/autoload.php';
use Jumbojett\OpenIDConnectClient; 

session_start();
include './RestController.php';

/* Alarm Manager.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
$response = [];
$restController = new RestController();

if(isset($_REQUEST['apiUsr']))
{
   if(isset($_REQUEST['apiPwd']))
   {
         $clientRs = $restController->verClient($_REQUEST['apiUsr'], $_REQUEST['apiPwd']);
         if($clientRs)
         {
            if(isset($_REQUEST['operation']))
            {
              switch($_REQUEST['operation'])
              {
                 case "getClientApps":
                    $result = $restController-> getClientApps();
                    if($result != "queryKo")
                    {
                       $response["result"] = "Ok";
                       $response["apps"] = $result;
                    }
                    else
                    {
                       $response["result"] = "queryKo";
                    }
                    break;
                    
                    
                  /*Come appName Cristiano manderà sempre "Twitter Vigilance"
                  generatorOriginalName è il nome del widget su DM, nome della metrica su TV
                  generatorOriginalType è il nome della metrica su DM, su TV Cristiano ci manda il macro-tipo, 
                  cioè metriche "semplici" (HLM (significa High Level Metrics)) oppure trend (trend).
                  LA COPPIA generatorOriginalName - generatorOriginalType dev'essere chiave univoca per identificare un 
                  generatore*/
                 case "registerEventGenerator":
                     if(isset($_REQUEST["appName"])&&isset($_REQUEST["appUsr"])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["url"]))
                     {
                        $response["result"] = $restController->registerEventGenerator($_REQUEST["appName"], $_REQUEST["appUsr"], $_REQUEST["url"], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"]);
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                    break;
                    
                 case "registerEventType":
                     if(isset($_REQUEST["appName"])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["eventType"])&&isset($_REQUEST["thrCnt"]))
                     {
                        $response["result"] = $restController->registerEventType($_REQUEST["appName"], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"], $_REQUEST["eventType"], $_REQUEST["thrCnt"]);
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                    break;
                 
                 
                 case "remoteLogin": 
                    if((!isset($_SESSION['loginType']))&&(!isset($_SESSION['loginApp']))&&(!isset($_SESSION['loginAppLdap']))&&(!isset($_SESSION['loginUsr']))&&(!isset($_SESSION['usrOrigin']))&&(!isset($_SESSION['usrRole'])))
                    {
                        if(isset($_REQUEST["usr"])&&isset($_REQUEST["clientApplication"])&&($_REQUEST["usr"]!= "")&&($_REQUEST["clientApplication"]!= ""))
                        {
                           
                           $result = $restController->remoteLogin($_REQUEST["usr"], $_REQUEST["clientApplication"], $_SERVER['REMOTE_ADDR']);
                           $response["result"] = $result["detail"];
                        }
                        else
                        {
                            //$file = fopen("C:\dashboardLog.txt", "a");
                           //fwrite($file, "Usr: " . $_REQUEST["usr"] . " - Client: " . $_REQUEST["clientApplication"] . "\n");
                           $response["result"] = "missingParams";
                        }
                    }
                    else
                    {
                       $response["result"] = "alreadyLoggedIn";
                    }
                    break;
                    
                 case "getRemoteLoginStatus":
                    if(isset($_SESSION['loginUsr']))
                    {
                       $response["result"] = "Logged";
                       $response['loginType'] = $_SESSION['loginType'];
                       $response['loginApp'] = $_SESSION['loginApp'];
                       $response['loginAppLdap'] = $_SESSION['loginAppLdap'];
                       $response['loginUsr'] = $_SESSION['loginUsr'];
                    }
                    else
                    {
                       $response["result"] = "Not logged";
                    }
                    
                    break;
                    
                 case "localLogin":
                    if(isset($_REQUEST["usr"])&&isset($_REQUEST["pwd"])&&($_REQUEST["usr"] != "")&&($_REQUEST["pwd"] != ""))
                    {
                       $result = $restController->localLogin($_REQUEST["usr"], $_REQUEST["pwd"]);
                       
                       switch($result["detail"])
                       {
                           case "Ok":
                             $response["result"] = "Ok";
                             $_SESSION['loginType'] = "local";
                             $_SESSION['loginApp'] = $result["usrApp"];
                             $_SESSION['loginAppLdap'] = $result["usrAppLdap"];
                             $_SESSION['loginUsr'] = $_REQUEST['usr'];
                             $_SESSION["usrOrigin"] = $result["usrOrigin"];
                             $_SESSION["usrRole"] = $result["usrRole"];
                             $response["loginApp"] = $_SESSION['loginApp'];
                             $response['loginAppLdap'] = $_SESSION['loginAppLdap'];
                             $response["loginUsr"] = $_SESSION['loginUsr'];
                             break;
                          
                           case "Unauthorized": case "ldapConnKo":
                             $response["result"] = $result["detail"];
                             break;
                       }
                    }
                    else
                    {
                       $response["result"] = "missingParams";
                    }
                    break;
                    
                  case "logout":
                     if($restController->localLogout())
                     {
                        $response["result"] = "Ok";
                     }
                     else
                     {
                        $response["result"] = "Ko";
                     }
                     if(isset($_SESSION['refreshToken'])) {
                        $oidc = new OpenIDConnectClient(
                            'https://www.snap4city.org',
                            'php-notificator',
                            'ca155043-dd50-45be-ae5f-2b52d1d8445d'
                        );

                        $oidc->setVerifyHost(false);
                        $oidc->setVerifyPeer(false);

                        $oidc->providerConfigParam(array('authorization_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/auth'));
                        $oidc->providerConfigParam(array('token_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
                        $oidc->providerConfigParam(array('userinfo_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/userinfo'));
                        $oidc->providerConfigParam(array('jwks_uri'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/certs'));
                        $oidc->providerConfigParam(array('issuer'=>'https://www.snap4city.org/auth/realms/master'));
                        $oidc->providerConfigParam(array('end_session_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/logout'));
                        $tkn=$oidc->refreshToken($_SESSION['refreshToken']); 
                        //Dev'essere assoluto, visto con Piero
                        $oidc->signOut($tkn->access_token, "https://notificator.snap4city.org/notificator/ssoLogin.php");                        
                     }
                     if(ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                     $status = session_status();
                     break;
                     
                  case "remoteLogout":
                    if(isset($_REQUEST["usr"])&&isset($_REQUEST["clientApplication"])&&($_REQUEST["usr"]!== "")&&($_REQUEST["clientApplication"]!== ""))
                    {
                       $result = $restController->remoteLogout($_REQUEST["usr"], $_REQUEST["clientApplication"], $_SERVER['REMOTE_ADDR']);
                       $response["result"] = $result["detail"];
                    }
                    else
                    {
                       $response["result"] = "missingParams";
                    }
                      
                    /*if(isset($_REQUEST["app"])&&isset($_REQUEST["appUsr"]))
                    {
                        $result = $restController->remoteLogout($_REQUEST["app"], $_REQUEST["appUsr"]);

                        if($result)
                        {
                           $response["result"] = "Ok";
                        }
                        else
                        {
                           $response["result"] = "Ko";
                        }
                    }
                    else
                    {
                       $response["result"] = "missingParams";
                    }*/ 
                    break;   
                    
                  case "setCurrentPage":
                     if(isset($_REQUEST["page"]))
                     {
                        $result = $restController->setCurrentPage($_REQUEST['page']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                  
                  case "getCurrentPage":
                     $response["result"] = $restController->getCurrentPage();
                     break;
                    
                  case "getEmailAddresses":
                    $result = $restController->getEmailAddresses(); 
                     
                    switch($result)
                    {
                       case "queryKo":
                          $response["result"] = $result;
                          break;
                       
                       default:
                          $response["result"] = "Ok";
                          $response["addresses"] = $result;
                          break;
                    }
                    break;
                 
                  case "getEmailAccount":
                     if(isset($_REQUEST["id"]))
                     {
                        $result = $restController->getEmailAccount($_REQUEST['id']);
                        switch($result)
                        {
                           case "queryKo": case "noAccount":
                              $response["result"] = $result;
                              break;

                           default:
                              $response["result"] = "Ok";
                              $response["account"] = $result;
                              break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                 
                  case "insertEmailAddress":
                     if(isset($_REQUEST["adr"])&&isset($_REQUEST["fName"])&&isset($_REQUEST["lName"])&&($_REQUEST["org"] != ""))
                     {
                        $result = $restController->insertEmailAddress($_REQUEST['adr'], $_REQUEST['fName'], $_REQUEST['lName'], $_REQUEST['org']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                     
                  case "editEmailAccount":
                     if(isset($_REQUEST["id"])&&isset($_REQUEST["adr"])&&isset($_REQUEST["fName"])&&isset($_REQUEST["lName"])&&isset($_REQUEST["org"]))
                     {
                        $result = $restController->editEmailAccount($_REQUEST["id"], $_REQUEST['adr'], $_REQUEST['fName'], $_REQUEST['lName'], $_REQUEST['org']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;   
                     
                  case "deleteEmailAccount":
                     if(isset($_REQUEST["id"]))
                     {
                        $result = $restController->deleteEmailAccount($_REQUEST['id']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                     
                  case "getEmails":
                    $result = $restController->getEmails(); 
                     
                    switch($result)
                    {
                       case "queryKo":
                          $response["result"] = $result;
                          break;
                       
                       default:
                          $response["result"] = "Ok";
                          $response["emails"] = $result;
                          break;
                    }
                    break;
                 
                 
                 case "insertEmail":
                     if(isset($_REQUEST["sub"])&&isset($_REQUEST["txt"]))
                     {
                        $result = $restController->insertEmail($_REQUEST['sub'], $_REQUEST['txt']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                     
                  case "getEmail":
                     if(isset($_REQUEST["id"]))
                     {
                        $result = $restController->getEmail($_REQUEST['id']);
                        switch($result)
                        {
                           case "queryKo": case "noEmail":
                              $response["result"] = $result;
                              break;

                           default:
                              $response["result"] = "Ok";
                              $response["email"] = $result;
                              break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                     
                  case "editEmail":
                     if(isset($_REQUEST["id"])&&isset($_REQUEST["sub"])&&isset($_REQUEST["txt"]))
                     {
                        $result = $restController->editEmail($_REQUEST['id'], $_REQUEST['sub'], $_REQUEST['txt']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;   
                 
                 case "deleteEmail":
                     if(isset($_REQUEST["id"]))
                     {
                        $result = $restController->deleteEmail($_REQUEST['id']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                  
                  case "getEventGenerators":
                    $result = $restController->getEventGenerators($_SESSION['usrRole']); 
                     
                    switch($result)
                    {
                       case "queryKo": case "dbConnKo":
                          $response["result"] = $result;
                          break;
                       
                       default:
                          $response["result"] = "Ok";
                          $response["data"] = $result;
                          break;
                    }
                    break;
                 
                  case "getGeneratorEvents":
                     if(isset($_REQUEST["generatorId"]))
                     {
                        $result = $restController->getGeneratorEvents($_REQUEST['generatorId']);
                        switch($result)
                        {
                           case "queryKo": case "noEvents": case "dbConnKo":
                              $response["result"] = $result;
                              break;

                           default:
                              $response["result"] = "Ok";
                              $response["events"] = $result;
                              break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                    break;
                 
                 case "getNotifications":
                     if(isset($_REQUEST["genId"]))
                     {
                        $result = $restController->getNotifications($_REQUEST["genId"]); 
                     
                        switch($result)
                        {
                          case "queryKo":
                             $response["result"] = $result;
                             break;

                          default:
                             $response["result"] = "Ok";
                             $response["notifications"] = $result;
                             break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }   
                     break;
                     
                  case "delDmNotification":
                     if(isset($_REQUEST["id"]))
                     {
                        $result = $restController->delDmNotification($_REQUEST['id']);
                        $response["result"] = $result;
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                     
                  case "getEmailAddressesForNotificationForm":
                    $result = $restController->getEmailAddressesForNotificationForm(); 
                     
                    switch($result)
                    {
                       case "queryKo": case "noAddresses":
                          $response["result"] = $result;
                          break;
                       
                       default:
                          $response["result"] = "Ok";
                          $response["addresses"] = $result;
                          break;
                    }
                    break;   
                    
                 case "insertNotification":
                     if(isset($_REQUEST["genId"])&&isset($_REQUEST["eventId"])&&isset($_REQUEST["name"])&&isset($_REQUEST["val"])&&isset($_REQUEST["valStart"])&&isset($_REQUEST["valEnd"])&&isset($_REQUEST["recFromBook"])&&isset($_REQUEST["recManual"])
                        &&($_REQUEST["name"] !== "")&&($_REQUEST["val"] !== "")&&($_REQUEST["valStart"] !== "")&&($_REQUEST["valEnd"] !== ""))
                     {
                        $result = $restController->insertNotification($_REQUEST['name'], $_REQUEST["genId"], $_REQUEST["eventId"], $_REQUEST['val'], $_REQUEST['valStart'], $_REQUEST['valEnd'], $_REQUEST["recFromBook"], $_REQUEST["recManual"], $_REQUEST["msgId"], $_REQUEST["sub"], $_REQUEST["txt"]);
                        switch($result)
                        {
                           case "queryKo":
                             $response["result"] = "queryKo";
                             break;

                           default:
                             $response["result"] = "Ok";
                             $response["newRecFromBook"] = $result;
                             $response["newDataForSelect"] = $restController->getEmailAddressesForNotificationForm();
                             break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
                     
                  case "loadEditNotificationForm":
                     if(isset($_REQUEST["notificationId"])&&($_REQUEST["notificationId"])!= "")
                     {
                        $result = $restController->loadEditNotificationForm($_REQUEST["notificationId"]); 
                     
                        switch($result)
                        {
                           case "queryKo": case "noNotification":
                              $response["result"] = $result;
                              break;

                           default:
                              $response["result"] = "Ok";
                              $response["notification"] = $result;
                              break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;   
                     
                  case "editNotification":
                     if(isset($_REQUEST["id"])&&isset($_REQUEST["name"])&&isset($_REQUEST["val"])&&isset($_REQUEST["eventId"])&&isset($_REQUEST["valStart"])&&isset($_REQUEST["valEnd"])&&isset($_REQUEST["recFromBook"])&&isset($_REQUEST["recManual"])
                     &&($_REQUEST["name"] !== "")&&($_REQUEST["val"] !== "")&&($_REQUEST["valStart"] !== "")&&($_REQUEST["valEnd"] !== ""))
                     {
                        $result = $restController->editNotification($_REQUEST["id"], $_REQUEST['name'], $_REQUEST["eventId"], $_REQUEST['val'], $_REQUEST['valStart'], $_REQUEST['valEnd'], $_REQUEST["recFromBook"], $_REQUEST["recManual"], $_REQUEST["msgId"], $_REQUEST["sub"], $_REQUEST["txt"]);
                        switch($result)
                        {
                           case "queryKo":
                              $response["result"] = $result;
                              break;

                           default:
                              $response["result"] = "Ok";
                              $response["newRecFromBook"] = $result;
                              $response["newDataForSelect"] = $restController->getEmailAddressesForNotificationForm();
                              break;
                        }
                     }
                     else
                     {
                        $response["result"] = "missingParams";
                     }
                     break;
   
                    
                    case "notifyEvent":
                        if(isset($_REQUEST["appName"])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST['containerName'])&&isset($_REQUEST["eventType"])&&isset($_REQUEST["eventTime"]))
                        {  
                           if(isset($_REQUEST["value"]))
                           {
                              $value = $_REQUEST["value"];
                           }
                           else
                           {
                              $value = "x";
                           }
                           
                           if(isset($_REQUEST["furtherDetails"]))
                           {
                              $furtherDetails = $_REQUEST["furtherDetails"];
                           }
                           else
                           {
                              $furtherDetails = "x";
                           }
                           
                           $result = $restController->notifyEvent($_REQUEST["appName"], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST['containerName'], $_REQUEST["eventType"], $_REQUEST["eventTime"], $value, $furtherDetails); 
                            
                           switch($result)
                           {
                              case "queryKo": case "logKo":
                                 $response["result"] = $result;
                                 echo json_encode($response);
                                 break;

                              default:
                                 $response["result"] = $result["result"];
                                 echo json_encode($response);
                                 $restController->sendEmails($result["appName"], $result["generatorId"], $result["generatorOriginalName"], $result["generatorOriginalType"], $result["containerName"], $result["appUsr"], $result["url"], $result["eventTypeId"], $result["eventType"], $result["eventTime"], $result["value"], $furtherDetails);
                                 break;
                           }
                         }
                         else
                         {
                            $response["result"] = "missingParams";
                            echo json_encode($response);
                         }
                         exit();
                    break;
                    
                    
                    case "getEventsLogList":
                       if(isset($_REQUEST["startDate"])&&isset($_REQUEST["endDate"])&&isset($_REQUEST["appName"])&&isset($_REQUEST["searchText"]))
                       {
                           $result = $restController->getEventsLogList($_REQUEST["startDate"], $_REQUEST["endDate"], $_REQUEST["appName"], $_REQUEST["searchText"]);
                           switch($result)
                           {
                              case "queryKo":
                                 $response["result"] = $result;
                                 break;

                              default:
                                 $response["result"] = "Ok";
                                 $response["events"] = $result;
                                 break;
                           }
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;

                    case "setEventValidity":
                       if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["eventType"])&&isset($_REQUEST["validity"]))
                       {
                          $response["result"] = $restController->setEventValidity($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"], $_REQUEST["eventType"], $_REQUEST["validity"]);
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;
                       
                    case "updateEventType":
                       if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["oldEventType"])&&isset($_REQUEST["newEventType"]))
                       {
                          $response["result"] = $restController->updateEventType($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"], $_REQUEST["oldEventType"], $_REQUEST["newEventType"]);
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;
                       
                    case "deleteEventType":
                       
                       if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["eventType"]))
                       {
                          $response["result"] = $restController->deleteEventType($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"], $_REQUEST["eventType"]);
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;   

                    case "setGeneratorValidity":
                       /*if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorNewName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["validity"])&&isset($_REQUEST["setEventsValidityTrue"]))
                       {
                          $response["result"] = $restController->setGeneratorValidity($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorNewName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"], $_REQUEST["validity"], $_REQUEST["setEventsValidityTrue"]);
                       }*/
                       if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorNewName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"])&&isset($_REQUEST["validity"]))
                       {
                          $response["result"] = $restController->setGeneratorValidity($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorNewName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"], $_REQUEST["validity"]);
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;
                     
                     case "disableAllGeneratorEventTypes":
                        if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"]))
                        {
                            $response["result"] = $restController->disableAllGeneratorEventTypes($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"]);
                        }
                        else
                        {
                           $response["result"] = "missingParams";
                        }
                        break;  
                       
                     case "deleteAllGeneratorEventTypes":
                        if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"]))
                        {
                            $response["result"] = $restController->deleteAllGeneratorEventTypes($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"]);
                        }
                        else
                        {
                           $response["result"] = "missingParams";
                        }
                        break;
                        
                     case "deleteGenerator":
                       if(isset($_REQUEST['appName'])&&isset($_REQUEST["generatorOriginalName"])&&isset($_REQUEST["generatorOriginalType"])&&isset($_REQUEST["containerName"]))
                       {
                          $response["result"] = $restController->deleteGenerator($_REQUEST['appName'], $_REQUEST["generatorOriginalName"], $_REQUEST["generatorOriginalType"], $_REQUEST["containerName"]);
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;
                       
                     case "getNewEvents":
                        if(isset($_REQUEST['clientLastEventId'])&&isset($_REQUEST["clientLastEventTime"]))
                        {
                           $response = $restController->getNewEvents($_REQUEST["clientLastEventId"], $_REQUEST["clientLastEventTime"]);
                        }
                        else
                        {
                           $response["result"] = "missingParams";
                        }
                        break;
                        
                    case "updateContainerName":
                       if(isset($_REQUEST['appName'])&&isset($_REQUEST["oldContainerName"])&&isset($_REQUEST["newContainerName"]))
                       {
                          $response["result"] = $restController->updateContainerName($_REQUEST['appName'], $_REQUEST["oldContainerName"], $_REQUEST["newContainerName"]);
                       }
                       else
                       {
                          $response["result"] = "missingParams";
                       }
                       break;    
              }
            }
            else 
            {
              $response["result"] = "noOperation";
            }
         }
         else
         {
            $response["result"] = "Unauthorized";
         }
   }
   else 
   {
      $response["result"] = "apiPwd missing";
   }
}
else
{
   $response["result"] = "apiUsr missing";
}

echo json_encode($response);
