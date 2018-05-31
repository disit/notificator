<?php
/* Notifications Management System.
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
   session_start();
?>

<div class="row" id="eventsManagerContainer">
   <div id="mainLoading" class="col-sm-10 col-sm-offset-1">
      <div class="col-sm-12 centerWithFlex loadingMsg">Loading generators, please wait</div>
      <div class="col-sm-12 centerWithFlex loadingSpin"><i class="fa fa-circle-o-notch fa-spin"></i></div>
   </div>
   
   <?php
      if($_SESSION['usrRole'] == "ToolAdmin")
      {
         echo '<div id="eventsManagerAppMenuContainer" class="col-sm-10 col-sm-offset-1">';
            echo '<div id="eventsManagerAppMenuLabel" class="col-sm-4 col-sm-offset-4 centerWithFlex">';
               echo 'Application:&nbsp;&nbsp;&nbsp;';
               echo '<select data-toggle="tooltip" data-container="body" title="Select the application for which you want to see events generators" id="eventsManagerAppSelect">';
                  $conf = parse_ini_file("../conf/conf.ini");
                  $link = mysqli_connect($conf["dbHost"], $conf["dbUsr"], $conf["dbPwd"]);
                  mysqli_set_charset($link, 'utf8');
                  mysqli_select_db($link, $conf["dbName"]);

                  $query = "SELECT * FROM " . $conf["dbName"] . ".clientApplications";
                  $rs = mysqli_query($link, $query);

                  if($rs)
                  {
                     while($row = mysqli_fetch_assoc($rs))
                     {
                        echo '<option value="' . $row['ldapName'] . '">' . $row['name'] . '</option>';
                     }
                  }
               echo '</select>';
            echo '</div>';
         echo '</div>';
      }
   ?>            
   <div id="dmEventsManagerTableContainer" class="col-sm-10 col-sm-offset-1">
      <table id="dmEventsManagerTable" class="mainContainerTable">
          <thead>
              <tr>
              </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
   </div>
   <div id="eventsManagerMsg" class="col-sm-12 centerWithFlex"></div>
   
   <div id="newEventsPopup">
      <div id="newEventsPopupHeader" class="centerWithFlex">
         New events occurred
      </div>
      <div id="newEventsPopupBody">
         <div id="newEventsPopupBodyHeader">
            <div class="newEventsPopupBodyHeaderCell centerWithFlex">
               Generator
            </div>
            <div class="newEventsPopupBodyHeaderCell centerWithFlex">
               Event
            </div>
            <div class="newEventsPopupBodyHeaderCell centerWithFlex">
               Time
            </div>
         </div>
         <div id="newEventsPopupBodyContent">
            
         </div>
      </div>
   </div>
</div>

<!-- Modale di gestione eventi -->
<div class="modal fade" id="mngDmEventsModal" tabindex="-1" role="dialog" aria-labelledby="mngEventsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="mngDmEventsModalLabel">Add/edit/delete notifications</h5>
        </div>
        <div class="modal-body">
           <div id="mngDmEventsModalBody">
              <div class="row mngDmEventsModalBodyRow">
                 <div id="mngDmEventsModalRuleRecap" class="col-sm-12">
                    <div class="row">
                       <div class="centerWithFlex ruleRecapLbl"></div>
                       <div class="centerWithFlex ruleRecapLbl"></div>
                       <div class="centerWithFlex ruleRecapLbl"></div>
                       <div class="centerWithFlex ruleRecapLbl"></div>
                       <div class="centerWithFlex ruleRecapLbl"></div>
                    </div>
                    <div class="row">
                       <div class="centerWithFlex ruleRecapCnt"></div>
                       <div class="centerWithFlex ruleRecapCnt"></div>
                       <div class="centerWithFlex ruleRecapCnt"></div>
                       <div class="centerWithFlex ruleRecapCnt"></div>
                       <div class="centerWithFlex ruleRecapCnt ruleRecapLink"></div>
                    </div>
                 </div>
              </div>
              <div class="row mngDmEventsModalBodyRow">
                 <div class="col-sm-10 col-sm-offset-1">
                     <!--<ul class="nav nav-pills">
                        <li class="active"><a href="#" data-toggle="pill">E-Mail notifications</a></li>
                        <li><a href="#" data-toggle="pill">REST notifications</a></li>
                     </ul>-->
                    <!--<div class="row">
                       <div class="pull-right centerWithFlex" id="addNotificationBtn" data-toggle="tooltip" title="Add a new notification for this generator: this will associate one event type with a message and one or more recipients"><img src="img/emailBook/plusYellow.png" width="32px" height="32px" /></div>
                    </div>-->
                    <div class="row">
                     <div id="mngDmEventsModalTableContainer" class="col-sm-12">
                        <table id="dmEmailNotificationsTable">
                           <thead>
                               <tr></tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                        <div id="dmEmailNotificationsMsg" class="col-sm-12 centerWithFlex"></div>
                     </div>
                    </div>   
                 </div>
              </div>
           </div>
           <input type="hidden" id="genId" />
        </div>
        <div class="modal-footer">
          <button type="button" id="mngDmEventsModalCloseBtn" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di inserimento nuova notifica -->
<div class="modal fade" id="addEmailNotificationDmModal" tabindex="-1" role="dialog" aria-labelledby="addEmailNotificationDmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEmailNotificationDmModalLabel">Add e-mail notification</h5>
        </div>
        <div class="modal-body">
           <div id="addEmailNotificationDmModalBody">
              <div class="row">
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Notification name</div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Active</div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Validity start</div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Validity end</div>
              </div>
              <div class="row">
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input class="form-control" type="text" id="addDmEmailNotificationName" data-toggle="tooltip" title="Add a name of your choice for the notification: this will have no effect on the e-mails sent"/>
                 </div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="checkbox" id="addDmEmailNotificationVal" checked>
                 </div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <div class="form-group">
                        <div class='input-group date' data-toggle="tooltip" title="E-mails will be sent if an event of the choosen type occurs after start time" id='addDmEmailNotificationValStart'>
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                 </div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <div class="form-group">
                        <div class='input-group date' data-toggle="tooltip" title="E-mails will be sent if an event of the choosen type occurs before end time" id='addDmEmailNotificationValEnd'>
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                 </div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Recipients from address book</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Custom recipients</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <select id="addDmEmailNotificationRecFromBook" data-toggle="tooltip" title="Click on this field and add/delete one or more recipients with single clicks" style="width: 100%"></select>
                    <div id="addDmEmailNotificationRecFromBookMsg" style="width: 100%"></div>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="addDmEmailNotificationRecManual" data-toggle="tooltip" title="Add one or more recipients (separated by spaces or commas) that are not in the address book: these recipients will be automatically added to address book" class="form-control" style="width: 100%"></select>
                 </div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Message source</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Event type</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="checkbox" id="addDmEmailNotificationMsgSrc" checked>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <select id="addDmEmailNotificationEventSelect" class="form-control"></select>
                 </div>           
              </div>
              <div class="row">
                 <div class="col-sm-12">
                    <!-- Ne viene mostrato solo una alla volta -->
                    <div id="addDmEmailNotificationEmailBookTableContainer" class="col-sm-12">
                       <table id="addDmEmailNotificationEmailBookTable">
                        <thead>
                            <tr>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                    <div class="col-sm-12" id="addDmEmailNotificationEmailBookMsg centerWithFlex"></div>
                    <div id="addDmEmailNotificationMsgComposerContainer" class="col-sm-12">
                        <div class="col-sm-12 centerWithFlex addEmailNotificationDmModalBodyLbl">Message subject</div>
                        <div class="col-sm-12">If you leave the subject blank or filled with the <span style='background-color: yellow'>[[Auto]]</span> placeholder, the system will generate the following automatic subject: Application name - Container title - Generator name - Generator type</div>
                        <div class="col-sm-12 centerWithFlex addEmailNotificationDmModalBodyField">
                           <input type="text" id="addDmEmailNotificationMsgComposerSub" class="form-control" value="[[Auto]]"/>
                        </div>
                        <div class="col-sm-12 centerWithFlex addEmailNotificationDmModalBodyLbl">Message body</div>
                        <div class="col-sm-12">You can add event details automatically selecting the <span style='background-color: yellow'>[[EventDetails]]</span> placeholder from the editor menu or adding it manually to message body. If the placeholder isn't added event details will be automatically appended at the end of message body.</div>
                        <div class="col-sm-12 centerWithFlex">
                           <textarea id="addDmEmailNotificationMsgComposerTxt" rows="10" cols="10"></textarea> <!-- Questa textarea verrà rimpiazzata dal CKEDITOR -->
                        </div>
                    </div>
                 </div>
              </div>
           </div>
           
           <input type="hidden" id="selectedMsgId" />
           
           <div id="addDmEmailNotificationModalOk1" class="modalBodyInnerDiv">New notification successfully inserted</div>
           <div id="addDmEmailNotificationModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>

           <div id="addDmEmailNotificationModalKo1" class="modalBodyInnerDiv"></div>
           <div id="addDmEmailNotificationModalKo2" class="modalBodyInnerDiv" style="display: none"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
           
        </div>
        <div class="modal-footer">
          <button type="button" id="addDmEmailNotificationCancelBtn" class="btn btn-secondary">Abort</button>
          <button type="button" id="addDmEmailNotificationConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di avviso inserimento nuova notifica non possibile -->
<div class="modal fade" id="addDmEmailNotificationImpossibleModal" tabindex="-1" role="dialog" aria-labelledby="addDmEmailNotificationImpossibleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addDmEmailNotificationImpossibleModalLabel">Add e-mail notification</h5>
        </div>
        <div id="addDmEmailNotificationImpossibleModalBody" class="modal-body"></div>
      </div>
    </div>
</div>

<!-- Modale di modifica notifica -->
<div class="modal fade" id="editEmailNotificationDmModal" tabindex="-1" role="dialog" aria-labelledby="editEmailNotificationDmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEmailNotificationDmModalLabel">Edit e-mail notification</h5>
        </div>
        <div class="modal-body">
           <div id="editEmailNotificationDmModalBody">
              <div class="row">
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Notification name</div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Active</div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Validity start</div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyLbl">Validity end</div>
              </div>
              <div class="row">
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input class="form-control" type="text" id="editDmEmailNotificationName" />
                 </div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="checkbox" id="editDmEmailNotificationVal" checked>
                 </div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <div class="form-group">
                        <div class='input-group date' id='editDmEmailNotificationValStart'>
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                 </div>
                 <div class="col-sm-3 centerWithFlex addEmailNotificationDmModalBodyField">
                    <div class="form-group">
                        <div class='input-group date' id='editDmEmailNotificationValEnd'>
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                 </div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Recipients from address book</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Custom recipients</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <select id="editDmEmailNotificationRecFromBook" style="width: 100%"></select>
                    <div id="editDmEmailNotificationRecFromBookMsg" style="width: 100%"></div>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="editDmEmailNotificationRecManual" class="form-control" style="width: 100%"></select>
                 </div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Message source</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Event type</div>
              </div>
              
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="checkbox" id="editDmEmailNotificationMsgSrc" checked>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <select id="editDmEmailNotificationEventSelect" class="form-control"></select>
                 </div>
              </div>
              
              <input type="hidden" id="selectedMsgIdEdit" name="selectedMsgIdEdit" />
              
              <div class="row">
                 <div class="col-sm-12">
                    <!-- Ne viene mostrato solo una alla volta -->
                    <div id="editDmEmailNotificationEmailBookTableContainer" class="col-sm-12">
                       <table id="editDmEmailNotificationEmailBookTable">
                        <thead>
                            <tr>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                    <div class="col-sm-12" id="editDmEmailNotificationEmailBookMsg centerWithFlex"></div>
                    <div id="editDmEmailNotificationMsgComposerContainer" class="col-sm-12">
                        <div class="col-sm-12 centerWithFlex">Message subject</div>
                        <div class="col-sm-12">If you leave the subject blank or filled with the <span style='background-color: yellow'>[[Auto]]</span> placeholder, the system will generate the following automatic subject: Application name - Container title - Generator name - Generator type</div>
                        <div class="col-sm-12 centerWithFlex">
                           <input type="text" id="editDmEmailNotificationMsgComposerSub" value="[[Auto]]"/>
                        </div>
                        <div class="col-sm-12 centerWithFlex">Message body</div>
                        <div class="col-sm-12">You can add event details automatically selecting the <span style='background-color: yellow'>[[EventDetails]]</span> placeholder from the editor menu or adding it manually to message body. If the placeholder isn't added event details will be automatically appended at the end of message body</div>
                        <div class="col-sm-12 centerWithFlex">
                           <textarea id="editDmEmailNotificationMsgComposerTxt" rows="10" cols="10"></textarea> <!-- Questa textarea verrà rimpiazzata dal CKEDITOR -->
                        </div>
                    </div>
                 </div>
              </div>
           </div>
           
           <input type="hidden" id="editDmEmailNotificationId" />
           
           <div id="editDmEmailNotificationModalOk1" class="modalBodyInnerDiv">Notification successfully edited</div>
           <div id="editDmEmailNotificationModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>

           <div id="editDmEmailNotificationModalKo1" class="modalBodyInnerDiv"></div>
           <div id="editDmEmailNotificationModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
           
           <div id="editDmEmailNotificationModalKo3" class="modalBodyInnerDiv"></div>
           <div id="editDmEmailNotificationModalKo4" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
           
        </div>
        <div class="modal-footer">
          <button type="button" id="editDmEmailNotificationCancelBtn" class="btn btn-secondary">Back</button>
          <button type="button" id="editDmEmailNotificationConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di cancellazione notifica -->
<div class="modal fade" id="delDmNotificationModal" tabindex="-1" role="dialog" aria-labelledby="delDmNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delDmNotificationModalLabel">Notification deletion</h5>
        </div>
        <div id="delDmNotificationModalBody1" class="modal-body centerWithFlex">You are about to delete the following notification:</div>
        <div id="delDmNotificationModalBody2" class="modal-body centerWithFlex"></div>
        
         <div id="delDmNotificationModalOk1" class="modalBodyInnerDiv">Notification successfully deleted</div>
         <div id="delDmNotificationModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>

         <div id="delDmNotificationModalKo1" class="modalBodyInnerDiv"></div>
         <div id="delDmNotificationModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        
        <input type="hidden" id="delDmNotificationId" />
        <input type="hidden" id="delDmNotificationName" />
        <div class="modal-footer">
          <button type="button" id="delDmNotificationCancelBtn" class="btn btn-secondary">Abort</button>
          <button type="button" id="delDmNotificationConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<script>
   $(document).ready(function () 
   {
      var newEventsPopupTop = $(window).height() + $("#newEventsPopup").outerHeight();
      //var newEventsPopupLeft = $(window).width() - $("#newEventsPopup").outerWidth();
      var newEventsPopupLeft = 0;
      $("#newEventsPopup").css("top", newEventsPopupTop + "px");
      $("#newEventsPopup").css("left", newEventsPopupLeft + "px");
      
      $("#eventsManagerAppSelect").tooltip();
      $("#addDmEmailNotificationName").tooltip();
      $("#addDmEmailNotificationValStart").tooltip();
      $("#addDmEmailNotificationValEnd").tooltip();
      $("#addDmEmailNotificationRecFromBook").tooltip();
      $("#addDmEmailNotificationRecManual").tooltip();
      
      var prevPage = getCurrentPage();
      
      setCurrentPage("eventsManager");
      var usrRole = "<?php echo $_SESSION['usrRole']; ?>";
      var loginAppLdap = "<?php echo $_SESSION['loginAppLdap']; ?>";
      
      if(prevPage === "eventsManager")
      {
         $("#appHeader").css("opacity", 1.0);
         setTimeout(function(){
            $("#footer").css("opacity", 1.0);
             setTimeout(function(){
               $("#mainContainer").css("opacity", 1.0);
            }, 300);
         }, 300);
      }
      else
      {
         $("#headerSpan").css("opacity", 1.0); 
         $("#mainContainer").css("opacity", 1.0);
         if(prevPage === "login")
         {
            $("#loggedUsr").html("<?php echo $_SESSION['loginUsr']; ?>");
            $("#loginApp").html("<?php echo $_SESSION['loginApp']; ?>");
            $("#usrRole").html("<?php echo $_SESSION['usrRole']; ?>");
         }  
      }
      
      setMainMenuLinks();
      $("#eventsManagerBtn").off("click");
      
      $("#mainMenu div.mainMenuItemContainer").removeClass("active");
      $("#eventsManagerBtn").parent().addClass("active");
      
      <?php
            if(isset($_REQUEST['generatorId']))
            {    
      ?>
               //console.log("Aprire eventi da remoto");   
               loadEventsManagerTable(usrRole, loginAppLdap, '<?php echo $_REQUEST['generatorId']; ?>');
      <?php
            }
            else
            {
      ?>       
               //console.log("NON aprire eventi da remoto");
               loadEventsManagerTable(usrRole, loginAppLdap, false);
      <?php
            }
      ?>
      
      loadEventsManagerTable(usrRole, loginAppLdap, false);
     
     $("#addDmEmailNotificationCancelBtn").click(function(){
        $("#addEmailNotificationDmModal").modal('hide');
        $("#mngDmEventsModal").modal('show');
     });
     
     $('#addDmEmailNotificationVal').bootstrapToggle({
         on: 'Yes',
         off: 'No',
         onstyle: 'success',
         offstyle: 'primary',
         size: 'medium'
         //width:
         //height:
     });
     
     $('#editDmEmailNotificationVal').bootstrapToggle({
         on: 'Yes',
         off: 'No',
         onstyle: 'success',
         offstyle: 'primary',
         size: 'medium'
         //width:
         //height:
     });
      
     //$("#addDmEmailNotificationValStart input").val(startDate);
     //$("#addDmEmailNotificationValEnd input").val(endDate);
     
     $('#addDmEmailNotificationValStart').datetimepicker({
        format: 'DD/MM/YYYY HH:mm'
     });
     
     $('#addDmEmailNotificationValEnd').datetimepicker({
        format: 'DD/MM/YYYY HH:mm'
     });
     
     $('#editDmEmailNotificationValStart').datetimepicker({
        format: 'DD/MM/YYYY HH:mm'
     });
     
     $('#editDmEmailNotificationValEnd').datetimepicker({
        format: 'DD/MM/YYYY HH:mm'
     });
     
     $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "getEmailAddressesForNotificationForm"
         },
         type: "POST",
         async: true,
         dataType: 'json',
         success: function (data) 
         {
            switch(data.result)
            {
               case "queryKo":
                  $("#addDmEmailNotificationRecFromBook").hide();
                  $("#addDmEmailNotificationRecFromBookMsg").html("There was an error while retrieving addresses from database, please try again");
                  $("#addDmEmailNotificationRecFromBookMsg").show();
                  break;

               case "noAddresses":
                  $("#addDmEmailNotificationRecFromBook").hide();
                  $("#addDmEmailNotificationRecFromBookMsg").html("There are no addresses in the book");
                  $("#addDmEmailNotificationRecFromBookMsg").show();
                  break;
                  
               case "Ok":
                  $("#addDmEmailNotificationRecFromBook").select2({
                     data: data.addresses,
                     multiple: true,
                     allowClear: true
                  });
                  
                  $("#editDmEmailNotificationRecFromBook").select2({
                     data: data.addresses,
                     multiple: true,
                     allowClear: true
                  });
                  break;
                  
               default:
                  $("#addDmEmailNotificationRecFromBook").hide();
                  $("#addDmEmailNotificationRecFromBookMsg").html("There was an error while retrieving addresses from database, please try again");
                  $("#addDmEmailNotificationRecFromBookMsg").show();
                  break;
            }
         },
         error: function (data)
         {
            $("#addDmEmailNotificationRecFromBook").hide();
            $("#addDmEmailNotificationRecFromBookMsg").html("There was an error while calling API for addresses retrieval, please try again");
            $("#addDmEmailNotificationRecFromBookMsg").show();
            console.log("Error");
            console.log(JSON.stringify(data));
         }
      });
     
     
     $('#addDmEmailNotificationMsgSrc').bootstrapToggle({
         on: 'Book',
         off: 'New',
         size: 'medium',
         onstyle: 'primary',
         offstyle: 'success'
         //width:
         //height:
     });
     
     $('#editDmEmailNotificationMsgSrc').bootstrapToggle({
         on: 'Book',
         off: 'New',
         size: 'medium',
         onstyle: 'primary',
         offstyle: 'success'
         //width:
         //height:
     });
     
      //$("#addDmEmailNotificationMsgLbl").html("Messages book");
      
      //Il CKEDITOR viene creato una sola volta, poi nascosto o mostrato al bisogno
      CKEDITOR.replace('addDmEmailNotificationMsgComposerTxt', {
         allowedContent: true,
         language: 'en',
         width: '100%'
         //height: '100'
      });
      
      CKEDITOR.instances.addDmEmailNotificationMsgComposerTxt.on("instanceReady", function(event)
      {
          var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
          CKEDITOR.instances['addDmEmailNotificationMsgComposerTxt'].setData(defaultText);
      });
      
      CKEDITOR.replace('editDmEmailNotificationMsgComposerTxt', {
         allowedContent: true,
         language: 'en',
         width: '100%'
         //height: '100'
      });
      
      CKEDITOR.instances.editDmEmailNotificationMsgComposerTxt.on("instanceReady", function(event)
      {
          var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
          CKEDITOR.instances['editDmEmailNotificationMsgComposerTxt'].setData(defaultText);
      });
     
      $('#addDmEmailNotificationMsgSrc').change(function() 
      {
         switch($(this).prop('checked'))
         {
            case true:
               //Mostra messaggi dal book
               //$("#addDmEmailNotificationMsgLbl").html("Messages book");
               $("#addDmEmailNotificationMsgComposerContainer").hide();
               $("#addDmEmailNotificationEmailBookTableContainer").show();
               break;
               
            case false:
               //Mostra composer
               //$("#addDmEmailNotificationMsgLbl").html("Message composer");
               $("#addDmEmailNotificationEmailBookTableContainer").hide();
               $("#addDmEmailNotificationMsgComposerContainer").show();
               break;
         }
      });
      
      $('#editDmEmailNotificationMsgSrc').change(function() 
      {
         switch($(this).prop('checked'))
         {
            case true:
               //Mostra messaggi dal book
               $("#editDmEmailNotificationMsgLbl").html("Messages book");
               $("#editDmEmailNotificationMsgComposerContainer").hide();
               $("#editDmEmailNotificationEmailBookTableContainer").show();
               break;
               
            case false:
               //Mostra composer
               $("#editDmEmailNotificationMsgLbl").html("Message composer");
               $("#editDmEmailNotificationEmailBookTableContainer").hide();
               $("#editDmEmailNotificationMsgComposerContainer").show();
               break;
         }
      });
      
      $("#addDmEmailNotificationConfirmBtn").click(function()
      {
         var notifValid = null;
         if($('#addDmEmailNotificationVal').parent().hasClass("off") === true)
         {
            notifValid = false;
         }
         else
         {
            notifValid = true;
         }
         
         if($('#addDmEmailNotificationMsgSrc').parent().hasClass("off") === false)
         {
            addNotification($("#addDmEmailNotificationName").val(), $("#genId").val(), $("#addDmEmailNotificationEventSelect").val(), notifValid, $("#addDmEmailNotificationValStart input").val(), $("#addDmEmailNotificationValEnd input").val(), $('#addDmEmailNotificationRecFromBook').val(), $("#addDmEmailNotificationRecManual").val(), $("#selectedMsgId").val(), null, null);
         }
         else
         {
            var msgText = CKEDITOR.instances.addDmEmailNotificationMsgComposerTxt.getData();
            addNotification($("#addDmEmailNotificationName").val(), $("#genId").val(), $("#addDmEmailNotificationEventSelect").val(), notifValid, $("#addDmEmailNotificationValStart input").val(), $("#addDmEmailNotificationValEnd input").val(), $('#addDmEmailNotificationRecFromBook').val(), $("#addDmEmailNotificationRecManual").val(), null, $("#addDmEmailNotificationMsgComposerSub").val(), msgText);
         }
      });
      
      $("#editDmEmailNotificationCancelBtn").click(function(){
        $("#editEmailNotificationDmModal").modal('hide');
        $("#mngDmEventsModal").modal('show');
      });
      
      $("#editDmEmailNotificationConfirmBtn").click(function()
      {
         var notifValid = null;
         if($('#editDmEmailNotificationVal').parent().hasClass("off") === true)
         {
            notifValid = false;
         }
         else
         {
            notifValid = true;
         }
         
         if($('#editDmEmailNotificationMsgSrc').parent().hasClass("off") === false)
         {
            editNotification($("#editDmEmailNotificationId").val(), $("#editDmEmailNotificationName").val(), $("#genId").val(), $("#editDmEmailNotificationEventSelect").val(), notifValid, $("#editDmEmailNotificationValStart input").val(), $("#editDmEmailNotificationValEnd input").val(), $('#editDmEmailNotificationRecFromBook').val(), $("#editDmEmailNotificationRecManual").val(), $("#selectedMsgIdEdit").val(), null, null);
         }
         else
         {
            var msgText = CKEDITOR.instances.editDmEmailNotificationMsgComposerTxt.getData();       
            editNotification($("#editDmEmailNotificationId").val(), $("#editDmEmailNotificationName").val(), $("#genId").val(), $("#editDmEmailNotificationEventSelect").val(), notifValid, $("#editDmEmailNotificationValStart input").val(), $("#editDmEmailNotificationValEnd input").val(), $('#editDmEmailNotificationRecFromBook').val(), $("#editDmEmailNotificationRecManual").val(), null, $("#editDmEmailNotificationMsgComposerSub").val(), msgText);
         }
      });
   });//Fine document ready
</script>   