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

var addNewAddressConditionsArrayLoc, editAddressConditionsArrayLoc = null;

function setAddressBookGlobals(addNewAddressConditionsArrayLocAtt, editAddressConditionsArrayAtt)
{
    addNewAddressConditionsArrayLoc = addNewAddressConditionsArrayLocAtt;
    editAddressConditionsArrayLoc = editAddressConditionsArrayAtt;
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

function checkFormField(array, field)
{
    if($("#" + field).val().length < 1)
    {
        array[field] = false;
    }
    else
    {
        array[field] = true;
    }
    console.log("Check form field for field " + field + ": " + array[field]);
}

function checkAddNewAddressConditions()
{
    var enableButton = true;
    
    for(var key in addNewAddressConditionsArrayLoc) 
    {
        checkFormField(addNewAddressConditionsArrayLoc, key);
        if(addNewAddressConditionsArrayLoc[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#addEmailAddressConfirmBtn").attr("disabled", false);
        $("#addEmailAddressModalMsg").css("color", "black");
        $("#addEmailAddressModalMsg").html("All fields have been filled");
    }
    else
    {
        $("#addEmailAddressConfirmBtn").attr("disabled", true);
        $("#addEmailAddressModalMsg").css("color", "red");
        $("#addEmailAddressModalMsg").html("Some fields have not been filled");
    }
}

function checkEditAddressConditions()
{
    var enableButton = true;
    
    for(var key in editAddressConditionsArrayLoc) 
    {
        checkFormField(editAddressConditionsArrayLoc, key);
        if(editAddressConditionsArrayLoc[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#editEmailAddressConfirmBtn").attr("disabled", false);
        $("#editEmailAddressModalMsg").css("color", "black");
        $("#editEmailAddressModalMsg").html("All fields have been filled");
    }
    else
    {
        $("#editEmailAddressConfirmBtn").attr("disabled", true);
        $("#editEmailAddressModalMsg").css("color", "red");
        $("#editEmailAddressModalMsg").html("Some fields have not been filled");
    }
}

function getEmailAccountForEdit(id)
{
   $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "getEmailAccount",
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
                  $("#editEmailAddressModalBody").hide();
                  $("#editEmailAddressModalKo1").show();
                  $("#editEmailAddressModalKo2").show();

                  $("#editEmailAddressModalKo1").html("Error while trying to connect to application database, please try again");

                  $("#editEmailAddressModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#editEmailAddressModal").modal('hide');
                     $("#editEmailAddressModalKo1").html("");
                     $("#editEmailAddressModalKo1").hide();
                     $("#editEmailAddressModalKo2").hide();
                     $("#editEmailAddressModalBody").show();
                     $("#editEmailAddressModal div.modal-footer").show();
                  }, 1500);
                  break;

               case "queryKo":
                  $("#editEmailAddressModalBody").hide();
                  $("#editEmailAddressModalKo1").show();
                  $("#editEmailAddressModalKo2").show();

                  $("#editEmailAddressModalKo1").html("Error while trying to query application database, please try again");

                  $("#editEmailAddressModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#editEmailAddressModal").modal('hide');
                     $("#editEmailAddressModalKo1").html("");
                     $("#editEmailAddressModalKo1").hide();
                     $("#editEmailAddressModalKo2").hide();
                     $("#editEmailAddressModalBody").show();
                     $("#editEmailAddressModal div.modal-footer").show();
                  }, 1500);
                  break;

               default:
                  $("#editEmailAdrFirstName").val(data.account.fName);
                  $("#editEmailAdrLastName").val(data.account.lName);
                  $("#editEmailAdrOrg").val(data.account.org);
                  $("#editEmailAdrEmail").val(data.account.adr);
                  $("#editEmailAdrId").val(data.account.id);
                  checkEditAddressConditions();
                  $("#editEmailAddressModal").modal('show');
                  break;
            }
         },
         error: function (data)
         {
            $("#editEmailAddressModalBody").hide();
            $("#editEmailAddressModalKo1").show();
            $("#editEmailAddressModalKo2").show();

            $("#editEmailAddressModalKo1").html("Error while trying to call database API, please try again");

            $("#editEmailAddressModal div.modal-footer").hide();

            setTimeout(function()
            {
               $("#editEmailAddressModal").modal('hide');
               $("#editEmailAddressModalKo1").html("");
               $("#editEmailAddressModalKo1").hide();
               $("#editEmailAddressModalKo2").hide();
               $("#editEmailAddressModalBody").show();
               $("#editEmailAddressModal div.modal-footer").show();
            }, 1500);
            
            console.log("Error");
            console.log(data);
         }
   });
}

function editEmailAccount(id, rowId, fName, lName, org, adr)
{
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "editEmailAccount",
         id: id,
         adr: adr,
         fName: fName,
         lName: lName,
         org: org
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
               $("#editEmailAddressModalBody").hide();
               $("#editEmailAddressModalKo1").show();
               $("#editEmailAddressModalKo2").show();

               $("#editEmailAddressModalKo1").html("Error while trying to connect to application database, please try again");

               $("#editEmailAddressModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editEmailAddressModal").modal('hide');
                  $("#editEmailAddressModalKo1").html("");
                  $("#editEmailAddressModalKo1").hide();
                  $("#editEmailAddressModalKo2").hide();
                  $("#editEmailAddressModalBody").show();
                  $("#editEmailAddressModal div.modal-footer").show();
               }, 1500);
               break;

            case "queryKo":
               $("#editEmailAddressModalBody").hide();
                  $("#editEmailAddressModalKo1").show();
                  $("#editEmailAddressModalKo2").show();

                  $("#editEmailAddressModalKo1").html("Error while trying to updata data into application database, please try again");

                  $("#editEmailAddressModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#editEmailAddressModal").modal('hide');
                     $("#editEmailAddressModalKo1").html("");
                     $("#editEmailAddressModalKo1").hide();
                     $("#editEmailAddressModalKo2").hide();
                     $("#editEmailAddressModalBody").show();
                     $("#editEmailAddressModal div.modal-footer").show();
                  }, 1500);
               break;

            case "Ok":
               $('#emailAddressBookTable').bootstrapTable('updateCell', {
                  index: rowId,
                  field: "adr",
                  value: adr
               });
       
               $('#emailAddressBookTable').bootstrapTable('updateCell', {
                  index: rowId,
                  field: "fName",
                  value: fName
               });
       
               $('#emailAddressBookTable').bootstrapTable('updateCell', {
                  index: rowId,
                  field: "lName",
                  value: lName
               });
       
               $('#emailAddressBookTable').bootstrapTable('updateCell', {
                  index: rowId,
                  field: "org",
                  value: org
               });

               $("#editEmailAddressModalBody").hide();
               $("#editEmailAddressModalOk1").show();
               $("#editEmailAddressModalOk2").show();

               $("#editEmailAddressModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editEmailAddressModal").modal('hide');
                  $("#editEmailAddressModalOk1").hide();
                  $("#editEmailAddressModalOk2").hide();
                  $("#editEmailAddressModalBody").show();
                  $("#editEmailAddressModal div.modal-footer").show();
                  $("#editEmailId").val("");
                  $("#editEmailRowId").val("");
                  addNewAddressConditionsArrayLoc['editEmailAdrFirstName'] = false;
                  addNewAddressConditionsArrayLoc['editEmailAdrLastName'] = false;
                  addNewAddressConditionsArrayLoc['editEmailAdrOrg'] = false;
                  addNewAddressConditionsArrayLoc['editEmailAdrEmail'] = false;
               }, 1500);
               break;
         }
      },
      error: function (data)
      {
         $("#editEmailAddressModalBody").hide();
         $("#editEmailAddressModalKo1").show();
         $("#editEmailAddressModalKo2").show();

         $("#editEmailAddressModalKo1").html("Error while trying to call database API, please try again");

         $("#editEmailAddressModal div.modal-footer").hide();

         setTimeout(function()
         {
            $("#editEmailAddressModal").modal('hide');
            $("#editEmailAddressModalKo1").html("");
            $("#editEmailAddressModalKo1").hide();
            $("#editEmailAddressModalKo2").hide();
            $("#editEmailAddressModalBody").show();
            $("#editEmailAddressModal div.modal-footer").show();
         }, 1500);
         
         console.log("Error");
         console.log(data);
      }
   });
}


function deleteEmailAddress(id)
{
   console.log("ID: " + id);
    $.ajax({
         url: "restInterface.php",
         data: 
         {
            apiUsr: "alarmManager",
            apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
            operation: "deleteEmailAccount",
            id: id
         },
         type: "POST",
         async: true,
         dataType: 'json',
         success: function (data) 
         {
            console.log("ID: " + id);
            console.log(JSON.stringify(data));
            
            switch(data)
            {
               case "missingParams":
                  //TBD - Non gestiamolo, è impossibile che accada
                  break;

               case "dbConnKo":
                  $("#delEmailAddressModalBody").hide();
                  $("#delEmailAddressModalKo1").show();
                  $("#delEmailAddressModalKo2").show();

                  $("#delEmailAddressModalKo1").html("Error while trying to connect to application database");

                  $("#delEmailAddressModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#delEmailAddressModal").modal('hide');
                     $("#delEmailAddressModalKo1").html("");
                     $("#delEmailAddressModalKo1").hide();
                     $("#delEmailAddressModalKo2").hide();
                     $("#delEmailAddressModalBody").show();
                     $("#delEmailAddressModal div.modal-footer").show();
                     $("#emailIdToDel").val("");
                  }, 1500);
                  break;

               case "queryKo":
                  $("#delEmailAddressModalBody").hide();
                  $("#delEmailAddressModalKo1").show();
                  $("#delEmailAddressModalKo2").show();

                  $("#delEmailAddressModalKo1").html("Error while trying to query application database");

                  $("#delEmailAddressModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#delEmailAddressModal").modal('hide');
                     $("#delEmailAddressModalKo1").html("");
                     $("#delEmailAddressModalKo1").hide();
                     $("#delEmailAddressModalKo2").hide();
                     $("#delEmailAddressModalBody").show();
                     $("#delEmailAddressModal div.modal-footer").show();
                     $("#emailIdToDel").val("");
                  }, 1500);
                  break;

               default:
                  var ids = new Array();
                  ids.push($("#emailIdToDel").val());
                  $('#emailAddressBookTable').bootstrapTable('remove', {field: 'id', values: ids});
                  
                  $("#delEmailAddressModalBody").hide();
                  $("#delEmailAddressModalOk1").show();
                  $("#delEmailAddressModalOk2").show();

                  $("#delEmailAddressModal div.modal-footer").hide();

                  setTimeout(function()
                  {
                     $("#delEmailAddressModal").modal('hide');
                     $("#delEmailAddressModalOk1").hide();
                     $("#delEmailAddressModalOk2").hide();
                     $("#delEmailAddressModalBody").show();
                     $("#delEmailAddressModal div.modal-footer").show();
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

