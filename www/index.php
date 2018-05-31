<?php
   /* Alarm Management System.
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
   ini_set('session.gc_maxlifetime', 7200);
   session_set_cookie_params(7200);
   session_start();
   session_regenerate_id();
   include './RestController.php';
   
   $appSettings = parse_ini_file("./conf/conf.ini");
?>

<!DOCTYPE html>
<html lang="en">
<head>    
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>DISIT Notificator</title>

   <!-- jQuery -->
   <script src="jquery/jquery-1.10.1.min.js"></script>
   
   <!-- Bootstrap -->
   <link href="bootstrap/bootstrap.css" rel="stylesheet">
   <script src="bootstrap/bootstrap.min.js"></script>
   
   <!-- Bootstrap table -->
   <link rel="stylesheet" href="bootstrap/table/dist/bootstrap-table.css">
   <script src="bootstrap/table/dist/bootstrap-table.js"></script>
   <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
   <script src="bootstrap/table/dist/locale/bootstrap-table-en-US.js"></script>
   
   <!-- Font awesome icons -->
   <link rel="stylesheet" href="fontAwesome/css/font-awesome.min.css">
   
   <!-- CKEditor -->
   <script src="ckeditor/ckeditor.js"></script>
   <!--<link rel="stylesheet" href="ckeditor/skins/moono/editor.css">-->
   
   <!-- CKEditor new -->
   <!--<script src="ckeditorNew/ckeditor.js"></script>
   <link rel="stylesheet" href="ckeditorNew/skins/bootstrapck/editor.css"> -->
   
   <!-- Select2 -->
   <link rel="stylesheet" href="select2/css/select2.min.css">
   <script src="select2/js/select2.min.js"></script>
   
   <!-- Bootstrap toggle button -->
   <link href="bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
   <script src="bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>
   
   <!-- Moment -->
   <script type="text/javascript" src="moment/moment.js"></script>
   
   <!-- Bootstrap datetime picker -->
   <script type="text/javascript" src="datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
   <link rel="stylesheet" href="datetimepicker/build/css/bootstrap-datetimepicker.min.css">

   <!-- Application CSSs -->
   <link href="css/main.css" rel="stylesheet">
   <link href="css/fonts.css" rel="stylesheet">
   <link href="css/login.css" rel="stylesheet">
   <link href="css/eventsManager.css" rel="stylesheet">
   <link href="css/emailBook.css" rel="stylesheet">
   <link href="css/emailAddressBook.css" rel="stylesheet">
   <link href="css/eventsLog.css" rel="stylesheet">
   
   <!-- Application JSs -->
   <script src="js/main.js"></script>
   <script src="js/login.js"></script>
   <script src="js/eventsManager.js"></script>
   <script src="js/emailBook.js"></script>
   <script src="js/emailAddressBook.js"></script>
   <script src="js/eventsLog.js"></script>
</head>

<body>
   <!-- Main header -->
   <div id="appHeader" class="container-fluid">
      <span id="headerSpan"></span>
   </div>

   <!-- Main menu -->
   <div id="mainMenu" class="container-fluid">
      <div class="mainMenuItemContainer">
         <a id="eventsManagerBtn" href="#">
            <div class="mainMenuIconContainer">
               <img src="img/mainMenu/eventsManagement.png" width="40" height="40">
            </div>
            <div class="mainMenuTextContainer">
               events generators
            </div>
         </a>
      </div>
      <div class="mainMenuItemContainer">
         <a id="emailBookBtn" href="#">
            <div class="mainMenuIconContainer">
               <img src="img/mainMenu/emailBook2.png" width="40" height="40">
            </div>
            <div class="mainMenuTextContainer">
               message book
            </div>
         </a>
      </div>
      <div class="mainMenuItemContainer">
         <a id="emailAddressBookBtn" href="#">
            <div class="mainMenuIconContainer">
               <img src="img/mainMenu/book2.png" width="40" height="40">
            </div>
            <div class="mainMenuTextContainer">
               address book
            </div>
         </a>
      </div>
      <div class="mainMenuItemContainer">
         <a id="restBookBtn" href="#">
            <div class="mainMenuIconContainer">
               <img src="img/mainMenu/rest2.png" width="40" height="40">
            </div>
            <div class="mainMenuTextContainer">
               rest<br/>book
            </div>
         </a>
      </div>
      <div class="mainMenuItemContainer">
         <a id="eventsLogBtn" href="#">
            <div class="mainMenuIconContainer">
               <img src="img/mainMenu/log2.png" width="40" height="40">
            </div>
            <div class="mainMenuTextContainer">
               events<br/>log
            </div>
         </a>
      </div>
      <div class="mainMenuItemContainer">
         <a id="applicationsBtn" href="#">
            <div class="mainMenuIconContainer">
               <img src="img/mainMenu/applications2.png" width="40" height="40">
            </div>
            <div class="mainMenuTextContainer">
               client applications
            </div>
         </a>
      </div>
   </div>
    
    <!-- Main content -->
    <div id="mainContainer">
       
    </div>
    
    <!-- Footer -->
    <div id="footer" class="container-fluid">
      <?php if(!isset($_SESSION['refreshToken'])) { ?>
      <div id="logoutBtn" class="footerBoxContainer">
         <div  class="footerUsrIcon">
            <img src="img/footer/Logout.png">
         </div>
         <div class="footerUsrText">
           &nbsp;Logout
         </div>
      </div> 
      <?php } ?>
      <div data-toggle="tooltip" title="Username you are currently logged in with" class="footerBoxContainer">
         <div class="footerUsrIcon">
            <img src="img/footer/LoggedUser.png">
         </div>
         <div id="loggedUsr" class="footerUsrText">
           <?php 
              if(isset($_SESSION['loginUsr']))
              {
                 echo $_SESSION['loginUsr'];
              }
           ?>
         </div>
         <input type="hidden" id="loggedUsrHidden" value="<?php if(isset($_SESSION['loginUsr'])){ echo $_SESSION['loginUsr']; }?>"/>
      </div>
      <div data-toggle="tooltip" title="Application your account belongs to" class="footerBoxContainer">
         <div class="footerUsrIcon">
            <img class="app" src="img/footer/application.png">
         </div>
         <div id="loginApp" class="footerUsrText">
           <?php 
              if(isset($_SESSION['loginApp']))
              {
                 echo $_SESSION['loginApp'];
              }
           ?>
         </div>
         <input type="hidden" id="loginAppHidden" value="<?php if(isset($_SESSION['loginAppLdap'])){ echo $_SESSION['loginAppLdap']; }?>" />
         <input type="hidden" id="clientLastEvent" />
         <input type="hidden" id="updateLastEventLock" />
      </div>
       <div data-toggle="tooltip" title="Role attributed to your account" class="footerBoxContainer">
         <div class="footerUsrIcon">
            <img src="img/footer/Key.png">
         </div>
         <div id="usrRole" class="footerUsrText">
           <?php 
              if(isset($_SESSION['usrRole']))
              {
                 echo $_SESSION['usrRole'];
              }
           ?>
         </div>
      </div>
       <div data-toggle="tooltip" title="Enable/disable real time events popups" id="footerNotifContainer">
         <div id="footerNotifBtnContainer">
            <input type="checkbox" id="footerNotifBtn" checked>
         </div>
         <div id="footerNotifTxt" class="footerUsrText">
           RT events
         </div>
      </div>
      <div id="footerBoxLastContainer">
         Notification management system&nbsp;-&nbsp;<a href="http://www.disit.org" target="blank" id="disitLink">DISIT Lab</a>&nbsp;2017
      </div>
    </div>    
   
   <script type='text/javascript'>
      $(document).ready(function () 
      {
         var clientLastEvent = {
            id: null,
            time: null,
            type: null,
            appName: null,
            appUsr: null,
            genName: null,
            genType: null,
            genContainer: null,
            url: null
         };
         
         $("#updateLastEventLock").val("true");
         $("#clientLastEvent").val(JSON.stringify(clientLastEvent));
         $("#updateLastEventLock").val("false");
         
         var newEventsPopupTop = $(window).height() + 200;
         //var newEventsPopupLeft = $(window).width() - 420;
         var newEventsPopupLeft = 0;
         
         var newEvents = [];
         var loggedIn = false;
         
         var getNewEventsInterval = null;
         
         $('#footerNotifBtn').bootstrapToggle({
            on: 'On',
            off: 'Off',
            onstyle: 'info',
            offstyle: 'danger',
            size: 'medium'
        });
         
         function indexWindowResize()
         {
            if($("#mainMenu").is(":visible"))
            {
               $("#mainContainer").height($(window).height() - $("#appHeader").height() - $("#mainMenu").height() - $("#footer").height());
            }
            else
            {
               $("#mainContainer").height($(window).height() - $("#appHeader").height() - $("#footer").height());
            }

            var paddingLeft = parseInt(($(window).width() - 900)/2);
            $("#mainMenu").css("padding-left", paddingLeft);
            var footerBoxLastContainerWidth = $(window).width() - 720;
            $("#footerBoxLastContainer").css("width", footerBoxLastContainerWidth);

            newEventsPopupTop = $(window).height() + 200;
            newEventsPopupLeft = 0;
            $("#newEventsPopup").css("top", newEventsPopupTop + "px");
            $("#newEventsPopup").css("left", newEventsPopupLeft + "px");
         }
         
         $('[data-toggle="tooltip"]').tooltip();
         $("#mainContainer").height($(window).height() - $("#appHeader").height() - $("#footer").height());
         $(window).off("resize", indexWindowResize);  
         $(window).resize(indexWindowResize);
         
        <?php
            if(isset($_REQUEST['usr'])&&isset($_REQUEST['showAlerts'])){
                $restController = new RestController();
                $result = $restController->autoLoginFromRemote($_REQUEST['usr'], $_REQUEST['showAlerts']);
                
                if($result['detail'] == "Ok")
                {
                    $_SESSION['loginType'] = $result['usrOrigin'];
                    $_SESSION['loginApp'] = $result['usrApp'];
                    $_SESSION['loginAppLdap'] = $result["usrAppLdap"];
                    $_SESSION['loginUsr'] = $result['username'];
                    $_SESSION["usrOrigin"] = $result["usrOrigin"];
                    $_SESSION["usrRole"] = $result["usrRole"]; 
                
        ?>
               $("#loginLoadingIcon").hide();
               $("#loginMsgRow").hide();
               $("#loginBtnRow").show();
               $("#mainContainer").css("opacity", 0.0);
               $("#headerSpan").css("opacity", 0.0);
                
               setTimeout(function(){
                  showLoggedUserModule();
                  $("#loggedUsrHidden").html("<?php echo $_SESSION['loginUsr']; ?>");
                  $("#loginAppHidden").html("<?php echo $_SESSION['loginApp']; ?>");
                  showMainMenu();
                  showGeneratorAlerts(/*Per ora non usato*/"loginPage", '<?php echo $result['generatorId'] ; ?>');
               }, 500);
        <?php
                }
                else
                {
                    if(ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                }
            } 
        ?>        
         
         switch(getCurrentPage())
         {
            case "login":
               showLoginModule();
               loggedIn = false;
               break;
               
            case "eventsManager":
               showLoggedUserModule();
               showMainMenu();
               showEventsManagerModule("mainPage");
               loggedIn = true;
               break;      
               
            case "emailBook":
               showLoggedUserModule();
               showMainMenu();
               showEmailBookModule();
               loggedIn = true;
               break;   
               
            case "emailAddressBook":
               showLoggedUserModule();
               showMainMenu();
               showEmailAddressBookModule();
               loggedIn = true;
               break;
               
            case "eventsLog":
               showLoggedUserModule();
               showMainMenu();
               showEventsLogModule();
               loggedIn = true;
               break;  
               
            //TBD - AGGIUNGI GLI ALTRI CASE VIA VIA CHE LI IMPLEMENTI   
         }
         
         setIndexGlobals(loggedIn);
        
         if((clientLastEvent.id === null)&&(clientLastEvent.time === null))
         {
            $.ajax({
               url: "<?php echo $appSettings['selfRestApiUrl']; ?>",
               data: 
               {
                  apiUsr: "alarmManager",
                  apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
                  operation: "getNewEvents",
                  clientLastEventId: clientLastEvent.id,
                  clientLastEventTime: clientLastEvent.time
               },
               type: "POST",
               async: true,
               dataType: 'json',
               success: function (data) 
               {
                  if(data.loggedIn === "yes")
                  {
                     if(data.result === "Ok")
                     {
                        if(data.detail === "firstCall")
                        {
                           if(data.resultNumber > 0)
                           {
                              $("#filterEventsLogTableBtn").prop("disabled", true);
                              $("#updateLastEventLock").val("true");
                              clientLastEvent = data.updatedLastEvent;
                              $("#clientLastEvent").val(JSON.stringify(clientLastEvent));
                              $("#updateLastEventLock").val("false");
                              $("#filterEventsLogTableBtn").prop("disabled", false);
                           }
                           else
                           {
                              clientLastEvent.id = 0;
                              clientLastEvent.time = 0;
                              console.log("getNewEvents first call OK, no events");
                           }
                        }
                     }
                     else
                     {
                        clientLastEvent.id = 0;
                        clientLastEvent.time = 0;
                        console.log("getNewEvents first call OK, but query KO");
                        console.log(JSON.stringify(data));
                     }
                  }
                  else
                  {
                     clientLastEvent.id = 0;
                     clientLastEvent.time = 0;
                  }
               },
               error: function (data)
               {
                  clientLastEvent.id = 0;
                  clientLastEvent.time = 0;
                  console.log("getNewEvents first call KO");
                  console.log(JSON.stringify(data));
               }
            });
         }

         getNewEventsInterval = setInterval(function(){
            $.ajax({
               url: "<?php echo $appSettings['selfRestApiUrl']; ?>",
               data: 
               {
                  apiUsr: "alarmManager",
                  apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
                  operation: "getNewEvents",
                  clientLastEventId: clientLastEvent.id,
                  clientLastEventTime: clientLastEvent.time
               },
               type: "POST",
               async: true,
               dataType: 'json',
               success: function (data) 
               {
                  if(data.loggedIn === "yes")
                  {
                     if((data.result === "Ok") && (data.detail === "standardCall"))
                     {
                        newEvents = data.newEvents;
                        if(newEvents.length > 0)
                        {
                           console.log("New events");
                           if($("#updateLastEventLock").val() === "false")
                           {
                              $("#filterEventsLogTableBtn").prop("disabled", true);
                              $("#updateLastEventLock").val("true");
                              clientLastEvent = newEvents[0];
                              $("#clientLastEvent").val(JSON.stringify(clientLastEvent));
                              $("#updateLastEventLock").val("false");
                              $("#filterEventsLogTableBtn").prop("disabled", false);

                              var newRow, genNameLbl, eventTypeLbl;

                              $("#newEventsPopupBodyContent").empty();

                              if(newEvents.length > 5)
                              {
                                 for(var i = 0; i < 4; i++)
                                 {
                                    //Modo rapido (ma non elegante) per vedere se siamo nella pagina eventsLog senza fare chiamate a getCurrentPage()
                                    if($("#headerSpan").html() === "Events log")
                                    {
                                       //Se filtro non attivo aggiorniamo la tabella, altrimenti mostriamo solo i popup
                                       if($("#filterActive").val() === "false")
                                       {
                                          $('#eventsLogTable').bootstrapTable('selectPage', 1);

                                          $('#eventsLogTable').bootstrapTable('prepend', {
                                             //id: ,
                                             time: newEvents[newEvents.length - i - 1].time,
                                             type: newEvents[newEvents.length - i - 1].type,
                                             appName: newEvents[newEvents.length - i - 1].appName,
                                             appUsr: newEvents[newEvents.length - i - 1].appUsr,
                                             genName: newEvents[newEvents.length - i - 1].genName,
                                             genType: newEvents[newEvents.length - i - 1].genType,
                                             genContainer: newEvents[newEvents.length - i - 1].genContainer,
                                             url: newEvents[newEvents.length - i - 1].url
                                          });
                                       }
                                    }

                                    if(newEvents[i].genName.length > 19)
                                    {
                                       genNameLbl = newEvents[i].genName.substring(0, 16) + "..."; 
                                    }
                                    else
                                    {
                                       genNameLbl = newEvents[i].genName;
                                    }

                                    if(newEvents[i].type.length > 19)
                                    {
                                       eventTypeLbl = newEvents[i].type.substring(0, 16) + "..."; 
                                    }
                                    else
                                    {
                                       eventTypeLbl = newEvents[i].type;
                                    }

                                    if(i%2 === 0)
                                    {
                                       newRow = $('<div class="newEventsPopupBodyRow"><div class="newEventsPopupBodyCellOdd centerWithFlex">' + genNameLbl + '</div><div class="newEventsPopupBodyCellOdd centerWithFlex">' + eventTypeLbl + '</div><div class="newEventsPopupBodyCellOdd centerWithFlex">' + newEvents[i].eventTime + '</div></div>');
                                    }
                                    else
                                    {
                                       newRow = $('<div class="newEventsPopupBodyRow"><div class="newEventsPopupBodyCellEven centerWithFlex">' + genNameLbl + '</div><div class="newEventsPopupBodyCellEven centerWithFlex">' + eventTypeLbl + '</div><div class="newEventsPopupBodyCellEven centerWithFlex">' + newEvents[i].eventTime + '</div></div>');
                                    }

                                    $("#newEventsPopupBodyContent").append(newRow);
                                 }
                                 //Append ultima riga coi puntini
                                 newRow = $('<div class="newEventsPopupBodyRow centerWithFlex">Further events...</div>');
                                 $("#newEventsPopupBodyContent").append(newRow);
                              }
                              else
                              {
                                 for(var i = 0; i < newEvents.length; i++)
                                 {
                                    //Modo rapido (ma non elegante) per vedere se siamo nella pagina eventsLog senza fare chiamate a getCurrentPage()
                                    if($("#headerSpan").html() === "Events log")
                                    {
                                       //Se filtro non attivo aggiorniamo la tabella, altrimenti mostriamo solo i popup
                                       if($("#filterActive").val() === "false")
                                       {
                                          $('#eventsLogTable').bootstrapTable('selectPage', 1);

                                          $('#eventsLogTable').bootstrapTable('prepend', {
                                             time: newEvents[newEvents.length - i - 1].time,
                                             type: newEvents[newEvents.length - i - 1].type,
                                             appName: newEvents[newEvents.length - i - 1].appName,
                                             appUsr: newEvents[newEvents.length - i - 1].appUsr,
                                             genName: newEvents[newEvents.length - i - 1].genName,
                                             genType: newEvents[newEvents.length - i - 1].genType,
                                             genContainer: newEvents[newEvents.length - i - 1].genContainer,
                                             url: newEvents[newEvents.length - i - 1].url
                                          }); 
                                       }
                                    }

                                    if(newEvents[i].genName.length > 19)
                                    {
                                       genNameLbl = newEvents[i].genName.substring(0, 16) + "..."; 
                                    }
                                    else
                                    {
                                       genNameLbl = newEvents[i].genName;
                                    }

                                    if(newEvents[i].type.length > 19)
                                    {
                                       eventTypeLbl = newEvents[i].type.substring(0, 16) + "..."; 
                                    }
                                    else
                                    {
                                       eventTypeLbl = newEvents[i].type;
                                    }

                                    if(i%2 === 0)
                                    {
                                       newRow = $('<div class="newEventsPopupBodyRow"><div class="newEventsPopupBodyCellOdd centerWithFlex">' + genNameLbl + '</div><div class="newEventsPopupBodyCellOdd centerWithFlex">' + eventTypeLbl + '</div><div class="newEventsPopupBodyCellOdd centerWithFlex">' + newEvents[i].eventTime + '</div></div>');
                                    }
                                    else
                                    {
                                       newRow = $('<div class="newEventsPopupBodyRow"><div class="newEventsPopupBodyCellEven centerWithFlex">' + genNameLbl + '</div><div class="newEventsPopupBodyCellEven centerWithFlex">' + eventTypeLbl + '</div><div class="newEventsPopupBodyCellEven centerWithFlex">' + newEvents[i].eventTime + '</div></div>');
                                    }
                                    $("#newEventsPopupBodyContent").append(newRow);
                                 }
                              }

                              if($("#filterActive").val() === "false")
                              {
                                 for(var i = 0; i < newEvents.length; i++)
                                 {
                                    $('#eventsLogTable tbody tr').eq(i).find("td").each(function(){
                                       $(this).addClass("newEventsLogTableRow");
                                    });
                                 }

                                 setTimeout(function(){
                                    for(var i = 0; i < newEvents.length; i++)
                                    {
                                       $('#eventsLogTable tbody tr').eq(i).find("td").each(function(){
                                          $(this).removeClass("newEventsLogTableRow");
                                       });
                                    }
                                 }, 4000);
                              }

                              if($("#footerNotifBtn").parent().hasClass("btn-info")&&(!$("#footerNotifBtn").parent().hasClass("off")))
                              {
                                 newEventsPopupTop = $(window).height() - $("#newEventsPopup").outerHeight() - $("#footer").height();
                                 $("#newEventsPopup").css("top", newEventsPopupTop + "px");

                                 setTimeout(function(){
                                    newEventsPopupTop = $(window).height() + $("#newEventsPopup").outerHeight();
                                    $("#newEventsPopup").css("top", newEventsPopupTop + "px");
                                 }, 4000);
                              }
                           }
                           else
                           {
                              console.log("getNewEvents OK - Standard call - updateLastEventLock attivo");  
                           }
                        }
                        else
                        {
                           console.log("getNewEvents OK - Standard call - No new events");                     
                        }
                     }
                     else
                     {
                        console.log("getNewEvents standard call OK, but query KO");
                        console.log(JSON.stringify(data));
                     }
                  }
                  else
                  {
                     console.log("getNewEvents OK - Standard call - not logged in");  
                  }
               },
               error: function (data)
               {
                  console.log("getNewEvents standard call KO");
                  console.log(JSON.stringify(data));
               }
            });
         }, 8000);
         
         
         //Lasciarlo commentato finché non troviamo una soluzione per il login automatico
         /*setInterval(function(){
            $.ajax({
               url: "<?php echo $appSettings['selfRestApiUrl']; ?>",
               data: 
               {
                  apiUsr: "alarmManager",
                  apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
                  operation: "getRemoteLoginStatus"
               },
               type: "POST",
               async: true,
               dataType: 'json',
               success: function (data) 
               {
                  console.log("Usr locally: " + $("#loggedUsrHidden").val() + " - App: " + $("#loginAppHidden").val() + " - Logged status: " + data.result + " - User from api: " + data.loginUsr);
                  if((data.result === "Not logged")&&loggedIn)
                  {
                     $.ajax({
                        url: "<?php echo $appSettings['selfRestApiUrl']; ?>",
                        data: {
                           apiUsr: "alarmManager",
                           apiPwd: "d0c26091b8c8d4c42c02085ff33545c1", //MD5
                           operation: "remoteLogout",
                           app: $("#loginAppHidden").val(),
                           appUsr: $("#loggedUsrHidden").val()
                        },
                        type: "POST",
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                           loggedIn = false;
                           $("#headerSpan").css("opacity", 0.0);
                           $("#mainContainer").css("opacity", 0.0);
                           setTimeout(function(){
                             hideMainMenu();
                             hideLoggedUserModule();
                             showLoginModule();
                             $("#headerSpan").html("Notification management system");
                           }, 300);
                        },
                        error: function (data)
                        {
                           console.log("Error in remote logout");
                           console.log(data);
                        }
                     });
                  }
               },
               error: function (data)
               {
                  console.log("getRemoteLoginStatus KO");
                  console.log(JSON.stringify(data));
               }
               });
         }, 1500);*/
         
      });
   </script>
</body>
