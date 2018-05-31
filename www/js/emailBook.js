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

var addMsgConditionsArray, editMsgConditionsArray = null;

function setEmailBookGlobals(addMsgConditionsArrayAtt, editMsgConditionsArrayAtt)
{
    addMsgConditionsArray = addMsgConditionsArrayAtt;
    editMsgConditionsArray = editMsgConditionsArrayAtt;
}

function getDateForPicker(type)
{
   var now = new Date();
     
   var day = now.getDate();
   if(day < 9)
   {
      day = "0" + day;
   }

   var month = now.getMonth() + 1;
   if(month < 9)
   {
      month = "0" + month;
   }

   var hour = now.getHours();
   if(hour < 9)
   {
      hour = "0" + hour;
   }

   var min = now.getMinutes();
   if(min < 9)
   {
      min = "0" + min;
   }
   
   var year = now.getFullYear();
   
   if(type === "oneYear")
   {
      year += 1;
   }
   
   return day + "/" + month + "/" + year + " " + hour + ":" + min;
}

function checkMsgFormField(array, field)
{
    if($("#" + field).val().length < 1)
    {
        array[field] = false;
    }
    else
    {
        array[field] = true;
    }
}

function checkAddMsgConditions()
{
    var enableButton = true;
    for(var key in addMsgConditionsArray) 
    {
        checkMsgFormField(addMsgConditionsArray, key);
        if(addMsgConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#addEmailConfirmBtn").attr("disabled", false);
        $("#addEmailNotificationModalMsg").css("color", "black");
        $("#addEmailNotificationModalMsg").html("");
    }
    else
    {
        $("#addEmailConfirmBtn").attr("disabled", true);
        $("#addEmailNotificationModalMsg").css("color", "red");
        $("#addEmailNotificationModalMsg").html("Subject is mandatory");
    }
}

function checkEditMsgConditions()
{
    var enableButton = true;
    
    for(var key in editMsgConditionsArray) 
    {
        checkMsgFormField(editMsgConditionsArray, key);
        if(editMsgConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#editEmailConfirmBtn").attr("disabled", false);
        $("#editEmailNotificationModalMsg").css("color", "black");
        $("#editEmailNotificationModalMsg").html("");
    }
    else
    {
        $("#editEmailConfirmBtn").attr("disabled", true);
        $("#editEmailNotificationModalMsg").css("color", "red");
        $("#editEmailNotificationModalMsg").html("Subject is mandatory");
    }
}

function getEmailForEdit(id)
{
   $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "getEmail",
            id: id
         },
         type: "POST",
         async: true,
         dataType: 'json',
         success: function (data) 
         {
            switch(data)
            {
               case "missingParams":
                  //TBD - Non gestiamolo, è impossibile che accada
                  break;

               case "dbConnKo":
                  $("#editEmailModalBody1").hide();
                  $("#editEmailModalBody2").hide();
                  $("#editEmailModalKo1").show();
                  $("#editEmailModalKo2").show();

                  $("#editEmailModalKo1").html("Error while trying to connect to application database");

                  $("#editEmailModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#editEmailModal").modal('hide');
                     $("#editEmailModalKo1").html("");
                     $("#editEmailModalKo1").hide();
                     $("#editEmailModalKo2").hide();
                     $("#editEmailModalBody1").show();
                     $("#editEmailModalBody2").show();
                     $("#editEmailModal div.modal-footer").show();
                  }, 1500);
                  break;

               case "queryKo":
                  $("#editEmailModalBody1").hide();
                  $("#editEmailModalBody2").hide();
                  $("#editEmailModalKo1").show();
                  $("#editEmailModalKo2").show();

                  $("#editEmailModalKo1").html("Error while trying to query application database");

                  $("#editEmailModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#editEmailModal").modal('hide');
                     $("#editEmailModalKo1").html("");
                     $("#editEmailModalKo1").hide();
                     $("#editEmailModalKo2").hide();
                     $("#editEmailModalBody1").show();
                     $("#editEmailModalBody2").show();
                     $("#editEmailModal div.modal-footer").show();
                  }, 1500);
                  break;

               default:
                  $("#editEmailSub").val(data.email.sub);
                  CKEDITOR.instances.editEmailTxt.setData(data.email.txt);
                  checkEditMsgConditions();
                  $("#editEmailModal").modal('show');
                  break;
            }
         },
         error: function (data)
         {
            console.log("Error");
            console.log(data);
         }
   });
}

function editEmail(id, sub, txt)
{
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "editEmail",
         id: id,
         sub: sub,
         txt: txt
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function (data) 
      {
         switch(data.result)
         {
            case "missingParams":
               //TBD - Non gestiamolo, è impossibile che accada
               break;

            case "dbConnKo":
               $("#editEmailModalBody").hide();
               $("#editEmailModalKo1").show();
               $("#editEmailModalKo2").show();

               $("#editEmailModalKo1").html("Error while trying to connect to application database");

               $("#editEmailModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editEmailModal").modal('hide');
                  $("#editEmailModalKo1").html("");
                  $("#editEmailModalKo1").hide();
                  $("#editEmailModalKo2").hide();
                  $("#editEmailModalBody").show();
                  $("#editEmailModal div.modal-footer").show();
                  $("#editEmailId").val("");
                  $("#editEmailRowId").val("");
               }, 1500);
               break;

            case "queryKo":
               $("#editEmailModalBody").hide();
               $("#editEmailModalKo1").show();
               $("#editEmailModalKo2").show();

               $("#editEmailModalKo1").html("Error while trying to query application database");

               $("#editEmailModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editEmailModal").modal('hide');
                  $("#editEmailModalKo1").html("");
                  $("#editEmailModalKo1").hide();
                  $("#editEmailModalKo2").hide();
                  $("#editEmailModalBody").show();
                  $("#editEmailModal div.modal-footer").show();
                  $("#editEmailId").val("");
                  $("#editEmailRowId").val("");
               }, 1500);
               break;

            case "Ok":
               $('#emailBookTable').bootstrapTable('updateCell', {
                  index: $("#editEmailRowId").val(),
                  field: "sub",
                  value: sub
               });
               
               $('#emailBookTable').bootstrapTable('updateCell', {
                  index: $("#editEmailRowId").val(),
                  field: "txt",
                  value: txt
               });

               $("#editEmailModalBody").hide();
               $("#editEmailModalOk1").show();
               $("#editEmailModalOk2").show();

               $("#editEmailModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editEmailModal").modal('hide');
                  $("#editEmailModalOk1").hide();
                  $("#editEmailModalOk2").hide();
                  $("#editEmailModalBody").show();
                  $("#editEmailModal div.modal-footer").show();
                  $("#editEmailId").val("");
                  $("#editEmailRowId").val("");
                  editMsgConditionsArray['editEmailSub'] = false;
               }, 1500);
               break;
         }
      },
      error: function (data)
      {
         console.log("Error");
         console.log(data);
      }
   });
}

function deleteEmail(id)
{
    $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "deleteEmail",
            id: id
         },
         type: "POST",
         async: true,
         dataType: 'json',
         success: function (data) 
         {
            switch(data)
            {
               case "missingParams":
                  //TBD - Non gestiamolo, è impossibile che accada
                  break;

               case "dbConnKo":
                  $("#delEmailModalBody1").hide();
                  $("#delEmailModalBody2").hide();
                  $("#delEmailModalKo1").show();
                  $("#delEmailModalKo2").show();

                  $("#delEmailModalKo1").html("Error while trying to connect to application database");

                  $("#delEmailModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#delEmailModal").modal('hide');
                     $("#delEmailModalKo1").html("");
                     $("#delEmailModalKo1").hide();
                     $("#delEmailModalKo2").hide();
                     $("#delEmailModalBody1").show();
                     $("#delEmailModalBody2").show();
                     $("#delEmailModal div.modal-footer").show();
                     $("#emailIdToDel").val("");
                  }, 1500);
                  break;

               case "queryKo":
                  $("#delEmailModalBody1").hide();
                  $("#delEmailModalBody2").hide();
                  $("#delEmailModalKo1").show();
                  $("#delEmailModalKo2").show();

                  $("#delEmailModalKo1").html("Error while trying to query application database");

                  $("#delEmailModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#delEmailModal").modal('hide');
                     $("#delEmailModalKo1").html("");
                     $("#delEmailModalKo1").hide();
                     $("#delEmailModalKo2").hide();
                     $("#delEmailModalBody1").show();
                     $("#delEmailModalBody2").show();
                     $("#delEmailModal div.modal-footer").show();
                     $("#emailIdToDel").val("");
                  }, 1500);
                  break;

               default:
                  var ids = new Array();
                  ids.push($("#emailIdToDel").val());
                  $('#emailBookTable').bootstrapTable('remove', {field: 'id', values: ids});
                  
                  $("#delEmailModalBody1").hide();
                  $("#delEmailModalBody2").hide();
                  $("#delEmailModalOk1").show();
                  $("#delEmailModalOk2").show();

                  $("#delEmailModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#delEmailModal").modal('hide');
                     $("#delEmailModalOk1").hide();
                     $("#delEmailModalOk2").hide();
                     $("#delEmailModalBody1").show();
                     $("#delEmailModalBody2").show();
                     $("#delEmailModal div.modal-footer").show();
                     $("#emailIdToDel").val("");
                  }, 1500);
                  break;
            }
         },
         error: function (data)
         {
            console.log("Error");
            console.log(data);
         }
   });
}

