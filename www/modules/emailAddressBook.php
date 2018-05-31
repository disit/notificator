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

<div class="row" id="emailAddressBookContainer">
   <div id="mainLoading" class="col-sm-10 col-sm-offset-1">
      <div class="col-sm-12 centerWithFlex loadingMsg">Loading addresses, please wait</div>
      <div class="col-sm-12 centerWithFlex loadingSpin"><i class="fa fa-circle-o-notch fa-spin"></i></div>
   </div>
   <div id="emailAddressBookTableContainer" class="col-sm-10 col-sm-offset-1">
      <table id="emailAddressBookTable" class="mainContainerTable">
          <thead>
              <tr>
              </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
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

<!-- Modale di aggiunta nuova voce in rubrica -->
<div class="modal fade" id="addEmailAddressModal" tabindex="-1" role="dialog" aria-labelledby="addEmailAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEmailAddressModalLabel">Add e-mail account</h5>
        </div>
        <div class="modal-body">
           <div id="addEmailAddressModalForm">
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">First name</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Last name</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="newEmailAdrFirstName" class="form-control"/>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="newEmailAdrLastName" class="form-control"/>
                 </div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Organization</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">E-Mail address</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="newEmailAdrOrg" class="form-control"/>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="email" id="newEmailAdrEmail" class="form-control"/>
                 </div>
              </div>
              <div class="row">
                  <div id="addEmailAddressModalMsg" class="col-sm-6 col-sm-offset-3 centerWithFlex">Some fields have not been filled</div>
              </div> 
           </div>
           
            <div id="addEmailAddressModalOk1" class="modalBodyInnerDiv">New e-mail account successfully inserted</div>
            <div id="addEmailAddressModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
           
            <div id="addEmailAddressModalKo1" class="modalBodyInnerDiv"></div>
            <div id="addEmailAddressModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="addEmailAddressCancelBtn" class="btn btn-secondary">Cancel</button>
          <button type="button" id="addEmailAddressConfirmBtn" class="btn btn-primary" disabled>Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di modifica voce in rubrica -->
<div class="modal fade" id="editEmailAddressModal" tabindex="-1" role="dialog" aria-labelledby="editEmailAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEmailAddressModalLabel">Edit e-mail account</h5>
        </div>
        <div class="modal-body">
           <div id="editEmailAddressModalBody">
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">First name</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Last name</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="editEmailAdrFirstName" class="form-control"/>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="editEmailAdrLastName" class="form-control"/>
                 </div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">Organization</div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyLbl">E-Mail address</div>
              </div>
              <div class="row">
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="text" id="editEmailAdrOrg" class="form-control"/>
                 </div>
                 <div class="col-sm-6 centerWithFlex addEmailNotificationDmModalBodyField">
                    <input type="email" id="editEmailAdrEmail" class="form-control"/>
                 </div>
                 <input type="hidden" id="editEmailAdrId" />
                 <input type="hidden" id="editEmailAdrRowId" />
              </div>
              <div class="row">
                  <div id="editEmailAddressModalMsg" class="col-sm-6 col-sm-offset-3 centerWithFlex"></div>
              </div>  
           </div>
           
            <div id="editEmailAddressModalOk1" class="modalBodyInnerDiv">E-mail account successfully edited</div>
            <div id="editEmailAddressModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
           
            <div id="editEmailAddressModalKo1" class="modalBodyInnerDiv"></div>
            <div id="editEmailAddressModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="editEmailAddressCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" id="editEmailAddressConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di cancellazione indirizzo e-mail -->
<div class="modal fade" id="delEmailAddressModal" tabindex="-1" role="dialog" aria-labelledby="delEmailAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delEmailAddressModalLabel">E-Mail account deletion</h5>
        </div>
        <div id="delEmailAddressModalBody" class="modal-body centerWithFlex"></div>
        
         <div id="delEmailAddressModalOk1" class="modalBodyInnerDiv">E-mail address successfully deleted</div>
         <div id="delEmailAddressModalOk2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>

         <div id="delEmailAddressModalKo1" class="modalBodyInnerDiv"></div>
         <div id="delEmailAddressModalKo2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
        
        <input type="hidden" id="emailIdToDel" />
        <div class="modal-footer">
          <button type="button" id="deleteEmailAddressCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" id="deleteEmailAddressConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
</div>

<!-- Modale di avviso cancellazione indirizzo e-mail non possibile -->
<div class="modal fade" id="delEmailAddressUndeletableModal" tabindex="-1" role="dialog" aria-labelledby="delEmailAddressUndeletableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delEmailAddressUndeletableModalLabel">E-Mail account deletion</h5>
        </div>
        <div id="delEmailAddressUndeletableModalBody" class="modal-body"></div>
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
      
      var addNewAddressConditionsArray = new Array();
      var editAddressConditionsArray = new Array();
      
      setAddressBookGlobals(addNewAddressConditionsArray, editAddressConditionsArray);
      
      addNewAddressConditionsArray['newEmailAdrFirstName'] = false;
      addNewAddressConditionsArray['newEmailAdrLastName'] = false;
      addNewAddressConditionsArray['newEmailAdrOrg'] = false;
      addNewAddressConditionsArray['newEmailAdrEmail'] = false;
      editAddressConditionsArray['editEmailAdrFirstName'] = false;
      editAddressConditionsArray['editEmailAdrLastName'] = false;
      editAddressConditionsArray['editEmailAdrOrg'] = false;
      editAddressConditionsArray['editEmailAdrEmail'] = false;
      
      
      
      $("#newEmailAdrFirstName").on('input', checkAddNewAddressConditions);
      $("#newEmailAdrLastName").on('input', checkAddNewAddressConditions);
      $("#newEmailAdrOrg").on('input', checkAddNewAddressConditions);
      $("#newEmailAdrEmail").on('input', checkAddNewAddressConditions);
      $("#editEmailAdrFirstName").on('input', checkEditAddressConditions);
      $("#editEmailAdrLastName").on('input', checkEditAddressConditions);
      $("#editEmailAdrOrg").on('input', checkEditAddressConditions);
      $("#editEmailAdrEmail").on('input', checkEditAddressConditions);
      
      var prevPage = getCurrentPage();
      setCurrentPage("emailAddressBook");
      
      //Refresh
      if(prevPage === "emailAddressBook")
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
      $("#emailAddressBookBtn").off("click");
       
      $("#mainMenu div.mainMenuItemContainer").removeClass("active");
      $("#emailAddressBookBtn").parent().addClass("active");
      
      var tableCols = null;
      
      $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "getEmailAddresses"
         },
         type: "POST",
         async: true,
         dataType: 'json',
         success: function (data) 
         {
            switch(data.result)
            {
               case "queryKo":
                  $("#emailAddressBookTable").hide();
                  $("#emailAddressesMsg").html("There was an error while retrieving addresses from database, please try again");
                  $("#emailAddressesMsg").show();
                  break;
                  
               case "Ok":
                  tableCols = [
                  {
                      title: "Address",
                      align: "center",
                      sortable: true,
                      field: "adr",
                      valign: "middle",
                      cellStyle: function cellStyle(value, row, index, field) {
                          return {
                            //classes: 'text-nowrap',
                            css: { 
                            }
                          };
                      }
                  },
                  {
                      title: "First name",
                      align: "center",
                      sortable: true,
                      field: "fName",
                      valign: "middle",
                      cellStyle: function cellStyle(value, row, index, field) {
                          return {
                            //classes: 'text-nowrap',
                            css: { 
                            }
                          };
                      }
                   },
                   {
                      title: "Last name",
                      align: "center",
                      sortable: true,
                      field: "lName",
                      valign: "middle",
                      cellStyle: function cellStyle(value, row, index, field) {
                          return {
                            //classes: 'text-nowrap',
                            css: { 
                            }
                          };
                      }
                   },
                   {
                      title: "Organization",
                      align: "center",
                      sortable: true,
                      field: "org",
                      valign: "middle",
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
                            return '<a href="#" class="editEmailAddrBtn"><i class="fa fa-gear" style="font-size:24px;color:#07afc5"></i></a>';
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
                            //La classe editDashBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
                            return '<a href="#" class="delEmailAddrBtn" data-deletable="' + value + '"><i class="fa fa-close" style="font-size:24px;color:' + btnColor + '"></i></a>'; 
                        }
                    }
                  ];
                  
                  $(function () {
                     $('#emailAddressBookTable').bootstrapTable({
                         data: data.addresses,
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
                           $("div.search input").css("font-family", "Aller");
                           $("div.pagination a").css("font-family", "Aller");
                           
                           //Creazione pulsante di aggiunta voce di rubrica
                           if($("#addEmailAddrBtn").length === 0)
                           {
                               var addEmailAddrBtn = $('<div class="pull-right centerWithFlex" data-toggle="tooltip" data-container="body" title="Add a new e-mail address to recipients list" id="addEmailAddrBtn"><img src="img/emailBook/plusYellow.png" width="32px" height="32px" /></div>');
                               $("div.fixed-table-toolbar").append(addEmailAddrBtn);
                               $("#addEmailAddrBtn").css("cursor", "pointer");
                               $("#addEmailAddrBtn").css("margin-top", "10px");
                               
                               $("#addEmailAddrBtn").tooltip(); 

                               $("#addEmailAddrBtn").hover(
                                   function() 
                                   {
                                      $(this).find("img").attr("src", "img/emailBook/plusRed.png");
                                   }, 
                                   function() 
                                   {
                                       $(this).find("img").attr("src", "img/emailBook/plusYellow.png");
                                   }
                               );
                               $("#addEmailAddrBtn").click(function(){
                                   checkAddNewAddressConditions();
                                   $("#addEmailAddressModal").modal('show');
                               });
                           }
                           
                            //Rimpiazzo del messaggio di assenza dati con uno specifico.
                            if($("#emailAddressBookTable tbody tr.no-records-found").length > 0)
                            {
                               $("#emailAddressBookTable tbody tr.no-records-found td").html("There are no e-mail addresses in the system");
                            }
                           
                           //Listener pulsanti di apertura voce di rubrica per modifica 
                           $("a.editEmailAddrBtn").off();
                           $("a.editEmailAddrBtn").click(function()
                           {
                              var emailAccountId = $(this).parent().parent().attr("data-uniqueid");
                              $("#editEmailAddrId").val(emailAccountId);
                              $("#editEmailAdrRowId").val($(this).parent().parent().index());
                              getEmailAccountForEdit(emailAccountId);
                           });
                           
                           $('#emailAddressBookTable i.fa-gear').hover(function(){
                              $(this).css("color", "#ffcc00");
                           },
                           function(){
                              $(this).css("color", "#07afc5");
                           });
                           
                           //Listener pulsanti di cancellazione indirizzo
                           $("a.delEmailAddrBtn").off();
                           $("a.delEmailAddrBtn").click(function()
                           {  
                              if($(this).attr("data-deletable") === "false")
                              {
                                 $("#delEmailAddressUndeletableModal").modal('show');
                                 $("#delEmailAddressUndeletableModal div.modal-body").html("Address&nbsp;<b>" + $(this).parent().parent().find("td").eq(0).html() + " </b>&nbsp;is not deletable beacuse it's used in one or more notifications.");
                                 setTimeout(function(){
                                    $("#delEmailAddressUndeletableModal").modal('hide');
                                 }, 3000);
                              }
                              else
                              {
                                 $("#delEmailAddressModal").modal('show');
                                 $("#delEmailAddressModal div.modal-body").html("Do you really want to delete e-mail &nbsp;<b> " + $(this).parent().parent().find("td").eq(0).html() + " </b> &nbsp;?");
                                 $("#emailIdToDel").val($(this).parent().parent().attr("data-uniqueid"));
                              }
                           });
                           
                           $('#emailAddressBookTable i.fa-close').off("hover");
                           $('#emailAddressBookTable i.fa-close').hover(function(){
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
                              $("#emailAddressBookTableContainer").show();
                              setTimeout(function(){
                                 $("#emailAddressBookTableContainer").css("opacity", 1);
                              }, 100);
                           }, 100);
                         }
                     });
                  });
                  break;
                  
               default:
                  $("#emailAddressBookTable").hide();
                  $("#emailAddressesMsg").html("There was an error while retrieving addresses from database, please try again");
                  $("#emailAddressesMsg").show();
                  break;
            
            }
         },
         error: function (data)
         {
            console.log("Error");
            console.log(JSON.stringify(data));
         }
      }); 
      
      $("#addEmailAddressConfirmBtn").click(function()
      {
         $.ajax({
            url: "restInterface.php",
            data: 
            {
               apiUsr: "alarmManager",
               apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
               operation: "insertEmailAddress",
               adr: $("#newEmailAdrEmail").val(),
               fName: $("#newEmailAdrFirstName").val(),
               lName: $("#newEmailAdrLastName").val(),
               org: $("#newEmailAdrOrg").val()
            },
            type: "POST",
            async: true,
            dataType: 'json',
            success: function(data) 
            {
               switch(data)
               {
                  case "missingParams":
                     //TBD - Non gestiamolo per ora
                     break;
                     
                  case "dbConnKo":
                     $("#addEmailAddressModalForm").hide();
                     $("#addEmailAddressModalKo1").show();
                     $("#addEmailAddressModalKo2").show();
                     
                     $("#addEmailAddressModalKo1").html("Error while trying to connect to application database");
                     
                     $("#addEmailAddressModal div.modal-footer").hide();
                     
                     setTimeout(function()
                     {
                        $("#addEmailAddressModal").modal('hide');
                        $("#addEmailAddressModalKo1").html("");
                        $("#addEmailAddressModalKo1").hide();
                        $("#addEmailAddressModalKo2").hide();
                        $("#addEmailAddressModalForm").show();
                        $("#addEmailAddressModal div.modal-footer").show();
                        $("#newEmailAdrEmail").val("");
                        $("#newEmailAdrFirstName").val("");
                        $("#newEmailAdrLastName").val("");
                        $("#newEmailAdrOrg").val("");
                     }, 1500);
                     break;
                     
                  case "queryKo":
                     $("#addEmailAddressModalForm").hide();
                     $("#addEmailAddressModalKo1").show();
                     $("#addEmailAddressModalKo2").show();
                     
                     $("#addEmailAddressModalKo1").html("Error while trying to query application database");
                     
                     $("#addEmailAddressModal div.modal-footer").hide();
                     
                     setTimeout(function()
                     {
                        $("#addEmailAddressModal").modal('hide');
                        $("#addEmailAddressModalKo1").html("");
                        $("#addEmailAddressModalKo1").hide();
                        $("#addEmailAddressModalKo2").hide();
                        $("#addEmailAddressModalForm").show();
                        $("#addEmailAddressModal div.modal-footer").show();
                        $("#newEmailAdrEmail").val("");
                        $("#newEmailAdrFirstName").val("");
                        $("#newEmailAdrLastName").val("");
                        $("#newEmailAdrOrg").val("");
                     }, 1500);
                     break;
                     
                  default:
                     var newAddress = {
                        id: String(data.result),
                        adr: $("#newEmailAdrEmail").val(),
                        fName: $("#newEmailAdrFirstName").val(),
                        lName: $("#newEmailAdrLastName").val(),
                        org: $("#newEmailAdrOrg").val(),
                        deletable: true
                     };
                     $('#emailAddressBookTable').bootstrapTable('append', newAddress);
                     
                     $("#addEmailAddressModalForm").hide();
                     $("#addEmailAddressModalOk1").show();
                     $("#addEmailAddressModalOk2").show();
                     
                     $("#addEmailAddressModal div.modal-footer").hide();
                     
                     setTimeout(function()
                     {
                        $("#addEmailAddressModal").modal('hide');
                        $("#addEmailAddressModalOk1").hide();
                        $("#addEmailAddressModalOk2").hide();
                        $("#addEmailAddressModalForm").show();
                        $("#addEmailAddressModal div.modal-footer").show();
                        $("#newEmailAdrEmail").val("");
                        $("#newEmailAdrFirstName").val("");
                        $("#newEmailAdrLastName").val("");
                        $("#newEmailAdrOrg").val("");
                        addNewAddressConditionsArray['newEmailAdrFirstName'] = false;
                        addNewAddressConditionsArray['newEmailAdrLastName'] = false;
                        addNewAddressConditionsArray['newEmailAdrOrg'] = false;
                        addNewAddressConditionsArray['newEmailAdrEmail'] = false;
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
      
      $("#addEmailAddressCancelBtn").click(function()
      {
         $("#newEmailAdrFirstName").val("");
         $("#newEmailAdrLastName").val("");
         $("#newEmailAdrOrg").val("");
         $("#newEmailAdrEmail").val("");
         $("#addEmailAddressModal").modal('hide');
      });
      
      $("#editEmailAddressConfirmBtn").click(function()
      {
         editEmailAccount($("#editEmailAdrId").val(), $("#editEmailAdrRowId").val(), $("#editEmailAdrFirstName").val(), $("#editEmailAdrLastName").val(), $("#editEmailAdrOrg").val(), $("#editEmailAdrEmail").val());
      });
      
      $("#deleteEmailAddressConfirmBtn").click(function()
      {
         console.log("ID: " + $("#emailIdToDel").val());
         deleteEmailAddress($("#emailIdToDel").val());
      });
      
      
   });//Fine document ready
</script>  
