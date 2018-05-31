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

var eventsLogList, usrRoleGen, ldapNameGen, appNameGen, clientApps = null;

function getDateForPicker(type)
{
   var now = new Date();
     
   var day = now.getDate();
   
   if(day < 9)
   {
      day = "0" + day;
   }

   var month = now.getMonth() + 1;
   
   if(type === "minus1Month")
   {
      month -= 1;
   }
   
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
   
   if(type === "plus1Year")
   {
      year += 1;
   }
   
   return day + "/" + month + "/" + year + " " + hour + ":" + min;
}

//Timeformat: 2017-08-03 15:33:30
function getDateForDatabase(type)
{
   var year, month, day, hour, min, now = new Date();
     
   year = now.getFullYear();
   month = now.getMonth() + 1;
   day = now.getDate();
   hour = now.getHours();
   min = now.getMinutes();
   
   switch(type)
   {
      case "midnightToday":
         hour = 0;
         min = 0;
         break;
      
      case "minus10Days":
         day = day - 10;
         if(day <= 0)
         {
            switch(month)
            {
               
               case 1: 
                  day = 31 + day;
                  month = 12;
                  year--;
                  break;
                  
               case 2:
                  day = 31 + day;
                  month--;
                  break;
               
               case 3:
                  if(year%4 === 0)
                  {
                     if(year%100 === 0)
                     {
                        if(year%400 === 0)
                        {
                           day = 29 + day;
                        }
                     }
                     else
                     {
                        day = 29 + day;
                     }
                  }
                  else
                  {
                     day = 28 + day;
                  }
                  month--;
                  break;
                  
               case 4:
                  day = 31 + day;
                  month--;
                  break;
               
               case 5:
                  day = 30 + day;
                  month--;
                  break;
                  
               case 6:
                  day = 31 + day;
                  month--;
                  break;
               
               case 7:
                  day = 30 + day;
                  month--;
                  break;
                  
               case 8:
                  day = 31 + day;
                  month--;
                  break;
               
               case 9:
                  day = 31 + day;
                  month--;
                  break;
                  
               case 10:
                  day = 30 + day;
                  month--;
                  break;
               
               case 11:
                  day = 31 + day;
                  month--;
                  break;
                  
               case 12:
                  day = 30 + day;
                  month--;
                  break;   
            }
         }
         break;
         
      default:
        break;
   }
   
   if(day < 9)
   {
      day = "0" + day;
   }
   
   if(month < 9)
   {
      month = "0" + month;
   }

   if(hour < 9)
   {
      hour = "0" + hour;
   }

   if(min < 9)
   {
      min = "0" + min;
   }
   
   return year + "-" + month + "-" + day + " " + hour + ":" + min;
}

function setEventsLogGlobals(usrRoleAtt, appNameAtt, ldapNameAtt)
{
   usrRoleGen = usrRoleAtt;
   appNameGen = appNameAtt;
   ldapNameGen = ldapNameAtt;
}

//Timeformat: 2017-08-03 15:33:30
function loadEventsLogTable(startDate, endDate, filterAppName, filterLdapAppName, usrRoleLocal, searchText)
{
   var tableCols = null;
   ldapNameGen = filterLdapAppName;
   var callerName = arguments.callee.caller.name;
   
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "getClientApps"
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function (data) 
      {
         //Parsing dei dati da mostrare nei menu e nella tabella
         clientApps = data.apps;
         //Creazione e popolamento tabella
         $.ajax({
            url: "restInterface.php",
            data: 
            {
               apiUsr: "alarmManager",
               apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
               operation: "getEventsLogList",
               startDate: startDate,
               endDate: endDate,
               appName: filterAppName,
               searchText: searchText
            },
            type: "POST",
            async: true,
            dataType: 'json',
            success: function (data) 
            {
               switch(data.result)
               {
                  case "queryKo":
                     $("#eventsLogTableContainer").hide();
                     $("#eventsLogMsg").html("There was an error while retrieving events from database, please try again");
                     $("#eventsLogMsg").show();
                     break;

                  case "Ok":
                     eventsLogList = data.events;
                     if(usrRoleLocal === "ToolAdmin")
                     {
                        tableCols = [
                        {
                           title: "Generator container",
                           align: "center",
                           sortable: true,
                           field: "genContainer",
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
                           title: "Generator name",
                           align: "center",
                           sortable: true,
                           field: "genName",
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
                           title: "Generator type",
                           align: "center",
                           sortable: true,
                           field: "genType",
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
                           title: "User",
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
                           title: "Event time",
                           align: "center",
                           sortable: true,
                           field: "time",
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
                           title: "Event type",
                           align: "center",
                           sortable: true,
                           field: "type",
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
                           title: "Application",
                           align: "center",
                           sortable: true,
                           field: "appName",
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
                           title: "Link",
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
                          }
                        ];
                     }
                     else
                     {
                        tableCols = [
                        {
                           title: clientApps[filterLdapAppName].containerTitleLabel,
                           align: "center",
                           sortable: true,
                           field: "genContainer",
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
                           title: clientApps[filterLdapAppName].genTitleLabel,
                           align: "center",
                           sortable: true,
                           field: "genName",
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
                           title: clientApps[filterLdapAppName].genTypeLabel,
                           align: "center",
                           sortable: true,
                           field: "genType",
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
                           title: "Event time",
                           align: "center",
                           sortable: true,
                           field: "time",
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
                           title: "Event type",
                           align: "center",
                           sortable: true,
                           field: "type",
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
                           title: "Link",
                           align: "center",
                           valign: "middle",
                           field: "url",
                           formatter: function(value, row, index)
                           {
                               //La classe delEmailBtn è di sola utilità per identificare l'elemento nel DOM, non ha istruzioni CSS
                               return '<a href="' + value + '" class="generatorLinkBtn" target="_blank"><i class="fa fa-link" style="font-size:24px;color:#07afc5"></i></a>'; 
                           }
                          }
                        ];
                     }
                     
                     if(callerName == "restoreEventsLogTable")
                     {
                        $("#filterActive").val("false");
                     }

                     $(function () {
                        $('#eventsLogTable').bootstrapTable({
                            data: data.events,
                            columns: tableCols,
                            search: false,
                            pagination: true,
                            pageSize: 17,
                            locale: 'en-US',
                            //searchAlign: 'left',
                            //uniqueId: "id",
                            onAll: function(name, args)
                            {
                              $("div.pagination-detail").remove();
                              $("div.search input").css("font-family", "Aller");
                              $("div.pagination a").css("font-family", "Aller");
                              
                              $('#filterEventsLogTableStart').data("DateTimePicker").date(startDate);
                              $('#filterEventsLogTableEnd').data("DateTimePicker").date(endDate);
                              
                              if(usrRoleLocal == "ToolAdmin")
                              {
                                 $("#eventsLogAppSelect").empty();
                                 $("#eventsLogAppSelect").append('<option value="All">All</option>');
                                 for(var ldapName in clientApps)
                                 {
                                    $("#eventsLogAppSelect").append('<option value="' + ldapName + '">' + clientApps[ldapName].name + '</option>');
                                 }
                                 $("#eventsLogAppSelect").val(filterLdapAppName);
                              }
                              
                              //Rimpiazzo del messaggio di assenza dati con uno specifico.
                              if($("#eventsLogTable tbody tr.no-records-found").length > 0)
                              {
                                 $("#eventsLogTable tbody tr.no-records-found td").html("No logged events in the system for choosen search criteria");
                              }
                              
                              //Rimpiazzo delle intestazioni colonne in base all'utente e all'applicazione selezionata
                              if(usrRoleLocal === "ToolAdmin")
                              {
                                 if(filterLdapAppName !== 'All')
                                 {
                                    $('#eventsLogTable thead tr th').eq(0).find("div.th-inner").html(clientApps[filterLdapAppName].containerTitleLabel);
                                    $('#eventsLogTable thead tr th').eq(1).find("div.th-inner").html(clientApps[filterLdapAppName].genTitleLabel);
                                    $('#eventsLogTable thead tr th').eq(2).find("div.th-inner").html(clientApps[filterLdapAppName].genTypeLabel);
                                 }
                                 else
                                 {
                                    $('#eventsLogTable thead tr th').eq(0).find("div.th-inner").html("Generator container");
                                    $('#eventsLogTable thead tr th').eq(1).find("div.th-inner").html("Generator name");
                                    $('#eventsLogTable thead tr th').eq(2).find("div.th-inner").html("Generator type");
                                 }
                              }
                              else
                              {
                                 $('#eventsLogTable thead tr th').eq(0).find("div.th-inner").html(clientApps[filterLdapAppName].containerTitleLabel);
                                 $('#eventsLogTable thead tr th').eq(1).find("div.th-inner").html(clientApps[filterLdapAppName].genTitleLabel);
                                 $('#eventsLogTable thead tr th').eq(2).find("div.th-inner").html(clientApps[filterLdapAppName].genTypeLabel);
                              }
                              
                              $('#eventsLogTable i.fa-link').off("hover");
                              $('#eventsLogTable i.fa-link').hover(function(){
                                 $(this).css("color", "#ffcc00");
                              },
                              function(){
                                 $(this).css("color", "#07afc5");
                              });
                              
                              //Nascondimento del loading ed esposizione della tabella
                              $("#mainLoading").css("opacity", 0);
                              setTimeout(function(){
                                 $("#mainLoading").hide();
                                 $("#eventsLogTable").show();
                                 setTimeout(function(){
                                    $("#eventsLogTable").css("opacity", 1);
                                 }, 100);
                              }, 100);
                            }
                        });
                     });
                     break;

                  default:
                     $("#eventsLogTableContainer").hide();
                     $("#eventsLogMsg").html("There was an error while retrieving events from database, please try again");
                     $("#eventsLogMsg").show();
                     break;
               }
            },
            error: function (data)
            {
               $("#eventsLogTableContainer").hide();
               $("#eventsLogMsg").html("There was an error while calling database API, please try again");
               $("#eventsLogMsg").show();

               console.log("Error");
               console.log(JSON.stringify(data));
            }
         });
      },
      errore: function (data)
      {
         console.log("Client applications retrieval ko:");
         console.log(JSON.stringify(data));
      }
   });
   
   return eventsLogList;
}

function filterEventsLogTable(usrRoleLocal)
{
   var startDate, endDate, filteredAppName, filteredLdapAppName = null;
   
   $("#eventsLogTable").css("opacity", 0);
   setTimeout(function(){
      $("#eventsLogTable").hide();
      $("#mainLoading").show();
      setTimeout(function(){
         $("#mainLoading").css("opacity", 1);
      }, 100);
   }, 100);
   
   startDate = $('#filterEventsLogTableStart input').val();
   endDate = $('#filterEventsLogTableEnd input').val();
   
   if(startDate === "")
   {
      startDate = "1900-01-01 00:00:00";
   }
   
   if(endDate === "")
   {
      endDate = "3000-01-01 00:00:00";
   }
   
   if(usrRoleLocal === "ToolAdmin")
   {
      filteredAppName = $("#eventsLogAppSelect option:selected").text();
      filteredLdapAppName = $("#eventsLogAppSelect").val();
   }
   else
   {
      filteredAppName = appNameGen;
      filteredLdapAppName = ldapNameGen;
   }
   
   var searchText = $("#filterEventsLogTableText").val();
   $('#eventsLogTable').bootstrapTable('destroy');
   
   loadEventsLogTable(startDate, endDate, filteredAppName, filteredLdapAppName, usrRoleLocal, searchText);
}

function restoreEventsLogTable(usrRoleLocal)
{
   $("#eventsLogTable").css("opacity", 0);
   setTimeout(function(){
      $("#eventsLogTable").hide();
      $("#mainLoading").show();
      setTimeout(function(){
         $("#mainLoading").css("opacity", 1);
      }, 100);
   }, 100);
   
   $("#filterEventsLogTableText").val("");
   
   $('#eventsLogTable').bootstrapTable('destroy');
   if(usrRoleLocal == "ToolAdmin")
   {
      loadEventsLogTable(getDateForDatabase("midnightToday"), getDateForDatabase("now"), "All", "All", usrRoleLocal, " ");
   }
   else
   {
      loadEventsLogTable(getDateForDatabase("midnightToday"), getDateForDatabase("now"), appNameGen, ldapNameGen, usrRoleLocal, " ");
   }
}