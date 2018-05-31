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

function getDateForDatepicker(type)
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

   var year = parseInt(now.getFullYear());

   if(type === "oneYear")
   {
      year = year + 1;
   }
   
   if(type === "forever")
   {
      year = year + 99;
   }

   return day + "/" + month + "/" + year + " " + hour + ":" + min;
}

function loadEventsManagerTable(usrRole, loginAppLdapName, generatorId)
{
   var tableCols = null;
   
   $("#eventsManagerAppMenuContainer").hide();
   $("#dmEventsManagerTableContainer").hide();
   
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "getEventGenerators"
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function (data) 
      {
         switch(data.result)
         {
            case "queryKo":
               $("#dmEventsManagerTable").hide();
               $("#eventsManagerMsg").html("There was an error while retrieving generators from database, please try again");
               $("#eventsManagerMsg").show();
               break;

            case "Ok":
               $("#eventsManagerMsg").hide();
               
               if(usrRole === "ToolAdmin")
               {
                  $("#eventsManagerAppSelect").change(function()
                  {
                     $('#dmEventsManagerTable').bootstrapTable('destroy');
                     loadEventsManagerTableInnerFunction(data, tableCols, $("#eventsManagerAppSelect").val(), usrRole, false);
                  });
                  
                  loadEventsManagerTableInnerFunction(data, tableCols, $("#eventsManagerAppSelect").val(), usrRole, generatorId);
               }
               else
               {
                  loadEventsManagerTableInnerFunction(data, tableCols, loginAppLdapName, usrRole, generatorId);
               }
               break;

            default:
               $("#dmEventsManagerTable").hide();
               $("#eventsManagerMsg").html("There was an error calling database API, please try again");
               $("#eventsManagerMsg").show();
               break;
         }
      },
      error: function (data)
      {
         $("#dmEventsManagerTable").hide();
         $("#eventsManagerMsg").html("There was an error calling database API, please try again");
         $("#eventsManagerMsg").show();
         console.log("Error");
         console.log(JSON.stringify(data));
      }
   }); 
}

function loadEventsManagerTableInnerFunction(data, tableCols, appName, usrRole, generatorIdToShow)
{
   var generatorContainerLbl, generatorNameLbl, generatorTypeLbl, generatorCreatorLbl, generatorLinkLbl = null;
   
   generatorContainerLbl = data.data.labels[appName].containerTitleLabel;
   generatorNameLbl = data.data.labels[appName].genTitleLabel;
   generatorTypeLbl = data.data.labels[appName].genTypeLabel;
   generatorCreatorLbl = data.data.labels[appName].usrLabel;
   generatorLinkLbl = data.data.labels[appName].genLinkLabel;
   
   if(usrRole === "ToolAdmin")
   {
      tableCols = [         
      {
         title: generatorContainerLbl,
         align: "center",
         sortable: true,
         field: "containerName",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 50)
               {
                  return value.substr(0, 50) + " ...";
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
         title: generatorNameLbl,
         align: "center",
         sortable: true,
         field: "generatorOriginalName",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 40)
               {
                  return value.substr(0, 40) + " ...";
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
         title: generatorTypeLbl,
         align: "center",
         sortable: true,
         field: "generatorOriginalType",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 40)
               {
                  return value.substr(0, 40) + " ...";
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
         title: generatorCreatorLbl,
         align: "center",
         sortable: true,
         field: "appUsr",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 40)
               {
                  return value.substr(0, 40) + " ...";
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
         title: generatorLinkLbl,
         align: "center",
         valign: "middle",
         field: "url",
         formatter: function(value, row, index)
         {
             //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
             if((value !== 'about:blank')&&(value !== null)&&(value !== 'null'))
             {
                return '<a href="' + value + '" class="generatorLinkBtn" target="_blank"><i class="fa fa-link" style="font-size:24px;color:#07afc5"></i></a>'; 
             }
             else
             {
                return '-';
             }
         }
        },        
       {
         title: "Add/edit/delete notifications",
         align: "center",
         valign: "middle",
         formatter: function()
         {
             //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
             return '<a href="#" class="mngDmEventsBtn"><i class="fa fa-gear" style="font-size:24px;color:#07afc5"></i></a>';
         }
        }
      ];
   }
   else
   {
      tableCols = [         
      {
         title: generatorContainerLbl,
         align: "center",
         sortable: true,
         field: "containerName",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 50)
               {
                  return value.substr(0, 50) + " ...";
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
         title: generatorNameLbl,
         align: "center",
         sortable: true,
         field: "generatorOriginalName",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 40)
               {
                  return value.substr(0, 40) + " ...";
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
         title: generatorTypeLbl,
         align: "center",
         sortable: true,
         field: "generatorOriginalType",
         valign: "middle",
         formatter: function(value, row, index)
         {
            if((value !== null)&&(value !== 'null'))
            {
               if(value.length > 40)
               {
                  return value.substr(0, 40) + " ...";
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
         title: generatorLinkLbl,
         align: "center",
         valign: "middle",
         field: "url",
         formatter: function(value, row, index)
         {
             //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
             if((value !== 'about:blank')&&(value !== null)&&(value !== 'null'))
             {
                return '<a href="' + value + '" class="generatorLinkBtn" target="_blank"><i class="fa fa-link" style="font-size:24px;color:#07afc5"></i></a>'; 
             }
             else
             {
                return '-';
             }
         }
        },        
       {
         title: "Add/edit/delete notifications",
         align: "center",
         valign: "middle",
         formatter: function()
         {
             //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
             return '<a href="#" class="mngDmEventsBtn"><i class="fa fa-gear" style="font-size:24px;color:#07afc5"></i></a>';
         }
        }
      ];
   }
   
   $('#dmEventsManagerTable').bootstrapTable({
       data: data.data.generators[appName],
       columns: tableCols,
       search: true,
       pagination: true,
       pageSize: 10,
       locale: 'en-US',
       searchAlign: 'left',
       uniqueId: "id",
       rowAttributes: function(row, index){return {
       };},
       onPostBody: function(bodyData)
       {
           if(generatorIdToShow !== false)
           {
              console.log("Body rendered");
              console.log(data.data.generators[appName]);
              var tableData = data.data.generators[appName];
              var rowId, pageNumber = null;
              for(var i = 0; i < tableData.length; i++)
              {
                  if(tableData[i].id === generatorIdToShow)
                  {
                      rowId = i%10;
                      pageNumber = Math.floor(i/10) + 1;
                      console.log("Row: " + rowId + " - Page number: " + pageNumber);
                      break;
                  }
              }
              
              console.log(tableData[i]);
              
              
              $("#mngDmEventsModal").modal('show');

            $("#genId").val(generatorIdToShow);
            //DA QUI
            
            $("#mngDmEventsModal div.ruleRecapLbl").eq(0).html(data.data.labels[appName].containerTitleLabel);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(1).html(data.data.labels[appName].genTitleLabel);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(2).html(data.data.labels[appName].genTypeLabel);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(3).html(data.data.labels[appName].usrLabel);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(4).html(data.data.labels[appName].genLinkLabel);
            
            $("#mngDmEventsModal div.ruleRecapCnt").eq(0).html(tableData[i].containerName);
            $("#mngDmEventsModal div.ruleRecapCnt").eq(1).html(tableData[i].generatorOriginalName);
            $("#mngDmEventsModal div.ruleRecapCnt").eq(2).html(tableData[i].generatorOriginalType);
            $("#mngDmEventsModal div.ruleRecapCnt").eq(3).html(tableData[i].appUsr);
            var containerLink = tableData[i].url;
            
            if(usrRole === "ToolAdmin")
            {
               for(var i = 0; i < $("#mngDmEventsModal div.ruleRecapCnt").length; i++)
               {
                  if(i === 4)
                  {
                     var link = $(this).parent().parent().find("td").eq(4).find("a").attr("href");
                     $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html('<a href="' + link + '" class="generatorLinkBtn " target="_blank"><i class="fa fa-link"></i></a>');
                  }
                  else
                  {
                     $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html($(this).parent().parent().find("td").eq(i).html());
                  }
               }
            }
            else
            {
               for(var i = 0; i < $("#mngDmEventsModal div.ruleRecapCnt").length; i++)
               {
                  if(i === 3)
                  {
                     $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html("-");
                  }
                  else
                  {
                     if(i === 4)
                     {
                        var link = $(this).parent().parent().find("td").eq(3).find("a").attr("href");
                        $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html('<a href="' + link + '" class="generatorLinkBtn" target="_blank"><i class="fa fa-link"></i></a>');
                     }
                     else
                     {
                        $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html($(this).parent().parent().find("td").eq(i).html());
                     }
                  }
               }
            }
            
            
            $("#mngDmEventsModal div.ruleRecapLink a").attr("href", containerLink);
            
            loadGeneratorNotificationsTable(generatorIdToShow);
              
              
              
              
              
              /*setTimeout(function(){
                  $('#dmEventsManagerTable').bootstrapTable('selectPage', pageNumber);
                  $('#dmEventsManagerTable i.fa-gear').eq(rowId).trigger('click');
              }, 5000); */
               
              
              
           }
       },
       onAll: function(name, args)
       {
         $("div.pagination-detail").remove();
         
         $('#dmEventsManagerTable i.fa-link').off("hover");
         $('#dmEventsManagerTable i.fa-link').hover(function(){
            $(this).css("color", "#ffcc00");
         },
         function(){
            $(this).css("color", "#07afc5");
         });
         
         $('#dmEventsManagerTable i.fa-gear').off("hover");
         $('#dmEventsManagerTable i.fa-gear').hover(function(){
            $(this).css("color", "#ffcc00");
         },
         function(){
            $(this).css("color", "#07afc5");
         });
         
         //Nascondimento del loading ed esposizione della tabella
         $("#mainLoading").css("opacity", 0);
         setTimeout(function(){
            $("#mainLoading").hide();
            $("#eventsManagerAppMenuContainer").show();
            $("#dmEventsManagerTableContainer").show();
            setTimeout(function(){
               $("#eventsManagerAppMenuContainer").css("opacity", 1);
               $("#dmEventsManagerTableContainer").css("opacity", 1);
            }, 100);
         }, 100);
         
         //Rimpiazzo del messaggio di assenza dati con uno specifico.
         if($("#dmEventsManagerTable tbody tr.no-records-found").length > 0)
         {
            $("#dmEventsManagerTable tbody tr.no-records-found td").html("There are no generators in the system for the selection made");
         }

         //Listener pulsanti di gesione eventi per ogni regola
         $("a.mngDmEventsBtn").off();
         $("a.mngDmEventsBtn").click(function()
         {
            $("#mngDmEventsModal").modal('show');

            var genId = $(this).parent().parent().attr("data-uniqueid");
            $("#genId").val(genId);

            $("#mngDmEventsModal div.ruleRecapLbl").eq(0).html(generatorContainerLbl);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(1).html(generatorNameLbl);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(2).html(generatorTypeLbl);
            
            $("#mngDmEventsModal div.ruleRecapLbl").eq(3).html(generatorCreatorLbl);
            $("#mngDmEventsModal div.ruleRecapLbl").eq(4).html(generatorLinkLbl);
            
            if(usrRole === "ToolAdmin")
            {
               for(var i = 0; i < $("#mngDmEventsModal div.ruleRecapCnt").length; i++)
               {
                  if(i === 4)
                  {
                     var link = $(this).parent().parent().find("td").eq(4).find("a").attr("href");
                     $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html('<a href="' + link + '" class="generatorLinkBtn " target="_blank"><i class="fa fa-link"></i></a>');
                  }
                  else
                  {
                     $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html($(this).parent().parent().find("td").eq(i).html());
                  }
               }
            }
            else
            {
               for(var i = 0; i < $("#mngDmEventsModal div.ruleRecapCnt").length; i++)
               {
                  if(i === 3)
                  {
                     $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html("-");
                  }
                  else
                  {
                     if(i === 4)
                     {
                        var link = $(this).parent().parent().find("td").eq(3).find("a").attr("href");
                        $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html('<a href="' + link + '" class="generatorLinkBtn" target="_blank"><i class="fa fa-link"></i></a>');
                     }
                     else
                     {
                        $("#mngDmEventsModal div.ruleRecapCnt").eq(i).html($(this).parent().parent().find("td").eq(i).html());
                     }
                  }
               }
            }
            
            loadGeneratorNotificationsTable(genId);
         });
       }
   });
}
        
function loadGeneratorNotificationsTable(genId)
{   
   var tableCols = null;
   
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "getNotifications",
         genId: genId
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function (data) 
      {
         switch(data.result)
         {
            case "queryKo":
               $('#dmEmailNotificationsTable').bootstrapTable('destroy');
               $("#dmEmailNotificationsTable").hide();
               $("#dmEmailNotificationsMsg").html("There was an error while retrieving notifications from database, please try again");
               $("#dmEmailNotificationsMsg").show();
               break;
               
            case "Ok":
               $("#dmEmailNotificationsMsg").hide();
               $("#dmEmailNotificationsTable").show();
               tableCols = [
               {
                   title: "Event type",
                   align: "center",
                   sortable: true,
                   field: "eventType",
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
                   title: "Notification name",
                   align: "center",
                   sortable: true,
                   field: "name",
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
                  title: "Active",
                  align: "center",
                  sortable: true,
                  field: "val",
                  valign: "middle",
                  formatter: function(value, row, index)
                  {
                     if(value === '1')
                     {
                        return "Yes";
                     }
                     else
                     {
                        return "No";
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
                   title: "Validity start",
                   align: "center",
                   sortable: true,
                   field: "valStart",
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
                   title: "Validity end",
                   align: "center",
                   sortable: true,
                   field: "valEnd",
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
                  sortable: true,
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
                  title: "Edit notification",
                  align: "center",
                  valign: "middle",
                  formatter: function()
                  {
                      //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
                      return '<a href="#" class="mngDmNotifBtn"><i class="fa fa-gear" style="font-size:24px;color:#07afc5"></i></a>'; 
                  }
                 },
                 {
                  title: "Delete notification",
                  align: "center",
                  valign: "middle",
                  formatter: function()
                  {
                      //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
                      return '<a href="#" class="delDmNotifBtn"><i class="fa fa-close" style="font-size:24px;color:red"></i></a>'; 
                  }
                 }
               ];
               
               $('#dmEmailNotificationsTable').bootstrapTable('destroy');
               $('#dmEmailNotificationsTable').bootstrapTable({
                   data: data.notifications,
                   columns: tableCols,
                   search: true,
                   pagination: true,
                   pageSize: 5,
                   locale: 'en-US',
                   searchAlign: 'left',
                   uniqueId: "id",
                   rowAttributes: function(row, index){return {
                       msgid: data.notifications[index].mid
                   };},
                   onAll: function(name, args)
                   {
                     $("div.pagination-detail").remove();
                     
                     $('#dmEmailNotificationsTable i.fa-gear').off("hover");
                     $('#dmEmailNotificationsTable i.fa-gear').hover(function(){
                        $(this).css("color", "#ffcc00");
                     },
                     function(){
                        $(this).css("color", "#07afc5");
                     });

                     $('#dmEmailNotificationsTable i.fa-close').off("hover");
                     $('#dmEmailNotificationsTable i.fa-close').hover(function(){
                        $(this).css("color", "#ffcc00");
                     },
                     function(){
                        $(this).css("color", "red");
                     });
                     
                     //Aggiunta del pulsante aggiungi notifica allineato al search field
                     if($("#mngDmEventsModalTableContainer div.fixed-table-toolbar #addNotificationBtn").length > 0)
                     {
                        $("#mngDmEventsModalTableContainer div.fixed-table-toolbar #addNotificationBtn").remove();
                     }
                     
                     $("#mngDmEventsModalTableContainer div.fixed-table-toolbar").append('<div class="pull-right centerWithFlex" id="addNotificationBtn" data-toggle="tooltip" title="Add a new notification for this generator: this will associate one event type with a message and one or more recipients"><img src="img/emailBook/plusYellow.png" width="32px" height="32px" /></div>');
                     $("#addNotificationBtn").off();
                     $("#addNotificationBtn").tooltip();
                     
                     $("#addNotificationBtn").hover(
                        function() 
                        {
                           $(this).find("img").attr("src", "img/emailBook/plusRed.png");
                           $(this).css("cursor", "pointer");
                        }, 
                        function() 
                        {
                            $(this).find("img").attr("src", "img/emailBook/plusYellow.png");
                        }
                     );
                     
                     $("#addNotificationBtn").click(function()
                     {
                        $("#mngDmEventsModal").modal('hide');

                        //Aggiornamento del message book integrato nel form di aggiunta notifica
                        $.ajax({
                            url: "restInterface.php",
                            data: 
                            {
                               apiUsr: "alarmManager",
                               apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
                               operation: "getGeneratorEvents",
                               generatorId: $("#genId").val()
                            },
                            type: "POST",
                            async: true,
                            dataType: 'json',
                            success: function(data) 
                            {
                               switch(data.result)
                               {
                                  case "queryKo":
                                     //TBD
                                     break;

                                  case "noEvents":
                                     $("#addDmEmailNotificationImpossibleModal div.modal-body").html("This generator has no event types associated with it, so it's not possibile to add notifications.");
                                     $("#addDmEmailNotificationImpossibleModal").modal('show');
                                     setTimeout(function(){
                                        $("#addDmEmailNotificationImpossibleModal").modal('hide');
                                        $("#mngDmEventsModal").modal('show');
                                     }, 3000);
                                     break;

                                  case "Ok":
                                     $("#addDmEmailNotificationEventSelect").empty();
                                     for(var i = 0; i < data.events.length; i++)
                                     {
                                        data.events[i].eventType = data.events[i].eventType.replace("<", "&lt;");
                                        data.events[i].eventType = data.events[i].eventType.replace(">", "&gt;");
                                        data.events[i].eventType = data.events[i].eventType.replace("=", "&equals;");
                                        $("#addDmEmailNotificationEventSelect").append('<option value="' + data.events[i].id + '">' + data.events[i].eventType + '</option>');
                                     }

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
                                                $("#addDmEmailNotificationEmailBookTable").hide();
                                                $("#addDmEmailNotificationEmailBookMsg").html("There was an error while retrieving emails from database, please try again");
                                                $("#addDmEmailNotificationEmailBookMsg").show();
                                                break;

                                             case "Ok":
                                                tableCols = [
                                                {
                                                   title: "Choosen message",
                                                   align: "center",
                                                   valign: "middle",
                                                   field: "id",
                                                   formatter: function(value, row, index)
                                                   {
                                                       return '<input type="radio" name="chooseMsgRadio" value="' + value + '"/>'; 
                                                   }
                                                },
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
                                                 }
                                                ];

                                                $('#addDmEmailNotificationEmailBookTable').bootstrapTable('destroy');

                                                $('#addDmEmailNotificationEmailBookTable').bootstrapTable({
                                                    data: data.emails,
                                                    columns: tableCols,
                                                    search: true,
                                                    pagination: true,
                                                    pageSize: 5,
                                                    locale: 'en-US',
                                                    searchAlign: 'left',
                                                    onAll: function(name, args)
                                                    {
                                                      $("div.pagination-detail").remove();

                                                      $("#addDmEmailNotificationEmailBookTable input[name=chooseMsgRadio]").off("click");
                                                      $("#addDmEmailNotificationEmailBookTable input[name=chooseMsgRadio]").click(function()
                                                      {
                                                         $("#selectedMsgId").val($(this).val());
                                                      });
                                                    }
                                                });

                                                //Rimpiazzo del messaggio di assenza dati con uno specifico.
                                                if($("#addDmEmailNotificationEmailBookTable tbody tr.no-records-found").length > 0)
                                                {
                                                   $("#addDmEmailNotificationEmailBookTable tbody tr.no-records-found td").html("There are no messages in the system");
                                                }

                                                if($("#addDmEmailNotificationEmailBookTable tbody tr").length > 0)
                                                {
                                                   $("#addDmEmailNotificationEmailBookTable tbody tr").eq(0).find("td").eq(0).find('input').trigger("click");
                                                }

                                                //Questo svuota la selezione della select2
                                                $('#addDmEmailNotificationRecFromBook').val([]).trigger("change");
                                                
                                                //Init delle date di default dei due timepicker
                                                var startDate = getDateForDatepicker("now");
                                                var endDate = getDateForDatepicker("forever");
                                                
                                                $('#addDmEmailNotificationValStart').data("DateTimePicker").date(startDate);
                                                $('#addDmEmailNotificationValEnd').data("DateTimePicker").date(endDate);
                                                
                                                //Viene aperto il modale
                                                $("#addEmailNotificationDmModal").modal('show');
                                                break;

                                             default:
                                                $("#addDmEmailNotificationEmailBookTable").hide();
                                                $("#addDmEmailNotificationEmailBookMsg").html("There was an error while retrieving emails from database, please try again");
                                                $("#addDmEmailNotificationEmailBookMsg").show();
                                                break;
                                          }
                                       },
                                       error: function (data)
                                       {
                                          console.log("Error");
                                          console.log(JSON.stringify(data));
                                       }
                                    });

                                     break;
                               }
                            },
                            error: function(data)
                            {
                               //TBD
                               console.log("Error");
                               console.log(data);
                            }
                         });
                     });
                     
                     //Listener pulsanti di gesione per ogni notifica
                     $("a.mngDmNotifBtn").off();
                     $("a.mngDmNotifBtn").click(function()
                     {
                        //Aggiornamento del message book integrato nel form di edit notifica
                        $.ajax({
                           url: "restInterface.php",
                           data: 
                           {
                              apiUsr: "alarmManager",
                              apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
                              operation: "getEmails"
                           },
                           type: "POST",
                           async: false,
                           dataType: 'json',
                           success: function (data) 
                           {
                              switch(data.result)
                              {
                                 case "queryKo":
                                    $("#addDmEmailNotificationEmailBookTable").hide();
                                    $("#addDmEmailNotificationEmailBookMsg").html("There was an error while retrieving emails from database, please try again");
                                    $("#addDmEmailNotificationEmailBookMsg").show();
                                    break;

                                 case "noEmails":
                                    $("#addDmEmailNotificationEmailBookTable").hide();
                                    $("#addDmEmailNotificationEmailBookMsg").html("There are no addresses in the book");
                                    $("#addDmEmailNotificationEmailBookMsg").show();
                                    break;

                                 case "Ok":
                                    tableCols = [
                                    {
                                       title: "Choosen message",
                                       align: "center",
                                       valign: "middle",
                                       field: "id",
                                       formatter: function(value, row, index)
                                       {
                                           return '<input type="radio" name="chooseMsgRadio" value="' + value + '"/>'; 
                                       }
                                    },
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
                                     }
                                    ];

                                    $('#editDmEmailNotificationEmailBookTable').bootstrapTable('destroy');
                                    $('#editDmEmailNotificationEmailBookTable').bootstrapTable({
                                          data: data.emails,
                                          columns: tableCols,
                                          search: true,
                                          pagination: true,
                                          pageSize: 5,
                                          locale: 'en-US',
                                          searchAlign: 'left',
                                          uniqueid: "id",
                                          onAll: function(name, args)
                                          {
                                            $("div.pagination-detail").remove();

                                            $("#editDmEmailNotificationEmailBookTable input[name=chooseMsgRadio]").off("click");
                                            $("#editDmEmailNotificationEmailBookTable input[name=chooseMsgRadio]").click(function()
                                            {
                                               $("#selectedMsgIdEdit").val($(this).val());
                                            });
                                          }
                                      });
                                        
                                    break;

                                 default:
                                    $("#addDmEmailNotificationEmailBookTable").hide();
                                    $("#addDmEmailNotificationEmailBookMsg").html("There was an error while retrieving emails from database, please try again");
                                    $("#addDmEmailNotificationEmailBookMsg").show();
                                    break;
                              }
                           },
                           error: function (data)
                           {
                              console.log("Error");
                              console.log(JSON.stringify(data));
                           }
                        });//Fine aggiornamento del message book integrato nel form di edit notifica
                        
                        loadEditNotificationForm($(this).parent().parent().attr("data-uniqueid"));
                        
                     });//Fine gestore del click su edit notifica
                     
                     //Rimpiazzo del messaggio di assenza dati con uno specifico.
                     if($("#dmEmailNotificationsTable tbody tr.no-records-found").length > 0)
                     {
                        $("#dmEmailNotificationsTable tbody tr.no-records-found td").html("There are no notifications associated with this generator");
                     }

                     //Listener pulsanti di cancellazione per ogni notifica 
                     $("a.delDmNotifBtn").off();
                     $("a.delDmNotifBtn").click(function()
                     {
                        $("#mngDmEventsModal").modal('hide');
                        $("#delDmNotificationModal").modal('show');
                        $("#delDmNotificationId").val($(this).parent().parent().attr("data-uniqueid"));
                        $("#delDmNotificationName").val($(this).parent().parent().find("td").eq(1).html());
                        $("#delDmNotificationModalBody2").html($("#delDmNotificationName").val());

                        $("#delDmNotificationCancelBtn").off();
                        $("#delDmNotificationConfirmBtn").off();

                        $("#delDmNotificationCancelBtn").click(function(){
                           $("#delDmNotificationModal").modal('hide');
                           $("#mngDmEventsModal").modal('show');
                        });

                        $("#delDmNotificationConfirmBtn").click(function(){
                           $.ajax({
                              url: "restInterface.php",
                              data: 
                              {
                                 apiUsr: "alarmManager",
                                 apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
                                 operation: "delDmNotification",
                                 id: $("#delDmNotificationId").val()
                              },
                              type: "POST",
                              async: true,
                              dataType: 'json',
                              success: function (data) 
                              {
                                 switch(data.result)
                                 {
                                    case "queryKo":
                                       $("#delDmNotificationModalBody1").hide();
                                       $("#delDmNotificationModalBody2").hide();
                                       $("#delDmNotificationModal div.modal-footer").hide();
                                       $("#delDmNotificationModalKo1").html("There was an error while updating database, please try again");
                                       $("#delDmNotificationModalKo1").show();
                                       $("#delDmNotificationModalKo2").show();

                                       setTimeout(function()
                                       {
                                          $("#delDmNotificationModal").modal('hide');
                                          $("#delDmNotificationModalKo1").hide();
                                          $("#delDmNotificationModalKo2").hide();
                                          $("#delDmNotificationModalBody1").show();
                                          $("#delDmNotificationModalBody2").show();
                                          $("#delDmNotificationModal div.modal-footer").show();
                                          $("#mngDmEventsModal").modal('show');
                                       }, 2000);
                                       break;

                                    case "Ok":
                                       $("#delDmNotificationModalBody1").hide();
                                       $("#delDmNotificationModalBody2").hide();
                                       $("#delDmNotificationModal div.modal-footer").hide();
                                       $("#delDmNotificationModalOk1").show();
                                       $("#delDmNotificationModalOk2").show();

                                       var ids = new Array();
                                       ids.push($("#delDmNotificationId").val());

                                       $('#dmEmailNotificationsTable').bootstrapTable('remove', {
                                          field: "id",
                                          values: ids
                                       });
                                       
                                       //Rimpiazzo del messaggio di assenza dati con uno specifico.
                                       if($("#dmEmailNotificationsTable tbody tr.no-records-found").length > 0)
                                       {
                                          $("#dmEmailNotificationsTable tbody tr.no-records-found td").html("There are no notifications associated with this generator");
                                       }

                                       setTimeout(function()
                                       {
                                          $("#delDmNotificationModal").modal('hide');
                                          $("#delDmNotificationModalOk1").hide();
                                          $("#delDmNotificationModalOk2").hide();
                                          $("#delDmNotificationModalBody1").show();
                                          $("#delDmNotificationModalBody2").show();
                                          $("#delDmNotificationModal div.modal-footer").show();
                                          $("#mngDmEventsModal").modal('show');
                                          $("#delDmNotificationId").val("");
                                          $("#delDmNotificationName").val("");
                                       }, 2000);
                                       break;

                                    default:
                                       $("#delDmNotificationModalBody1").hide();
                                       $("#delDmNotificationModalBody2").hide();
                                       $("#delDmNotificationModal div.modal-footer").hide();
                                       $("#delDmNotificationModalKo1").html("There was an error while updating database, please try again");
                                       $("#delDmNotificationModalKo1").show();
                                       $("#delDmNotificationModalKo2").show();

                                       setTimeout(function()
                                       {
                                          $("#delDmNotificationModal").modal('hide');
                                          $("#delDmNotificationModalKo1").hide();
                                          $("#delDmNotificationModalKo2").hide();
                                          $("#delDmNotificationModalBody1").show();
                                          $("#delDmNotificationModalBody2").show();
                                          $("#delDmNotificationModal div.modal-footer").show();
                                          $("#mngDmEventsModal").modal('show');
                                       }, 2000);
                                       break;
                                 }
                              },
                              error: function(data)
                              {
                                 $("#delDmNotificationModalBody1").hide();
                                 $("#delDmNotificationModalBody2").hide();
                                 $("#delDmNotificationModal div.modal-footer").hide();
                                 $("#delDmNotificationModalKo1").html("There was an error while calling backend API, please try again");
                                 $("#delDmNotificationModalKo1").show();
                                 $("#delDmNotificationModalKo2").show();
                                 console.log("Error");
                                 console.log(JSON.stringify(data));
                                 setTimeout(function()
                                 {
                                    $("#delDmNotificationModal").modal('hide');
                                    $("#delDmNotificationModalKo1").hide();
                                    $("#delDmNotificationModalKo2").hide();
                                    $("#delDmNotificationModalBody1").show();
                                    $("#delDmNotificationModalBody2").show();
                                    $("#delDmNotificationModal div.modal-footer").show();
                                    $("#mngDmEventsModal").modal('show');
                                 }, 2000);
                              }
                           });
                        });
                     });
                   }
               });
               break;
               
            default:
               $('#dmEmailNotificationsTable').bootstrapTable('destroy');
               $("#dmEmailNotificationsTable").hide();
               $("#dmEmailNotificationsMsg").html("There was an error while calling backend API, please try again");
               $("#dmEmailNotificationsMsg").show();
               break;   
         }
      },
      error: function (data)
      {
         console.log("Error");
         console.log(JSON.stringify(data));
      }
   });
}            

function loadEditNotificationForm(notificationId)
{
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "loadEditNotificationForm",
         notificationId: notificationId
      },
      type: "POST",
      async: false,
      dataType: 'json',
      success: function(data) 
      {
         switch(data.result)
         {
            case "missingParams":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalKo1").show();
               $("#editDmEmailNotificationModalKo2").show();

               $("#editDmEmailNotificationModalKo1").html("Notification data could not be retrieved because notification id is not available, please try again");

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editDmEmailNotificationModalKo1").hide();
                  $("#editDmEmailNotificationModalKo2").hide();
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
                  $("#editEmailNotificationDmModal").modal('hide');
                  $("#mngDmEventsModal").modal('show');
               }, 2500);
               break;

            case "dbConnKo":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalKo1").show();
               $("#editDmEmailNotificationModalKo2").show();

               $("#editDmEmailNotificationModalKo1").html("Notification data could not be retrieved because of a database connection failure, please try again");

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editDmEmailNotificationModalKo1").hide();
                  $("#editDmEmailNotificationModalKo2").hide();
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
                  $("#editEmailNotificationDmModal").modal('hide');
                  $("#mngDmEventsModal").modal('show');
               }, 2500);
               break;

            case "queryKo":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalKo1").show();
               $("#editDmEmailNotificationModalKo2").show();

               $("#editDmEmailNotificationModalKo1").html("Notification data could not be retrieved because of a failure during database query, please try again");

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editDmEmailNotificationModalKo1").hide();
                  $("#editDmEmailNotificationModalKo2").hide();
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
                  $("#editEmailNotificationDmModal").modal('hide');
                  $("#mngDmEventsModal").modal('show');
               }, 2500);
               break;

            case "Ok":
               $("#editDmEmailNotificationId").val(notificationId);
               $("#editDmEmailNotificationName").val(data.notification.name);
               
               if(data.notification.val === '1')
               {
                  $('#editDmEmailNotificationVal').bootstrapToggle('on');
               }
               else
               {
                  $('#editDmEmailNotificationVal').bootstrapToggle('off');
               }
               
               $('#editDmEmailNotificationEventSelect').empty();
               
               for(var i = 0; i < data.notification.eventsList.length; i++)
               {
                  data.notification.eventsList[i].eventType = data.notification.eventsList[i].eventType.replace("<", "&lt;");
                  data.notification.eventsList[i].eventType = data.notification.eventsList[i].eventType.replace(">", "&gt;");
                  data.notification.eventsList[i].eventType = data.notification.eventsList[i].eventType.replace("=", "&equals;");
                  $('#editDmEmailNotificationEventSelect').append('<option value="' + data.notification.eventsList[i].id + '">' + data.notification.eventsList[i].eventType + '</option>');
               }
               
               $('#editDmEmailNotificationEventSelect').val(data.notification.eventId);
               
               var year = data.notification.valStart.substr(0, 4);
               var month = data.notification.valStart.substr(5, 7);
               var day = data.notification.valStart.substr(8, 10);
               var hour = data.notification.valStart.substr(11, 13);
               var min = data.notification.valStart.substr(14, 16);
               var valStartFormatted = day + "/" + month + "/" + year + " " + hour + ":" + min;
               
               $('#editDmEmailNotificationValStart').data("DateTimePicker").date(valStartFormatted);
               
               year = data.notification.valEnd.substr(0, 4);
               month = data.notification.valEnd.substr(5, 7);
               day = data.notification.valEnd.substr(8, 10);
               hour = data.notification.valEnd.substr(11, 13);
               min = data.notification.valEnd.substr(14, 16);
               var valEndFormatted = day + "/" + month + "/" + year + " " + hour + ":" + min;
               
               $('#editDmEmailNotificationValEnd').data("DateTimePicker").date(valEndFormatted);
               var recAddresses = new Array();
               for(var i = 0; i < data.notification.rec.length; i++)
               {
                  recAddresses.push(data.notification.rec[i]);
               }
                             
               $('#editDmEmailNotificationRecFromBook').val(recAddresses).change();
               
               function scanEditDmEmailNotificationEmailBookTableExternal(e) 
               {
                  var tableData = $('#editDmEmailNotificationEmailBookTable').bootstrapTable('getData', false);
                  var pageNumber;
                  
                  for(var i in tableData)
                  {
                     if(tableData[i].id === data.notification.msgId)
                     {
                        pageNumber = Math.floor(i / 5) + 1;
                        continue;
                     }
                  }
                  
                  $('#editDmEmailNotificationEmailBookTable').bootstrapTable('selectPage', pageNumber);
                  $("#editDmEmailNotificationEmailBookTable tbody tr").each(function(i)
                  {
                      if($(this).find("td").eq(0).find("input").val() ===  data.notification.msgId)
                      {
                         $(this).find("td").eq(0).find("input").attr("checked", true);
                         $("#selectedMsgIdEdit").val($(this).find("td").eq(0).find("input").val());
                      }
                  });
               }
               
               $("#mngDmEventsModal").modal('hide');
               $("#editEmailNotificationDmModal").modal('show');
               
               $('#editEmailNotificationDmModal').off('shown.bs.modal', scanEditDmEmailNotificationEmailBookTableExternal);
               $('#editEmailNotificationDmModal').on('shown.bs.modal', scanEditDmEmailNotificationEmailBookTableExternal);
               break;
         }
      },
      error: function(data)
      {
         console.log("Ko");
         console.log(data);
         
         $("#editEmailNotificationDmModalBody").hide();
         $("#editDmEmailNotificationModalKo1").show();
         $("#editDmEmailNotificationModalKo2").show();

         $("#editDmEmailNotificationModalKo1").html("Notification data could not be retrieved because of an API failure, please try again");

         $("#editEmailNotificationDmModal div.modal-footer").hide();

         setTimeout(function()
         {
            $("#editDmEmailNotificationModalKo1").hide();
            $("#editDmEmailNotificationModalKo2").hide();
            $("#editEmailNotificationDmModalBody").show();
            $("#editEmailNotificationDmModal div.modal-footer").show();
            $("#editEmailNotificationDmModal").modal('hide');
            $("#mngDmEventsModal").modal('show');
         }, 2500);
      }
   });
}
                                           
function addNotification(name, genId, eventId, val, valStart, valEnd, recFromBook, recManual, msgId, sub, txt)
{
   if(val)
   {
      val = 1;
   }
   else
   {
      val = 0;
   }
   
   valStart = valStart + ":00";
   valEnd = valEnd + ":00";
   
   var year = valStart.substr(6 ,4);
   var month = valStart.substr(3 ,2);
   var day = valStart.substr(0 ,2);
   var hour = valStart.substr(11 ,2);
   var min = valStart.substr(14 ,2);
   var sec = "00";
   
   var valStartFormatted = year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
   
   var year = valEnd.substr(6 ,4);
   var month = valEnd.substr(3 ,2);
   var day = valEnd.substr(0 ,2);
   var hour = valEnd.substr(11 ,2);
   var min = valEnd.substr(14 ,2);
   var sec = "00";
   
   var valEndFormatted = year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
   
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "insertNotification",
         name: name,
         genId: genId,
         eventId: eventId,
         val: val,
         valStart: valStartFormatted,
         valEnd: valEndFormatted,
         recFromBook: recFromBook,
         recManual: recManual,
         msgId: msgId,
         sub: sub,
         txt: txt
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function(data) 
      {
         switch(data.result)
         {
            case "missingParams":
               $("#addEmailNotificationDmModalBody").hide();
               $("#addDmEmailNotificationModalKo1").show();
               $("#addDmEmailNotificationModalKo2").show();

               $("#addDmEmailNotificationModalKo1").html("Some mandatory fields are empty, please fill them and try again");

               $("#addEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#addDmEmailNotificationModalKo1").hide();
                  $("#addDmEmailNotificationModalKo2").hide();
                  $("#addEmailNotificationDmModalBody").show();
                  $("#addEmailNotificationDmModal div.modal-footer").show();
               }, 2500);
               break;

            case "dbConnKo":
               $("#addEmailNotificationDmModalBody").hide();
               $("#addDmEmailNotificationModalKo1").show();
               $("#addDmEmailNotificationModalKo2").show();

               $("#addDmEmailNotificationModalKo1").html("Error while trying to connect to application database, please try again");

               $("#addEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#addDmEmailNotificationModalKo1").hide();
                  $("#addDmEmailNotificationModalKo2").hide();
                  $("#addEmailNotificationDmModalBody").show();
                  $("#addEmailNotificationDmModal div.modal-footer").show();
               }, 2500);
               break;

            case "queryKo":
               $("#addEmailNotificationDmModalBody").hide();
               $("#addDmEmailNotificationModalKo1").show();
               $("#addDmEmailNotificationModalKo2").show();

               $("#addDmEmailNotificationModalKo1").html("Error while trying to query application database, please try again");

               $("#addEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#addDmEmailNotificationModalKo1").hide();
                  $("#addDmEmailNotificationModalKo2").hide();
                  $("#addEmailNotificationDmModalBody").show();
                  $("#addEmailNotificationDmModal div.modal-footer").show();
               }, 2500);
               break;

            case "Ok":
               $("#addEmailNotificationDmModalBody").hide();
               $("#addDmEmailNotificationModalOk1").show();
               $("#addDmEmailNotificationModalOk2").show();

               $("#addEmailNotificationDmModal div.modal-footer").hide();
               
               setTimeout(function()
               {
                  $("#addEmailNotificationDmModal").modal('hide');
                  $("#addDmEmailNotificationModalOk1").hide();
                  $("#addDmEmailNotificationModalOk2").hide();
                  $("#addDmEmailNotificationName").val("");
                  $('#addDmEmailNotificationVal').bootstrapToggle('on');
                  
                  var startDate = getDateForPicker("now");
                  var endDate = getDateForPicker("forever");
                  
                  $("#addDmEmailNotificationValStart").data("DateTimePicker").date(startDate);
                  $("#addDmEmailNotificationValEnd").data("DateTimePicker").date(endDate);
                  
                  $('#addDmEmailNotificationRecFromBook').select2().each(function(i, item){
                    $(item).select2("destroy");
                  });
                  
                  $("#addDmEmailNotificationRecFromBook").select2({
                   data: data.newDataForSelect,
                   multiple: true,
                   allowClear: true
                  });
                  
                  $('#editDmEmailNotificationRecFromBook').select2().each(function(i, item){
                    $(item).select2("destroy");
                  });
                  
                  $("#editDmEmailNotificationRecFromBook").select2({
                   data: data.newDataForSelect,
                   multiple: true,
                   allowClear: true
                  });
                  
                  
                  $("#addDmEmailNotificationRecManual").val("");
                  $('#addDmEmailNotificationMsgSrc').bootstrapToggle('on');
                  //Non resettiamo il radiobutton della tabella, per ora, levargli l'attributo "checked" potrebbe dar problemi
                  $("#addDmEmailNotificationMsgComposerSub").val("[[Auto]]");
                  var defaultText = "Dear recipient, <br>" +
                                    "the following event has occurred:<br>" + 
                                    "[[EventDetails]]<br>" +
                                    "Regards.<br>" + 
                                    "<strong>DISIT Notification System</strong>";
                  CKEDITOR.instances['addDmEmailNotificationMsgComposerTxt'].setData(defaultText);
                  loadGeneratorNotificationsTable(genId);
                  $("#addEmailNotificationDmModalBody").show();
                  $("#addEmailNotificationDmModal div.modal-footer").show();
                  $("#mngDmEventsModal").modal('show');
               }, 1500);
               break;
         }
      },
      error: function(data)
      {
         $("#addEmailNotificationDmModalBody").hide();
         $("#addDmEmailNotificationModalKo1").show();
         $("#addDmEmailNotificationModalKo2").show();
         $("#addDmEmailNotificationModalKo1").html("Error while trying to call database API, please try again");
         $("#addEmailNotificationDmModal div.modal-footer").hide();

         setTimeout(function()
         {
            $("#addDmEmailNotificationModalKo1").hide();
            $("#addDmEmailNotificationModalKo2").hide();
            $("#addEmailNotificationDmModalBody").show();
            $("#addEmailNotificationDmModal div.modal-footer").show();
         }, 2500);
         
         console.log("Error");
         console.log(JSON.stringify(data));
      }
   });
}                                             

function editNotification(id, name, genId, eventId, val, valStart, valEnd, recFromBook, recManual, msgId, sub, txt)
{
   if(val === true)
   {
      val = 1;
   }
   else
   {
      val = 0;
   }
   
   valStart = valStart + ":00";
   valEnd = valEnd + ":00";
   
   var year = valStart.substr(6 ,4);
   var month = valStart.substr(3 ,2);
   var day = valStart.substr(0 ,2);
   var hour = valStart.substr(11 ,2);
   var min = valStart.substr(14 ,2);
   var sec = "00";
   
   var valStartFormatted = year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
   
   var year = valEnd.substr(6 ,4);
   var month = valEnd.substr(3 ,2);
   var day = valEnd.substr(0 ,2);
   var hour = valEnd.substr(11 ,2);
   var min = valEnd.substr(14 ,2);
   var sec = "00";
   
   var valEndFormatted = year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
      
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "editNotification",
         id: id,
         name: name,
         eventId: eventId,
         val: val,
         valStart: valStartFormatted,
         valEnd: valEndFormatted,
         recFromBook: recFromBook,
         recManual: recManual,
         msgId: msgId,
         sub: sub,
         txt: txt
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function(data) 
      {
         switch(data.result)
         {
            case "missingParams":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalKo3").show();
               $("#editDmEmailNotificationModalKo4").show();

               $("#editDmEmailNotificationModalKo3").html("Some mandatory fields are empty, please fill them and try again");

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editDmEmailNotificationModalKo3").hide();
                  $("#editDmEmailNotificationModalKo4").hide();
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
               }, 2500);
               break;

            case "dbConnKo":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalKo3").show();
               $("#editDmEmailNotificationModalKo4").show();

               $("#editDmEmailNotificationModalKo3").html("Error while trying to connect to application database, please try again");

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editDmEmailNotificationModalKo3").hide();
                  $("#editDmEmailNotificationModalKo4").hide();
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
               }, 2500);
               break;

            case "queryKo":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalKo3").show();
               $("#editDmEmailNotificationModalKo4").show();

               $("#editDmEmailNotificationModalKo3").html("Error while trying to query application database, please try again");

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editDmEmailNotificationModalKo3").hide();
                  $("#editDmEmailNotificationModalKo4").hide();
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
               }, 2500);
               break;

            case "Ok":
               $("#editEmailNotificationDmModalBody").hide();
               $("#editDmEmailNotificationModalOk1").show();
               $("#editDmEmailNotificationModalOk2").show();

               $("#editEmailNotificationDmModal div.modal-footer").hide();

               setTimeout(function()
               {
                  $("#editEmailNotificationDmModal").modal('hide');
                  $("#editDmEmailNotificationModalOk1").hide();
                  $("#editDmEmailNotificationModalOk2").hide();
                  $("#editDmEmailNotificationName").val("");
                  $('#editDmEmailNotificationVal').bootstrapToggle('on');
                  $('#editDmEmailNotificationValStart').data("DateTimePicker").clear();
                  $('#editDmEmailNotificationValEnd').data("DateTimePicker").clear();
                  
                  var $select = $('#editDmEmailNotificationRecFromBook').select2();
                  $select.each(function(i,item){
                    $(item).select2("destroy");
                  });
                  
                  $("#editDmEmailNotificationRecFromBook").select2({
                     data: data.newDataForSelect,
                     multiple: true,
                     allowClear: true
                  });
                  
                  $("#editDmEmailNotificationRecManual").val("");
                  $('#editDmEmailNotificationMsgSrc').bootstrapToggle('on');
                  //Non resettiamo il radiobutton della tabella, per ora, levargli l'attributo "checked" potrebbe dar problemi
                  $("#editDmEmailNotificationMsgComposerSub").val("[[Auto]]");
                  var defaultText = "Dear recipient, <br>" +
                            "the following event has occurred:<br>" + 
                            "[[EventDetails]]<br>" +
                            "Regards.<br>" + 
                            "<strong>DISIT Notification System</strong>";
                  CKEDITOR.instances['editDmEmailNotificationMsgComposerTxt'].setData(defaultText);
                  loadGeneratorNotificationsTable(genId);
                  $("#editEmailNotificationDmModalBody").show();
                  $("#editEmailNotificationDmModal div.modal-footer").show();
                  $("#mngDmEventsModal").modal('show');
               }, 1500);
               break;
         }
      },
      error: function(data)
      {
         $("#editEmailNotificationDmModalBody").hide();
         $("#editDmEmailNotificationModalKo3").show();
         $("#editDmEmailNotificationModalKo4").show();

         $("#editDmEmailNotificationModalKo3").html("Error while trying to call database API, please try again");

         $("#editEmailNotificationDmModal div.modal-footer").hide();

         setTimeout(function()
         {
            $("#editDmEmailNotificationModalKo3").hide();
            $("#editDmEmailNotificationModalKo4").hide();
            $("#editEmailNotificationDmModalBody").show();
            $("#editEmailNotificationDmModal div.modal-footer").show();
         }, 2500);
         
         console.log("Error");
         console.log(data);
      }
   });
}


