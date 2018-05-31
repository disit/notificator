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

?>

<div class="row" id="emailBookContainer">
   <div id="mainLoading" class="col-sm-10 col-sm-offset-1">
      <div class="col-sm-12 centerWithFlex loadingMsg">Loading messages, please wait</div>
      <div class="col-sm-12 centerWithFlex loadingSpin"><i class="fa fa-circle-o-notch fa-spin"></i></div>
   </div>
   <div id="emailBookTableContainer" class="col-sm-10 col-sm-offset-1">
      <table id="emailBookTable" class="mainContainerTable">
          <thead>
              <tr>
              </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
      <div id="emailBookMsg" class="col-sm-12 centerWithFlex"></div>
   </div>
   
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

<!-- Modale di aggiunta nuova email -->
<div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-labelledby="addEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEmailModalLabel">Add e-mail</h5>
        </div>
        <div class="modal-body">
           <div id="addEmailModalBody">
              <div class="row centerWithFlex addEmailNotificationDmModalBodyLbl">Message subject</div>
              <div class="row addEmailNotificationDmModalBodyField" style="margin-left: 15px">If you leave the subject blank or filled with the <span style='background-color: yellow'>[[Auto]]</span> placeholder, the system will generate the following automatic subject: <i>Application name - Container title - Generator name - Generator type</i></div>
              <div class="row centerWithFlex addEmailNotificationDmModalBodyField">
                 <input type="text" id="addEmailSub" class="form-control" value="[[Auto]]"/>
              </div>
              <div class="row centerWithFlex addEmailNotificationDmModalBodyLbl" id="addEmailLabelRow">Message body</div>
              <div class="row addEmailNotificationDmModalBodyField" id="addEmailplaceholderRow">You can add event details automatically selecting the <span style='background-color: yellow'>[[EventDetails]]</span> placeholder from the editor menu or adding it manually to message body. If the placeholder isn't added event details will be automatically appended at the end of message body.</div>
              <div class="row" id="addEmailTxtRow">
                 <textarea id="addEmailTxt" rows="10" cols="65"></textarea> <!-- Questa textarea verrà rimpiazzata dal CKEDITOR -->
              </div>
              <div class="row">
                  <div id="addEmailNotificationModalMsg" class="col-sm-6 col-sm-offset-3 centerWithFlex"></div>
              </div>
           </div>
           
            <div id="addEmailModalOk1" class="modalBodyInnerDiv">New message successfully inserted</div>
            <div id="addEmailModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
           
            <div id="addEmailModalKo1" class="modalBodyInnerDiv"></div>
            <div id="addEmailModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="addEmailCancelBtn" class="btn btn-secondary">Cancel</button>
          <button type="button" id="addEmailConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di modifica email -->
<div class="modal fade" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="editEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEmailModalLabel">Edit e-mail</h5>
        </div>
        <div class="modal-body">
           <div id="editEmailModalBody">
              <div class="row centerWithFlex addEmailNotificationDmModalBodyLbl">Message subject</div>
              <div class="row addEmailNotificationDmModalBodyField" style="margin-left: 15px">If you leave the subject blank or filled with the <span style='background-color: yellow'>[[Auto]]</span> placeholder, the system will generate the following automatic subject: Application name - Container title - Generator name - Generator type</div>
              <div class="row centerWithFlex addEmailNotificationDmModalBodyField">
                 <input type="text" id="editEmailSub" class="form-control"/>
              </div>
              <div class="row centerWithFlex addEmailNotificationDmModalBodyLbl" id="editEmailLabelRow">Message body</div>
              <div class="row addEmailNotificationDmModalBodyField" id="editEmailplaceholderRow">You can add event details automatically selecting the <span style='background-color: yellow'>[[EventDetails]]</span> placeholder from the editor menu or adding it manually to message body. If the placeholder isn't added event details will be automatically appended at the end of message body.</div>
              <div class="row" id="editEmailTxtRow">
                 <textarea id="editEmailTxt" rows="10" cols="65"></textarea> <!-- Questa textarea verrà rimpiazzata dal CKEDITOR -->
              </div>
              <div class="row">
                  <div id="editEmailNotificationModalMsg" class="col-sm-6 col-sm-offset-3 centerWithFlex"></div>
              </div>
           </div>
           <input type="hidden" id="editEmailId" /> 
           <input type="hidden" id="editEmailRowId" />
           <div id="editEmailModalOk1" class="modalBodyInnerDiv">Message successfully edited</div>
           <div id="editEmailModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
           
           <div id="editEmailModalKo1" class="modalBodyInnerDiv"></div>
           <div id="editEmailModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="editEmailCancelBtn" class="btn btn-secondary">Cancel</button>
          <button type="button" id="editEmailConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di cancellazione e-mail -->
<div class="modal fade" id="delEmailModal" tabindex="-1" role="dialog" aria-labelledby="delEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delEmailModalLabel">E-Mail deletion</h5>
        </div>
        <div id="delEmailModalBody1" class="modal-body centerWithFlex"></div>
        <div id="delEmailModalBody2" class="modal-body centerWithFlex"></div>
        
         <div id="delEmailModalOk1" class="modalBodyInnerDiv">E-mail successfully deleted</div>
         <div id="delEmailModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>

         <div id="delEmailModalKo1" class="modalBodyInnerDiv"></div>
         <div id="delEmailModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        
        <input type="hidden" id="emailIdToDel" />
        <div class="modal-footer">
          <button type="button" id="deleteEmailCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" id="deleteEmailConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di avviso cancellazione indirizzo e-mail non possibile -->
<div class="modal fade" id="delEmailUndeletableModal" tabindex="-1" role="dialog" aria-labelledby="delEmailUndeletableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delEmailAddressUndeletableModalLabel">E-Mail deletion</h5>
        </div>
        <div id="delEmailUndeletableModalBody" class="modal-body"></div>
      </div>
    </div>
</div>

<script src="js/main.js"></script>

<script>
   $(document).ready(function () 
   {
      var newEventsPopupTop = $(window).height() + $("#newEventsPopup").outerHeight();
      //var newEventsPopupLeft = $(window).width() - $("#newEventsPopup").outerWidth();
      var newEventsPopupLeft = 0;
      $("#newEventsPopup").css("top", newEventsPopupTop + "px");
      $("#newEventsPopup").css("left", newEventsPopupLeft + "px");
      
      var addMsgConditionsArray = [];
      var editMsgConditionsArray = [];
      
      setEmailBookGlobals(addMsgConditionsArray, editMsgConditionsArray);
      
      //addMsgConditionsArray['addEmailSub'] = false;
      //editMsgConditionsArray['editEmailSub'] = false;
      
      //$("#addEmailSub").on('input', checkAddMsgConditions);
      //$("#editEmailSub").on('input', checkEditMsgConditions);
      
      var prevPage = getCurrentPage();
      setCurrentPage("emailBook");
      
      //Refresh
      if(prevPage === "emailBook")
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
      }
      
      setMainMenuLinks();
      $("#emailBookBtn").off("click");
      
      $("#mainMenu div.mainMenuItemContainer").removeClass("active");
      $("#emailBookBtn").parent().addClass("active");
      
      var tableCols = null;
      
      CKEDITOR.replace('addEmailTxt', {
         allowedContent: true,
         language: 'en',
         width: '96%'
         //height: '100'
      });
      
      CKEDITOR.instances.addEmailTxt.on("instanceReady", function(event)
      {
          var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
          CKEDITOR.instances['addEmailTxt'].setData(defaultText);
      });
      
      CKEDITOR.replace('editEmailTxt', {
         allowedContent: true,
         language: 'en',
         width: '96%'
         //height: '100'
      });
      
      $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "getEmails"
         },
         type: "POST",
         async: true,
         dataType: 'json',
         success: function (data) 
         {
            switch(data.result)
            {
               case "queryKo":
                  $("#emailBookTable").hide();
                  $("#emailBookMsg").html("There was an error while retrieving messages from database, please try again");
                  $("#emailBookMsg").show();
                  break;
                  
               case "Ok":
                  tableCols = [
                  {
                      title: "Subject",
                      align: "center",
                      sortable: true,
                      field: "sub",
                      valign: "middle",
                      formatter: function(value, row, index)
                      {
                           if((value !== null)&&(value !== 'null'))
                           {
                              if(value.length > 30)
                              {
                                 return value.substr(0, 30) + " ...";
                              }
                              else
                              {
                                 return value;
                              }
                           }
                           else
                           {
                            return "-";
                           }
                      },
                      cellStyle: function cellStyle(value, row, index, field) {
                        return {
                          //classes: 'text-nowrap',
                          css: { 
                          }
                        };
                      }
                  },
                  {
                      title: "Text",
                      align: "center",
                      sortable: false,
                      field: "txt",
                      valign: "middle",
                      formatter: function(value, row, index)
                      {
                           if((value !== null)&&(value !== 'null'))
                           {
                              if(value.length > 30)
                              {
                                 return value.substr(0, 30) + " ...";
                              }
                              else
                              {
                                 return value;
                              }
                           }
                           else
                           {
                            return "-";
                           }
                      },
                      cellStyle: function cellStyle(value, row, index, field) {
                        return {
                          //classes: 'text-nowrap',
                          css: { 
                          }
                        };
                      }
                   },
                   {
                        title: "Edit",
                        align: "center",
                        valign: "middle",
                        formatter: function()
                        {
                            //La classe editEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
                            return '<a href="#" class="editEmailBtn"><i class="fa fa-gear" style="font-size:24px;color:#07afc5"></i></a>'; 
                        }
                    },
                   {
                        title: "Delete",
                        align: "center",
                        valign: "middle",
                        field: "deletable",
                        formatter: function(value)
                        {
                            var btnColor;
                            if(value === false)
                            {
                               btnColor = "grey";
                            }
                            else
                            {
                               btnColor = "red";
                            }
                            //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
                            return '<a href="#" class="delEmailBtn" data-deletable="' + value + '"><i class="fa fa-close" style="font-size:24px;color:' + btnColor + '"></i></a>'; 
                        }
                    }
                  ];
                  
                  $('#emailBookTable').bootstrapTable({
                      data: data.emails,
                      columns: tableCols,
                      search: true,
                      pagination: true,
                      pageSize: 10,
                      locale: 'en-US',
                      searchAlign: 'left',
                      uniqueId: "id",
                      onAll: function(name, args)
                      {
                        $("div.pagination-detail").remove();

                        //Creazione pulsante di aggiunta email
                        if($("#addEmailBtn").length === 0)
                        {
                            var addEmailBtn = $('<div class="pull-right centerWithFlex" id="addEmailBtn" data-toggle="tooltip" data-container="body" title="Add a new e-mail message to the book"><img src="img/emailBook/plusYellow.png" width="32px" height="32px" /></div>');
                            $("div.fixed-table-toolbar").append(addEmailBtn);
                            $("#addEmailBtn").css("cursor", "pointer");
                            $("#addEmailBtn").css("margin-top", "10px");
                            
                            $("#addEmailBtn").tooltip(); 

                            $("#addEmailBtn").hover(
                                function() 
                                {
                                   $(this).find("img").attr("src", "img/emailBook/plusRed.png");
                                }, 
                                function() 
                                {
                                    $(this).find("img").attr("src", "img/emailBook/plusYellow.png");
                                }
                            );
                            $("#addEmailBtn").click(function(){
                                $("#addEmailModal").modal('show');
                            });
                        }
                        
                        //Rimpiazzo del messaggio di assenza dati con uno specifico.
                        if($("#emailBookTable tbody tr.no-records-found").length > 0)
                        {
                           $("#emailBookTable tbody tr.no-records-found td").html("There are no messages in the system");
                        }

                        //Listener pulsanti di apertura e-mail per modifica 
                        $("a.editEmailBtn").off();
                        $("a.editEmailBtn").click(function()
                        {
                           $("#editEmailId").val($(this).parent().parent().attr("data-uniqueid"));
                           $("#editEmailRowId").val($(this).parent().parent().index());
                           getEmailForEdit($("#editEmailId").val());
                        });
                        
                        $('#emailBookTable i.fa-gear').hover(function(){
                           $(this).css("color", "#ffcc00");
                        },
                        function(){
                           $(this).css("color", "#07afc5");
                        });
                        
                        //Listener pulsanti di cancellazione e-mail
                        $("a.delEmailBtn").off();
                        $("a.delEmailBtn").click(function()
                        {
                           if($(this).attr("data-deletable") === "false")
                           {
                              $("#delEmailUndeletableModal").modal('show');
                              $("#delEmailUndeletableModal div.modal-body").html("Message with subject&nbsp;<b>" + $(this).parent().parent().find("td").eq(0).html() + " </b>&nbsp;is not deletable beacuse it's used in one or more notifications.");
                              setTimeout(function(){
                                 $("#delEmailUndeletableModal").modal('hide');
                              }, 3000);
                           }
                           else
                           {
                              $("#delEmailModal").modal('show');
                              $("#delEmailModalBody1").html("You are about to delete e-mail with subject:");
                              $("#delEmailModalBody2").html("<b>" + $(this).parent().parent().find("td").eq(0).html() + "</b>");
                              $("#emailIdToDel").val($(this).parent().parent().attr("data-uniqueid"));
                           }
                        });
                        
                        $('#emailBookTable i.fa-close').off("hover");
                        $('#emailBookTable i.fa-close').hover(function(){
                           $(this).css("color", "#ffcc00");
                        },
                        function(){
                           if($(this).parent().attr("data-deletable") === "false")
                           {
                              $(this).css("color", "grey");
                           }
                           else
                           {
                              $(this).css("color", "red");
                           }
                        });
                        
                        //Nascondimento del loading ed esposizione della tabella
                        $("#mainLoading").css("opacity", 0);
                        setTimeout(function(){
                           $("#mainLoading").hide();
                           $("#emailBookTableContainer").show();
                           setTimeout(function(){
                              $("#emailBookTableContainer").css("opacity", 1);
                           }, 100);
                        }, 100);
                      }
                  });
                  break;
                  
               default:
                  $("#emailBookTable").hide();
                  $("#emailBookMsg").html("There was an error while retrieving emails from database, please try again");
                  $("#emailBookMsg").show();
                  break;
            }
         },
         error: function (data)
         {
            console.log("Error");
            console.log(JSON.stringify(data));
         }
      }); 
      
      $("#addEmailConfirmBtn").click(function()
      {
         $.ajax({
            url: "restInterface.php",
            data: 
            {
               apiUsr: "alarmManager",
               apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
               operation: "insertEmail",
               sub: $("#addEmailSub").val(),
               txt: CKEDITOR.instances.addEmailTxt.getData()
            },
            type: "POST",
            async: true,
            dataType: 'json',
            success: function(data) 
            {
               switch(data)
               {
                  case "missingParams":
                     //TBD - Non gestiamolo, è impossibile che accada
                     break;
                     
                  case "dbConnKo":
                     $("#addEmailModalBody").hide();
                     $("#addEmailModalKo1").show();
                     $("#addEmailModalKo2").show();
                     
                     $("#addEmailModalKo1").html("Error while trying to connect to application database");
                     
                     $("#addEmailModal div.modal-footer").hide();
                     
                     setTimeout(function()
                     {
                        $("#addEmailModal").modal('hide');
                        $("#addEmailModalKo1").html("");
                        $("#addEmailModalKo1").hide();
                        $("#addEmailModalKo2").hide();
                        $("#addEmailModalBody").show();
                        $("#addEmailModal div.modal-footer").show();
                        $("#addEmailSub").val("[[Auto]]");
                        var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
                        CKEDITOR.instances['addEmailTxt'].setData(defaultText);
                     }, 1500);
                     break;
                     
                  case "queryKo":
                     $("#addEmailModalBody").hide();
                     $("#addEmailModalKo1").show();
                     $("#addEmailModalKo2").show();
                     
                     $("#addEmailModalKo1").html("Error while trying to query application database");
                     
                     $("#addEmailModal div.modal-footer").hide();
                     
                     setTimeout(function()
                     {
                        $("#addEmailModal").modal('hide');
                        $("#addEmailModalKo1").html("");
                        $("#addEmailModalKo1").hide();
                        $("#addEmailModalKo2").hide();
                        $("#addEmailModalBody").show();
                        $("#addEmailModal div.modal-footer").show();
                        $("#addEmailSub").val("[[Auto]]");
                        var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
                        CKEDITOR.instances['addEmailTxt'].setData(defaultText);
                     }, 1500);
                     break;
                     
                  default:
                     var newEmail = {
                        id: String(data.result),
                        sub: $("#addEmailSub").val(),
                        txt: CKEDITOR.instances.addEmailTxt.getData(),
                        deletable: true
                     };
                     $('#emailBookTable').bootstrapTable('append', newEmail);
                     
                     $("#addEmailModalBody").hide();
                     $("#addEmailModalOk1").show();
                     $("#addEmailModalOk2").show();
                     
                     $("#addEmailModal div.modal-footer").hide();
                     
                     setTimeout(function()
                     {
                        $("#addEmailModal").modal('hide');
                        $("#addEmailModalOk1").hide();
                        $("#addEmailModalOk2").hide();
                        $("#addEmailModalBody").show();
                        $("#addEmailModal div.modal-footer").show();
                        $("#addEmailSub").val("[[Auto]]");
                        addMsgConditionsArray['addEmailSub'] = false;
                        var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
                        CKEDITOR.instances['addEmailTxt'].setData(defaultText);
                     }, 1500);
                     break;
               }
            },
            error: function(data)
            {
               console.log("Error");
               console.log(data);
            }
         });
      });
      
      $("#addEmailCancelBtn").click(function()
      {
         $("#addEmailSub").val("[[Auto]]");
         var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
         CKEDITOR.instances['addEmailTxt'].setData(defaultText);
         $("#addEmailModal").modal('hide');
      });
      
      $("#editEmailCancelBtn").click(function()
      {
         $("#editEmailSub").val("");
         CKEDITOR.instances.editEmailTxt.setData('');
         $("#editEmailModal").modal('hide');
      });
      
      $("#editEmailConfirmBtn").click(function()
      {
         editEmail($("#editEmailId").val(), $("#editEmailSub").val(), CKEDITOR.instances.editEmailTxt.getData());
      });
      
      $("#deleteEmailConfirmBtn").click(function()
      {
         deleteEmail($("#emailIdToDel").val());
      });
      
      
   });//Fine document ready
</script>  
