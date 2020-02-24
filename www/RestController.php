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

error_reporting(E_ERROR | E_NOTICE);

require './phpmailer/PHPMailerAutoload.php';

class RestController
{
   private $link;
   private $conf;
   
   //Constructor
   function __construct() 
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      mysqli_set_charset($this->link, 'utf8');
      mysqli_select_db($this->link, $this->conf["dbName"]);
   }
   
   //Getters
   function getConf()
   {
      return $this->conf;
   }
   
   function getLink()
   {
      return $this->link;
   }
   
   //Setters
   function setConf($conf)
   {
      $this->conf = $conf;
   }
   
   function setLink($link)
   {
      $this->link = $link;
   }
   
   //Business methods
   public static function checkLdapMembership($connection, $userDn, $tool, $baseDn) 
   {
      $result = ldap_search($connection, $baseDn, '(&(objectClass=posixGroup)(memberUid=' . $userDn . '))');
      $entries = ldap_get_entries($connection, $result);
      foreach ($entries as $key => $value) 
      {
         if(is_numeric($key)) 
         {
            if($value["cn"]["0"] == $tool) 
            {
               return true;
            }
         }
      }
      return false;
   }
   

   public static function checkLdapRole($connection, $userDn, $role, $baseDn) 
   {
      $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))');
      $entries = ldap_get_entries($connection, $result);
      foreach ($entries as $key => $value) 
      {
         if(is_numeric($key)) 
         {
            if($value["cn"]["0"] == $role) 
            {
               return true;
            }
         }
      }
      return false;
  }
   
   function verClient($usr, $pwd) 
   {
      if(($usr == $this->conf["alrUsr"]) && ($pwd == md5($this->conf["alrPwd"])))
      {
         return true;
      }
      else
      {
         return false;
      }
   }
   
   function registerEventGenerator($appName, $appUsr, $url, $generatorOriginalName, $generatorOriginalType, $containerName)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      $appName = mysqli_real_escape_string($this->link, $appName);
      $appUsr = urldecode(mysqli_real_escape_string($this->link, $appUsr));
      $url = mysqli_real_escape_string($this->link, $url);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = urldecode(mysqli_real_escape_string($this->link, $containerName));
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         //Controlliamo se è già presente
         $query0 = "SELECT count(*) AS isPresent FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '" . $appName . "' AND appUsr = '" . $appUsr . "' AND generatorOriginalName = '" . $generatorOriginalName . "' AND generatorOriginalType = '" . $generatorOriginalType . "' AND containerName = '" . $containerName . "'";
         $rs0 = mysqli_query($this->link, $query0);
         
         if($rs0)
         {
            $row = mysqli_fetch_assoc($rs0);
            $isPresent = $row['isPresent'];
            
            if($isPresent > 0)
            {
               $query1 = "UPDATE " . $this->conf["dbName"] . ".eventGenerators SET val = 1";
               $rs1 = mysqli_query($this->link, $query1);

               if($rs1)
               {
                  return "Ok";
               }
               else
               {
                  return "queryKo";
               } 
            }
            else
            {
               $query1 = "INSERT INTO " . $this->conf["dbName"] . ".eventGenerators (appName, generatorOriginalName, generatorOriginalType, appUsr, url, containerName) " .
                  "VALUES ('$appName', '$generatorOriginalName', '$generatorOriginalType', '$appUsr', '$url', '$containerName')";
               
               $rs1 = mysqli_query($this->link, $query1);

               if($rs1)
               {
                  return "Ok";
               }
               else
               {
                  return "queryKo";
               } 
            }
         }
         else
         {
            return "queryKo";
         } 
      }
   }
   
   function registerEventType($appName, $generatorOriginalName, $generatorOriginalType, $containerName, $eventType, $thrCnt)
   {
      $dbHost = $this->conf["dbHost"];
      $dbUsr = $this->conf["dbUsr"];
      $dbPwd = $this->conf["dbPwd"];
      $dbName = $this->conf["dbName"];
      
      mysqli_close($this->link);
      $this->link = mysqli_connect($dbHost, $dbUsr, $dbPwd);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      $eventType = mysqli_real_escape_string($this->link, $eventType);
      $thrCnt = mysqli_real_escape_string($this->link, $thrCnt);

      if(!$this->link)
      {
         return "dbConnKo";
      }
      else
      {
         mysqli_select_db($this->link, $dbName);
         $query = "SELECT id FROM " . $dbName . ".eventGenerators WHERE generatorOriginalName = '$generatorOriginalName' AND generatorOriginalType = '$generatorOriginalType' AND appName = '$appName' AND containerName = '$containerName'";           
         $rs = mysqli_query($this->link, $query);
         
         if($rs)
         {
            $row = mysqli_fetch_assoc($rs);
            $genId = $row['id'];
            if ($row != null) {
                $query2 = "INSERT INTO " . $dbName . ".events (genId, eventType, thrCnt, val) VALUES ('$genId', '$eventType', '$thrCnt', 1) ON DUPLICATE KEY UPDATE val = 1";
                $rs2 = mysqli_query($this->link, $query2);

                if($rs2)
                {
                   return "Ok";
                }
                else
                {
                   return "queryKo";
                } 
            }
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   
   function remoteLogin($usr, $clientApplication, $userIp)
   {
      //$key = $this->conf["encDecKey"];
      $result = [];
      
      $dbHost = $this->conf["dbHost"];
      $dbUsr = $this->conf["dbUsr"];
      $dbPwd = $this->conf["dbPwd"];
      $dbName = $this->conf["dbName"];
      mysqli_close($this->link);
      $this->link = mysqli_connect($dbHost, $dbUsr, $dbPwd);
      //$file = fopen("C:\dashboardLog.txt", "a");
      /*$file = fopen("C:\dashboardLog.txt", "a");
      
      $private = "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDfmlc2EgrdhvakQApmLCDOgP0nNERInBheMh7J/r5aU8PUAIpG
XET/8+kOGI1dSYjoux80AuHvkWp1EeHfMwC/SZ9t6rF4sYqV5Lj9t32ELbh2VNbE
/7QEVZnXRi5GdhozBZtS1gJHM2/Q+iToyh5dfTaAU8bTnLEPMNC1h3qcUQIDAQAB
AoGAcbh6UFqewgnpGKIlZ89bpAsANVckv1T8I7QT6qGvyBrABut7Z8t3oEE5r1yX
UPGcOtkoRniM1h276ex9VtoGr09sUn7duoLiEsp8aip7p7SB3X6XXWJ9K733co6C
dpXotfO0zMnv8l3O9h4pHrrBkmWDBEKbUeuE9Zz7uy6mFAECQQDygylLjzX+2rvm
FYd5ejSaLEeK17AiuT29LNPRHWLu6a0zl923299FCyHLasFgbeuLRCW0LMCs2SKE
Y+cIWMSRAkEA7AnzWjby8j8efjvUwIWh/L5YJyWlSgYKlR0zdgKxxUy9+i1MGRkn
m81NLYza4JLvb8/qjUtvw92Zcppxb7E7wQJAIuQWC+X12c30nLzaOfMIIGpgfKxd
jhFivZX2f66frkn2fmbKIorCy7c3TIH2gn4uFmJenlaV/ghbe/q3oa7L0QJAFP19
ipRAXpKGX6tqbAR2N0emBzUt0btfzYrfPKtYq7b7XfgRQFogT5aeOmLARCBM8qCG
tzHyKnTWZH6ff9M/AQJBAIToUPachXPhDyOpDBcBliRNsowZcw4Yln8CnLqgS9H5
Ya8iBJilFm2UlcXfpUOk9bhBTbgFp+Bv6BZ2Alag7pY=
-----END RSA PRIVATE KEY-----";
        
        fwrite($file, "base64 received: " . base64_decode($loginAuth) . "\n");
        //$loginAuth = preg_replace('/\s+/', '+', $loginAuth);
        //fwrite($file, "base64 received mod: " . $loginAuth . "\n");
      
        if(!$privateKey = openssl_pkey_get_private($private))
        {
            //fwrite($file, "Fail private \n");
        }
        else
        {
            //fwrite($file, "OK private \n");
        }
        
        openssl_free_key($privateKey);
      
        if(!openssl_private_decrypt(base64_decode($loginAuth), $decrypted, $privateKey))
        {
            fwrite($file, "Fail decrypt \n");
        }
        else
        {
            fwrite($file, "OK decrypt: \n");
            //fwrite($file, $decrypted);
        }
        openssl_free_key($privateKey);
        
      //Decodifica del payload da JSON a oggetto PHP
      $loginAuthObj = json_decode($decrypted, true);
      
      $clientApp = $loginAuthObj['clientApplication'];
      $username = $loginAuthObj['username'];*/
      
      //$loginAuthJsonMd5 = $loginAuth;
      //fwrite($file, "MD5 received: " . $loginAuthJsonMd5);
      
      $query = "SELECT count(*) AS quantity FROM " . $dbName . ".clientLoggedUsers WHERE userIp = '$userIp' AND usr = '$usr' AND clientApplication = '$clientApplication'";
      $rs = mysqli_query($this->link, $query);
      
      if($rs)
      {
         $row = $rs->fetch_assoc();
         
         if($row['quantity'] > 0)
         {
             $result["detail"] = "alreadyRemotelyLoggedIn";
             return $result;
         }
         else
         {
            $query2 = "INSERT INTO " . $dbName . ".clientLoggedUsers VALUES('$usr', '$clientApplication', '$userIp', (UNIX_TIMESTAMP() + 60*(SELECT sessionLifetime FROM " . $dbName . ".clientApplications WHERE MD5(ldapName) = '$clientApplication')))";
            $rs2 = mysqli_query($this->link, $query2);

            if($rs2)
            {
                $result["detail"] = "Ok";
                return $result;
            }
            else
            {
                $result["detail"] = "dbQueryKo";
                return $result;
            } 
         }
      }
      else
      {
          $result["detail"] = "dbQueryKo";
          return $result;
      }
   }
   
   function autoLoginFromRemote($usr, $showAlerts)
   {
      $dbHost = $this->conf["dbHost"];
      $dbUsr = $this->conf["dbUsr"];
      $dbPwd = $this->conf["dbPwd"];
      $dbName = $this->conf["dbName"];
      mysqli_close($this->link);
      $this->link = mysqli_connect($dbHost, $dbUsr, $dbPwd);
      
      $ldapServer = $this->conf["ldapServer"];
      $ldapPort = $this->conf["ldapPort"];
      
      $results = [];  
      $isLocal = false;
      
      //$file = fopen("C:\dashboardLog.txt", "a");
      
      //Prima verifichiamo se è un'utenza locale
      $query = "SELECT * FROM " . $dbName . ".eventGenerators WHERE MD5(CONCAT(appName, '|', generatorOriginalName, '|', containerName)) = '$showAlerts'";
      $rs = mysqli_query($this->link, $query);
      
      if($rs)
      {
          $row = $rs->fetch_assoc();
          
          $results["detail"] = "Ok";
          $results["generatorId"] = $row['id'];
          $results["username"] = $usr;
          $username = $row["appUsr"];
          $appName = $row["appName"];
          $results["usrApp"] = $row["appName"];
          
          //Repere delle applicazioni client dal DB locale
          $query2 = "SELECT * FROM " . $dbName . ".clientApplications WHERE name = '$appName'";
          $rs2 = mysqli_query($this->link, $query2);
          
          if($rs2)
          {
              $row2 = $rs2->fetch_assoc();
              $url = $row2["loginApiUrl"];
              $apiPwd = $this->conf["clientsApiPwd"];
              
              $results["usrAppLdap"] = $row2["ldapName"];
              $appLdapName = $row2["ldapName"];
              
                //Controlliamo se ha la sessione aperta e valida
                $query3 = "SELECT count(*) AS quantity FROM " . $dbName . ".clientLoggedUsers WHERE usr = MD5('$username') AND clientApplication = MD5('$appLdapName') AND ttl >= UNIX_TIMESTAMP()";
                /*$file = fopen("C:\dashboardLog.txt", "w");
                fwrite($file, "Query3: " . $query3 . "\n");
                fclose($file);*/
                $rs3 = mysqli_query($this->link, $query3);
                
                if($rs3)
                {
                    $row3 = $rs3->fetch_assoc();
                    if($row3['quantity'] > 0)
                    {
                        $data = '?apiPwd=' . $apiPwd . "&usr=" . $username . "&pwd=" . md5($this->conf["clientsApiWildcardPwd"]);
                        $url = $url.$data;

                        $options = array(
                            'http' => array(
                                'header'  => "Content-type: application/json\r\n",
                                'method'  => 'GET',
                                //'timeout' => 2
                            )
                        );

                        try
                        {
                           $context  = stream_context_create($options);
                           $callResult = @file_get_contents($url, false, $context);

                           if($callResult)
                           {
                              $callResult = json_decode($callResult);

                              if(($callResult->detail == "Ok")&&($callResult->usrRole != "Observer"))
                              {
                                 $results["usrOrigin"] = "local";
                                 $results["usrRole"] = $callResult->usrRole;
                                 $isLocal = true;
                              }
                           }
                        } 
                        catch (Exception $ex) 
                        {
                           $results["detail"] = "originalAppLoginKo";
                           return $results;
                        }

                        //Se non è locale verifichiamo se è LDAP
                        if(!$isLocal)
                        {
                           $ldapUsername = "cn=". $username . ",dc=ldap,dc=disit,dc=org"; 
                           $ds = @ldap_connect($ldapServer, $ldapPort);
                           @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                           $bind = @ldap_bind($ds);
                           if($ds && $bind)
                           {
                                if($this->checkLdapMembership($ds, $ldapUsername, $appLdapName, $baseDn))
                                {
                                   if($this->checkLdapRole($ds, $ldapUsername, "RootAdmin", $baseDn))
                                   {
                                      $results["usrOrigin"] = "ldap";
                                      $results["usrRole"] = "RootAdmin";
                                   }
                                   else if($this->checkLdapRole($ds, $ldapUsername, "ToolAdmin", $baseDn))
                                   {
                                      $results["usrOrigin"] = "ldap";
                                      $results["usrRole"] = "ToolAdmin";
                                   }
                                   else
                                   {
                                       if($this->checkLdapRole($ds, $ldapUsername, "AreaManager", $baseDn))
                                       {
                                          $results["usrOrigin"] = "ldap";
                                          $results["usrRole"] = "AreaManager";
                                       }
                                       else
                                       {
                                          if($this->checkLdapRole($ds, $ldapUsername, "Manager", $baseDn))
                                          {
                                             $results["usrOrigin"] = "ldap";
                                             $results["usrRole"] = "Manager";
                                          }
                                          else
                                          {
                                             $results["detail"] = "Unauthorized";
                                          }
                                       }
                                   }
                                }
                                else
                                {
                                    $results["detail"] = "Not member: " . $appLdapName;
                                }
                           }
                           else
                           {
                              $results["detail"] = "ldapConnKo";
                           }
                           $results["usrOrigin"] = "ldap";
                       }
                    }
                    else
                    {
                        $results["detail"] = "sessionExpired";
                    }
                }
                else 
                {
                    $results["detail"] = "queryKo";
                }
          }
          else
          {
              $results["detail"] = "queryKo";
          }
      }
      else  
      {
          $results["detail"] = "queryKo";
      }
      
      
      return $results;
   }
   
   function localLogin($usr, $pwd)
   {
      $ldapNames = [];
      $result = [];
      $baseDn = "dc=ldap,dc=disit,dc=org";
      $dbHost = $this->conf["dbHost"];
      $dbUsr = $this->conf["dbUsr"];
      $dbPwd = $this->conf["dbPwd"];
      $dbName = $this->conf["dbName"];
      mysqli_close($this->link);
      $this->link = mysqli_connect($dbHost, $dbUsr, $dbPwd);
      
      $ldapServer = $this->conf["ldapServer"];
      $ldapPort = $this->conf["ldapPort"];
      $username = mysqli_real_escape_string($this->link, $usr);
      $ldapUsername = "cn=". $usr . ",dc=ldap,dc=disit,dc=org";
      $password = mysqli_real_escape_string($this->link, $pwd);
      $ldapPassword = $pwd;
      $isLocal = false;
      
      //Repere delle applicazioni client dal DB locale
      $query = "SELECT * FROM " . $dbName . ".clientApplications";
      $rs = mysqli_query($this->link, $query);
      
      if($rs)
      {
         while($row = $rs->fetch_assoc()) 
         {
            if(($row['ldapName'] != null) && ($row['loginApiUrl'] != null))
            {
               array_push($ldapNames, [$row['ldapName'], $row['name']]);
            
               //Per prima cosa verifichiamo se è presente sugli account locali
               $url = $row["loginApiUrl"];
               $apiPwd = $this->conf["clientsApiPwd"]; 

               $data = '?apiPwd=' . $apiPwd . "&usr=" . $username . "&pwd=" . md5($password);
               $url = $url.$data;
               
               $options = array(
                   'http' => array(
                       'header'  => "Content-type: application/json\r\n",
                       'method'  => 'GET',
                       //'timeout' => 2
                   )
               );

               try
               {
                  $context  = stream_context_create($options);
                  $callResult = @file_get_contents($url, false, $context);
                  
                  //$file = fopen("C:\dashboardLog.txt", "w");
                  //fwrite($file, "Call result: " . $callResult . "\n");

                  if($callResult)
                  {
                     $callResult = json_decode($callResult);

                     if(($callResult->detail == "Ok")&&($callResult->usrRole != "Observer"))
                     {
                        $result["detail"] = "Ok";
                        $result["usrApp"] = $row["name"];
                        $result["usrAppLdap"] = $row["ldapName"];
                        $result["usrOrigin"] = "local";
                        $result["usrRole"] = $callResult->usrRole;
                        return $result;
                     }
                  }
               } 
               catch (Exception $ex) 
               {
                  //Non facciamo niente di specifico in caso di mancata risposta dell'host
               }
            }
         }
      }
      
      if(!$isLocal)
      {
         $ds = @ldap_connect($ldapServer, $ldapPort);
         @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
         $bind = @ldap_bind($ds);
         if($ds && $bind)
         {
            foreach($ldapNames as $key => $value) 
            {
               if($this->checkLdapMembership($ds, $ldapUsername, $value[0], $baseDn))
               {
                  if($this->checkLdapRole($ds, $ldapUsername, "RootAdmin", $baseDn))
                  {
                     $result["detail"] = "Ok";
                     $result["usrApp"] = $value[1];
                     $result["usrAppLdap"] = $value[0];
                     $result["usrOrigin"] = "ldap";
                     $result["usrRole"] = "RootAdmin";
                     return $result;
                  }
                  else if($this->checkLdapRole($ds, $ldapUsername, "ToolAdmin", $baseDn))
                  {
                     $result["detail"] = "Ok";
                     $result["usrApp"] = $value[1];
                     $result["usrAppLdap"] = $value[0];
                     $result["usrOrigin"] = "ldap";
                     $result["usrRole"] = "ToolAdmin";
                     return $result;
                  }
                  else
                  {
                      if($this->checkLdapRole($ds, $ldapUsername, "AreaManager", $baseDn))
                      {
                         $result["detail"] = "Ok";
                         $result["usrApp"] = $value[1];
                         $result["usrAppLdap"] = $value[0];
                         $result["usrOrigin"] = "ldap";
                         $result["usrRole"] = "AreaManager";
                         return $result;
                      }
                      else
                      {
                         if($this->checkLdapRole($ds, $ldapUsername, "Manager", $baseDn))
                         {
                            $result["detail"] = "Ok";
                            $result["usrApp"] = $value[1];
                            $result["usrAppLdap"] = $value[0];
                            $result["usrOrigin"] = "ldap";
                            $result["usrRole"] = "Manager";
                            return $result;
                         }
                         else
                         {
                            $result["detail"] = "Unauthorized";
                            return $result;
                         }
                      }
                  }
               } 
            }
         }
         else
         {
            $result["detail"] = "ldapConnKo";
            return $result;
         }
      }//Fine di if(!$isLocal)
   }
   
   function getEmailAddresses()
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         $query1 = "SELECT * FROM " . $this->conf["dbName"] . ".emailAddrBook ORDER BY emailAddrBook.adr ASC";
         $rs1 = mysqli_query($this->link, $query1);
         $addresses = [];
         
         if($rs1)
         {
            while($row = mysqli_fetch_assoc($rs1))
            {
               $address = [];
               $address['id'] = $row['id'];
               $recId = $row['id'];
               $address['adr'] = $row['adr'];
               $address['fName'] = $row['fName'];
               $address['lName'] = $row['lName'];
               $address['org'] = $row['org'];
               
               $query2 = "SELECT count(*) AS deletable FROM " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation WHERE emailNotificationRecipientsRelation.recId = $recId";
               $rs2 = mysqli_query($this->link, $query2);
               
               if($rs2)
               {
                  $row2 = mysqli_fetch_assoc($rs2);
                  if($row2['deletable'] > 0)
                  {
                     $address['deletable'] = false;
                  }
                  else
                  {
                     $address['deletable'] = true;
                  }
               }
               else
               {
                  return "queryKo";
               }
               
               array_push($addresses, $address);
            }

            return $addresses;
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function getEmailAccount($id)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $id = mysqli_real_escape_string($this->link, $id);

         $query = "SELECT * FROM " . $this->conf["dbName"] . ".emailAddrBook WHERE id = $id";
         $rs = mysqli_query($this->link, $query);
         $account = [];
         
         if($rs)
         {
            $dim = mysqli_num_rows($rs);
            if($dim > 0)
            {
               while($row = mysqli_fetch_assoc($rs))
               {
                  $account['id'] = $row['id'];
                  $account['adr'] = $row['adr'];
                  $account['fName'] = $row['fName'];
                  $account['lName'] = $row['lName'];
                  $account['org'] = $row['org'];
               }
               
               return $account;
            }
            else
            {
               return "noAccount";
            }
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function insertEmailAddress($adr, $fName, $lName, $org)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $adr = mysqli_real_escape_string($this->link, $adr);
         $fName = mysqli_real_escape_string($this->link, $fName);
         $lName = mysqli_real_escape_string($this->link, $lName);
         $org = mysqli_real_escape_string($this->link, $org);

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "INSERT INTO " . $this->conf["dbName"] . ".emailAddrBook (adr, fName, lName, org) VALUES('$adr', '$fName', '$lName', '$org')";
            $rs = mysqli_query($this->link, $query); 
            
            if($rs)
            {
               return mysqli_insert_id($this->link);
            }
            else
            {
               return "queryKo";
            }        
         }
      }
   }
   
   function editEmailAccount($id, $adr, $fName, $lName, $org)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $id = mysqli_real_escape_string($this->link, $id);
         $adr = mysqli_real_escape_string($this->link, $adr);
         $fName = mysqli_real_escape_string($this->link, $fName);
         $lName = mysqli_real_escape_string($this->link, $lName);
         $org = mysqli_real_escape_string($this->link, $org);

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "UPDATE " . $this->conf["dbName"] . ".emailAddrBook SET adr = '$adr', fName = '$fName', lName = '$lName', org = '$org' WHERE id = $id";
            $rs = mysqli_query($this->link, $query); 
            
            if($rs)
            {
               return "Ok";
            }
            else
            {
               return "queryKo";
            }        
         }
      }
   }
   
   function deleteEmailAccount($id)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $id = mysqli_real_escape_string($this->link, $id);

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "DELETE FROM " . $this->conf["dbName"] . ".emailAddrBook WHERE id = $id";
            $rs = mysqli_query($this->link, $query); 
            
            if($rs)
            {
               return "Ok";
            }
            else
            {
               return "queryKo";
            }        
         }
      }
   }
   
   function setCurrentPage($page)
   {
      $_SESSION["currentPage"] = $page;
      return "Ok";
   }
   
   function getCurrentPage()
   {
      if(isset($_SESSION["currentPage"]))
      {
         return $_SESSION["currentPage"];
      }
      else
      {
         return "login";
      }
   }
   
   function localLogout()
   {
      if(isset($_SESSION['loginType']))
      {
         unset($_SESSION['loginType']);
         unset($_SESSION['loginApp']);
         unset($_SESSION['loginAppLdap']);
         unset($_SESSION['loginUsr']);
         unset($_SESSION['usrOrigin']);
         unset($_SESSION['usrRole']);
         unset($_SESSION["currentPage"]);
         
         if(isset($_SESSION['loginType'])||isset($_SESSION['loginApp'])||isset($_SESSION['loginAppLdap'])||isset($_SESSION['loginUsr'])||isset($_SESSION['usrOrigin'])||isset($_SESSION['usrRole'])||isset($_SESSION["currentPage"]))
         {
            return false;
         }
         else
         {
            return true;
         }
      }
   }
   
   function remoteLogout($usr, $clientApplication, $userIp)
   {
      $result = [];
      
      $dbHost = $this->conf["dbHost"];
      $dbUsr = $this->conf["dbUsr"];
      $dbPwd = $this->conf["dbPwd"];
      $dbName = $this->conf["dbName"];
      mysqli_close($this->link);
      $this->link = mysqli_connect($dbHost, $dbUsr, $dbPwd);
      
      $query = "DELETE FROM " . $dbName . ".clientLoggedUsers WHERE userIp = '$userIp' AND usr = '$usr' AND clientApplication = '$clientApplication'";
      $rs = mysqli_query($this->link, $query);
      
        if($rs)
        {
            $result["detail"] = "Ok";
            return $result;
        }
        else
        {
            $result["detail"] = "dbQueryKo";
            return $result;
        } 
       
      /*if(isset($_SESSION['loginType']))
      {
         if((session_status() == PHP_SESSION_ACTIVE)&&($_SESSION['loginType'] == 'remote')&&($_SESSION['loginAppLdap'] == $app)&&($_SESSION['loginUsr'] == $appUsr))
         {
            unset($_SESSION['loginType']);
            unset($_SESSION['loginApp']);
            unset($_SESSION['loginAppLdap']);
            unset($_SESSION['loginUsr']);
            unset($_SESSION['usrOrigin']);
            unset($_SESSION['usrRole']);
            unset($_SESSION["currentPage"]);

            if(isset($_SESSION['loginType'])||isset($_SESSION['loginApp'])||isset($_SESSION['loginAppLdap'])||isset($_SESSION['loginUsr'])||isset($_SESSION['usrOrigin'])||isset($_SESSION['usrRole'])||isset($_SESSION["currentPage"]))
            {
               return false;
            }
            else
            {
               return true;
            }
         }
         else
         {
            return false;
         }
      }
      else
      {
         return false;
      }*/
   }
   
   function getEmails()
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query1 = "SELECT * FROM " . $this->conf["dbName"] . ".emailBook ORDER BY emailBook.sub ASC";
         $rs1 = mysqli_query($this->link, $query1);
         $emails = [];
         
         if($rs1)
         {
            while($row = mysqli_fetch_array($rs1))
            {
               $email = [];
               $email['id'] = $row['id'];
               $msgId = $row['id'];
               $email['sub'] = $row['sub'];
               $email['txt'] = $row['txt'];
               
               $query2 = "SELECT count(*) AS deletable FROM " . $this->conf["dbName"] . ".emailNotifications WHERE emailNotifications.msgId = $msgId";
               $rs2 = mysqli_query($this->link, $query2);
               
               if($rs2)
               {
                  $row2 = mysqli_fetch_assoc($rs2);
                  if($row2['deletable'] > 0)
                  {
                     $email['deletable'] = false;
                  }
                  else
                  {
                     $email['deletable'] = true;
                  }
               }
               else
               {
                  return "queryKo";
               }
               
               array_push($emails, $email);
            }

            return $emails;
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function deleteEmail($id)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $id = mysqli_real_escape_string($this->link, $id);

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "DELETE FROM " . $this->conf["dbName"] . ".emailBook WHERE id = $id";
            $rs = mysqli_query($this->link, $query); 
            
            if($rs)
            {
               return "Ok";
            }
            else
            {
               return "queryKo";
            }        
         }
      }
   }
   
   function insertEmail($sub, $txt)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $sub = mysqli_real_escape_string($this->link, $sub);
         $txt = mysqli_real_escape_string($this->link, $txt);

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "INSERT INTO " . $this->conf["dbName"] . ".emailBook (sub, txt) VALUES('$sub', '$txt')";
            $rs = mysqli_query($this->link, $query); 
            
            if($rs)
            {
               return mysqli_insert_id($this->link);
            }
            else
            {
               return "queryKo";
            }        
         }
      }
   }
   
   function getEmail($id)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $id = mysqli_real_escape_string($this->link, $id);
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         $query = "SELECT * FROM " . $this->conf["dbName"] . ".emailBook WHERE id = $id";
         $rs = mysqli_query($this->link, $query);
         $email = [];
         
         if($rs)
         {
            $dim = mysqli_num_rows($rs);
            if($dim > 0)
            {
               while($row = mysqli_fetch_assoc($rs))
               {
                  $email['id'] = $row['id'];
                  $email['sub'] = $row['sub'];
                  $email['txt'] = $row['txt'];
               }
               
               return $email;
            }
            else
            {
               return "noEmail";
            }
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function editEmail($id, $sub, $txt)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $id = mysqli_real_escape_string($this->link, $id);
         $sub = mysqli_real_escape_string($this->link, $sub);
         $txt = mysqli_real_escape_string($this->link, $txt);
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         $query = "UPDATE " . $this->conf["dbName"] . ".emailBook SET sub = '$sub', txt = '$txt' WHERE id = $id";
         $rs = mysqli_query($this->link, $query);
         $email = [];
         
         if($rs)
         {
            return "Ok";
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function getEventGenerators()
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         
         $response = [];
         $generators = [];
         $labels = [];
         
         $usrRole = $_SESSION['usrRole'];
         $username = $_SESSION['loginUsr'];
         
         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $usrApp = $_SESSION['loginApp'];
            
            if($usrRole == "ToolAdmin" || $usrRole == "RootAdmin")
            {
               $query = "SELECT * FROM " . $this->conf["dbName"] . ".clientApplications";
            }
            else
            {
               $query = "SELECT * FROM " . $this->conf["dbName"] . ".clientApplications WHERE clientApplications.name = '$usrApp'";
            }
            
            $rs = mysqli_query($this->link, $query);

            if($rs)
            {
               while($row = mysqli_fetch_assoc($rs))
               {
                  $labels[$row['ldapName']] = ['containerTitleLabel' => $row['containerTitleLabel'], 'genTitleLabel' => $row['genTitleLabel'], 'genTypeLabel' => $row['genTypeLabel'], 'usrLabel' => $row['usrLabel'], 'genLinkLabel' => $row['genLinkLabel']];
                  $appName = $row['name'];
                  $ldapName = $row['ldapName'];
                  $generators[$ldapName] = [];
                  
                  if($usrRole == "ToolAdmin" || $usrRole == "RootAdmin")
                  {
                     $query2 = "SELECT * FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.val = 1 ORDER BY eventGenerators.id ASC";
                  }
                  else
                  {
                     $query2 = "SELECT * FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.appUsr = '$username' AND eventGenerators.val = 1 ORDER BY eventGenerators.id ASC";
                  }
                  
                  $rs2 = mysqli_query($this->link, $query2);

                  if($rs2)
                  {
                     while($row2 = mysqli_fetch_assoc($rs2))
                     {
                        array_push($generators[$ldapName], $row2);
                     }
                  }
                  else
                  {
                     return "queryKo";
                  }
               }

               $response['labels'] = $labels;
               $response['generators'] = $generators;

               return $response;
            }
            else
            {
               return "queryKo";
            }
         }   
      }
   }
   
   function getNotifications($genId)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         
         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "SELECT notif.id AS id, notif.name AS name, notif.genId AS genId, notif.eventId AS eventId, notif.msgId AS msgId, notif.val AS val, notif.valStart AS valStart, notif.valEnd AS valEnd, msgBook.sub AS sub, msgBook.txt AS txt, " .
                     "events.eventType AS eventType, events.thrCnt AS thrCnt " .
                     "FROM " . $this->conf["dbName"] . ".emailNotifications notif " .
                     "INNER JOIN " . $this->conf["dbName"] . ".events AS events " . 
                     "ON notif.eventId = events.id " . 
                     "INNER JOIN " . $this->conf["dbName"] . ".emailBook AS msgBook " . 
                     "ON notif.msgId = msgBook.id " .
                     "WHERE notif.genId = $genId";
            
            $rs = mysqli_query($this->link, $query);
            $notifications = [];

            if($rs)
            {
               while($row = mysqli_fetch_assoc($rs))
               {
                  $notification = [];
                  $notification['id'] = $row['id'];
                  $notification['name'] = $row['name'];
                  $notification['eventType'] = $row['eventType'];
                  $notification['thrCnt'] = $row['thrCnt'];
                  $notification['mid'] = $row['msgId'];
                  $notification['sub'] = $row['sub'];
                  $notification['txt'] = $row['txt'];
                  $notification['val'] = $row['val'];
                  $notification['valStart'] = $row['valStart'];
                  $notification['valEnd'] = $row['valEnd'];

                  $subQuery = "SELECT addrBook.* FROM " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation AS relations
                              INNER JOIN " . $this->conf["dbName"] . ".emailAddrBook AS addrBook
                              ON relations.recId = addrBook.id
                              WHERE relations.notId = " . $notification['id'];

                  $subRs = mysqli_query($this->link, $subQuery);
                  if($subRs)
                  {
                     $recipients = [];
                     while($subRow = mysqli_fetch_assoc($subRs))
                     {
                        $recipient = [];
                        $recipient['id'] = $subRow['id'];
                        $recipient['adr'] = $subRow['adr'];
                        $recipient['fName'] = $subRow['fName'];
                        $recipient['lName'] = $subRow['lName'];
                        $recipient['org'] = $subRow['org'];
                        array_push($recipients, $recipient);
                     }
                  }
                  $notification['rec'] = $recipients;

                  array_push($notifications, $notification);
               }

               return $notifications;
            }
            else
            {
               return "queryKo";
            }
         }
      }
   }
   
   function getGeneratorEvents($genId)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         
         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            $query = "SELECT * FROM " . $this->conf["dbName"] . ".events WHERE events.genId = $genId AND events.val = 1";
            
            $rs = mysqli_query($this->link, $query);
            $events = [];

            if($rs)
            {
               $dim = mysqli_num_rows($rs);
               if($dim > 0)
               {
                  while($row = mysqli_fetch_assoc($rs))
                  {
                     $event = [];
                     $event['id'] = $row['id'];
                     $event['genId'] = $row['genId'];
                     $event['eventType'] = $row['eventType'];
                     $event['thrCnt'] = $row['thrCnt'];
                     $event['val'] = $row['val'];
                     
                     array_push($events, $event);
                  }

                  return $events;
               }
               else
               {
                  return "noEvents";
               }
            }
            else
            {
               return "queryKo";
            }
         }
      }
   }
   
   function delDmNotification($id)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $id = mysqli_real_escape_string($this->link, $id);
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, 'utf8');
            mysqli_select_db($this->link, $this->conf["dbName"]);
            $beginTransactionResult = mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
            
            $query = "DELETE FROM " . $this->conf["dbName"] . ".emailNotifications WHERE id = $id";
            $rs = mysqli_query($this->link, $query); 
            
            if($rs)
            {
               $query2 = "DELETE FROM " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation WHERE notId = $id";
               
               $rs2 = mysqli_query($this->link, $query2);
               if($rs2)
               {
                  $commit = mysqli_commit($this->link);
                  if($commit)
                  {
                     return "Ok";
                  }
                  else
                  {
                     $rollbackResult = mysqli_rollback($this->link);
                     return "queryKo";
                  }
               }
               else
               {
                  $rollbackResult = mysqli_rollback($this->link);
                  return "queryKo";
               }       
            }
            else
            {
               $rollbackResult = mysqli_rollback($this->link);
               return "queryKo";
            }        
         }
      }
   }
   
   function insertNotification($name, $genId, $eventId, $val, $valStart, $valEnd, $recFromBook, $recManual, $msgId, $sub, $txt)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $name = mysqli_real_escape_string($this->link, $name);
         $genId = mysqli_real_escape_string($this->link, $genId);
         $eventId = mysqli_real_escape_string($this->link, $eventId);
         $val = mysqli_real_escape_string($this->link, $val);
         $valStart = mysqli_real_escape_string($this->link, $valStart);
         $valEnd = mysqli_real_escape_string($this->link, $valEnd);
         
         if($recManual != "")
         {
            $recManual = preg_replace('/ +/', ',', $recManual);
            $recManual = preg_replace('/,+/', ',', $recManual);
            $recManual = explode(',', $recManual);
            $recipients = array_unique(array_merge($recManual, $recFromBook));
         }
         else
         {
            $recipients = $recFromBook;
            $recManual = [];
         }

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, "utf8");
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
            
            if($msgId != null)
            {
               $msgId = mysqli_real_escape_string($this->link, $msgId);
            }
            else
            {
               //Caso di messaggio inserito ex novo
               $sub = mysqli_real_escape_string($this->link, $sub);
               $txt = mysqli_real_escape_string($this->link, $txt);
               
               $query0 = "INSERT INTO " . $this->conf["dbName"] . ".emailBook (sub, txt) VALUES('$sub', '$txt')";
               $rs0 = mysqli_query($this->link, $query0); 

               if($rs0)
               {
                  $msgId = mysqli_insert_id($this->link);
               }
               else
               {
                  $queryFail = true;
                  mysqli_rollback($this->link);
                  return "queryKo";
               }
            }
            
            $query1 = "INSERT INTO " . $this->conf["dbName"] . ".emailNotifications(name, genId, eventId, msgId, val, valStart, valEnd) VALUES('$name', $genId, $eventId, $msgId, '$val', '$valStart', '$valEnd')";
            $rs1 = mysqli_query($this->link, $query1); 
            
            if($rs1)
            {
               $i = 0;
               $j = 0;
               $queryFail = false;
               $notId = mysqli_insert_id($this->link);

               while(($j < count($recManual))&&(!$queryFail))
               {
                  $adr = $recManual[$j];

                  if($adr != "")
                  {
                     $query2 = "INSERT IGNORE INTO " . $this->conf["dbName"] . ".emailAddrBook(adr) VALUES('$adr')";
                     $rs2 = mysqli_query($this->link, $query2);
                     if(!$rs2)
                     {
                        $queryFail = true;
                        mysqli_rollback($this->link);
                        return "queryKo";
                     }
                     else
                     {
                        $j++;
                     }
                  }
               }

               while(($i < count($recipients))&&(!$queryFail))
               {
                  $address = $recipients[$i];
                  $query3 = "INSERT INTO " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation(notId, recId) VALUES($notId, (SELECT emailAddrBook.id FROM " . $this->conf["dbName"] . ".emailAddrBook WHERE emailAddrBook.adr = '$address'))";
                  $rs3 = mysqli_query($this->link, $query3);

                  if($rs3)
                  {
                     $i++;
                  }
                  else
                  {
                     $queryFail = true;
                     mysqli_rollback($this->link);
                     return "queryKo";
                  }
               }
               
               if(!$queryFail)
               {
                  mysqli_commit($this->link);
                  return "Ok";
               }
            }
            else
            {
               mysqli_rollback($this->link);
               return "queryKo";
            }       
         }
      }
   }
   
   function getEmailAddressesForNotificationForm()
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         $query = "SELECT * FROM " . $this->conf["dbName"] . ".emailAddrBook";
         $rs = mysqli_query($this->link, $query);
         $addresses = [];
         
         if($rs)
         {
            $dim = mysqli_num_rows($rs);
            if($dim > 0)
            {
               while($row = mysqli_fetch_assoc($rs))
               {
                  $address = [];
                  //$address['id'] = $row['id'];
                  $address['id'] = $row['adr'];
                  $address['text'] = $row['fName'] . " " . $row['lName'] . " <" . $row['adr'] . ">";
                  $address['adr'] = $row['adr'];
                  $address['fName'] = $row['fName'];
                  $address['lName'] = $row['lName'];
                  $address['org'] = $row['org'];
                  array_push($addresses, $address);
               }
               
               return $addresses;
            }
            else
            {
               return "noAddresses";
            }
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function loadEditNotificationForm($notificationId)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         $query = "SELECT * FROM " . $this->conf["dbName"] . ".emailNotifications WHERE id = $notificationId";
         $rs = mysqli_query($this->link, $query);
         $notification = [];
         
         if($rs)
         {
            $dim = mysqli_num_rows($rs);
            if($dim > 0)
            {
               while($row = mysqli_fetch_assoc($rs))
               {
                  $notification['id'] = $row['id'];
                  $notification['name'] = $row['name'];
                  $notification['genId'] = $row['genId'];
                  $notification['eventId'] = $row['eventId'];
                  $notification['msgId'] = $row['msgId'];
                  $notification['val'] = $row['val'];
                  $notification['valStart'] = $row['valStart'];
                  $notification['valEnd'] = $row['valEnd'];
                  $notification['eventsList'] = $this->getGeneratorEvents($notification['genId']);
               }
               
               $query2 = "SELECT book.adr FROM " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation AS rels " .
                         "LEFT JOIN " . $this->conf["dbName"] . ".emailAddrBook AS book ON rels.recId = book.id " .
                         "WHERE rels.notId = $notificationId";
               
               $rs2 = mysqli_query($this->link, $query2);
               
               if($rs2)
               {
                  $rec = [];
                  while($row = mysqli_fetch_assoc($rs2))
                  {
                     array_push($rec, $row['adr']);
                  }
                  
                  $notification['rec'] = $rec;         
                  return $notification;
               }
               else
               {
                  return "queryKo";
               }
            }
            else
            {
               return "noNotification";
            }
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function editNotification($id, $name, $eventId, $val, $valStart, $valEnd, $recFromBook, $recManual, $msgId, $sub, $txt)
   {
      if(isset($_SESSION['loginUsr']))
      {
         $this->conf = parse_ini_file("./conf/conf.ini");
         $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $id = mysqli_real_escape_string($this->link, $id);
         $name = mysqli_real_escape_string($this->link, $name);
         $eventId = mysqli_real_escape_string($this->link, $eventId);
         $val = mysqli_real_escape_string($this->link, $val);
         $valStart = mysqli_real_escape_string($this->link, $valStart);
         $valEnd = mysqli_real_escape_string($this->link, $valEnd);
         
         if($recManual != "")
         {
            $recManual = preg_replace('/ +/', ',', $recManual);
            $recManual = preg_replace('/,+/', ',', $recManual);
            $recManual = explode(',', $recManual);
            $recipients = array_unique(array_merge($recManual, $recFromBook));
         }
         else
         {
            $recipients = $recFromBook;
            $recManual = [];
         }

         if(!$this->link)
         {
            return "dbConnKo";
         }
         else
         {
            mysqli_set_charset($this->link, "utf8");
            mysqli_select_db($this->link, $this->conf["dbName"]);
            
            mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
            
            if($msgId != null)
            {
               $msgId = mysqli_real_escape_string($this->link, $msgId);
            }
            else
            {
               //Caso di messaggio inserito ex novo
               $sub = mysqli_real_escape_string($this->link, $sub);
               $txt = mysqli_real_escape_string($this->link, $txt);
               
               $query0 = "INSERT INTO " . $this->conf["dbName"] . ".emailBook (sub, txt) VALUES('$sub', '$txt')";
               $rs0 = mysqli_query($this->link, $query0); 

               if($rs0)
               {
                  $msgId = mysqli_insert_id($this->link);
               }
               else
               {
                  $queryFail = true;
                  $rollbackResult = mysqli_rollback($this->link);
                  return "queryKo";
               }
            }
            
            $query1 = "UPDATE " . $this->conf["dbName"] . ".emailNotifications SET name = '$name', eventId = $eventId , msgId = $msgId, val = '$val', valStart = '$valStart', valEnd = '$valEnd' WHERE id = $id";
            $rs1 = mysqli_query($this->link, $query1); 
            
            if($rs1)
            {
               $queryFail = false;
               
               $query2 = "DELETE FROM " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation WHERE notId = $id";
               $rs2 = mysqli_query($this->link, $query2);
               
               if($rs2)
               {
                  $i = 0;
                  $j = 0;
                  
                  while(($j < count($recManual))&&(!$queryFail))
                  {
                     $adr = $recManual[$j];

                     if($adr != "")
                     {
                        $query3 = "INSERT IGNORE INTO " . $this->conf["dbName"] . ".emailAddrBook(adr) VALUES('$adr')";
                        $rs3 = mysqli_query($this->link, $query3);
                        if(!$rs3)
                        {
                           $queryFail = true;
                           mysqli_rollback($this->link);
                           return "queryKo";
                        }
                        else
                        {
                           $j++;
                        }
                     }
                  }

                  while(($i < count($recipients))&&(!$queryFail))
                  {
                     $address = $recipients[$i];
                     $query4 = "INSERT INTO " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation(notId, recId) VALUES($id, (SELECT emailAddrBook.id FROM " . $this->conf["dbName"] . ".emailAddrBook WHERE emailAddrBook.adr = '$address'))";
                     $rs4 = mysqli_query($this->link, $query4);

                     if($rs4)
                     {
                        $i++;
                     }
                     else
                     {
                        $queryFail = true;
                        mysqli_rollback($this->link);
                        return "queryKo";
                     }
                  }

                  if(!$queryFail)
                  {
                     mysqli_commit($this->link);
                     return "Ok";
                  }
               }
               else
               {
                  $queryFail = true;
                  mysqli_rollback($this->link);
                  return "queryKo";
               }
            }
            else
            {
               $rollbackResult = mysqli_rollback($this->link);
               return "queryKo";
            } 
         }
      }
   }
   
   //APPENA IMPLEMENTI LE REST ESTENDILO PER MANDARE ANCHE LE REST ASSOCIATE AD UNA REGOLA
   function notifyEvent($appName, $generatorOriginalName, $generatorOriginalType, $containerName, $eventType, $eventTime, $value, $furtherDetails)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      mysqli_set_charset($this->link, 'utf8');
      mysqli_select_db($this->link, $this->conf["dbName"]);
      
      $logFail = false; 
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      $eventType = mysqli_real_escape_string($this->link, $eventType);
      $eventTime = mysqli_real_escape_string($this->link, $eventTime);
      $value = mysqli_real_escape_string($this->link, $value);
      $furtherDetails = mysqli_real_escape_string($this->link, $furtherDetails);
      $generatorOriginalName = html_entity_decode($generatorOriginalName, ENT_HTML5);

      if(!$this->link)
      {
         return "dbConnKo";
      }
      else
      {
         mysqli_set_charset($this->link, "utf8");
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query = "SELECT gen.appName as appName, gen.id as generatorId, gen.generatorOriginalName as generatorOriginalName, gen.generatorOriginalType as generatorOriginalType, " . 
                  "gen.appUsr as appUsr, gen.url as url, evt.id as eventId, evt.eventType as eventType " .
                  "FROM " . $this->conf["dbName"] . ".events AS evt " . 
                  "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS gen " .
                  "ON evt.genId = gen.id " .
                  "WHERE evt.eventType = '$eventType' AND gen.appName = '$appName' AND gen.generatorOriginalName = '$generatorOriginalName' AND gen.generatorOriginalType = '$generatorOriginalType' AND gen.containerName = '$containerName' AND gen.val = 1";

         $rs = mysqli_query($this->link, $query); 

         if($rs)
         {
            while($row = mysqli_fetch_assoc($rs))
            {
               $generatorId = $row['generatorId'];
               $eventTypeId = $row['eventId'];
               $appUsr = $row['appUsr'];
               $url = $row['url'];
               
               //Per ora non gestiamo il valore ritornato dalla chiamata di questo metodo
               $logResult = $this->logEvent($eventTime, $eventTypeId, $value, $generatorId);
               
               if($logResult != "Ok")
               {
                   $logFail = true;
               }
            }
            
            if($logFail)
            {
                return "logKo";
            }
            else
            {
                $result = [];
                $result["result"] = "logOk";
                $result["appName"] = $appName;
                $result["generatorId"] = $generatorId;
                $result["generatorOriginalName"] = $generatorOriginalName;
                $result["generatorOriginalType"] = $generatorOriginalType;
                $result["containerName"] = $containerName;
                $result["appUsr"] = $appUsr;
                $result["url"] = $url;
                $result["eventTypeId"] = $eventTypeId;
                $result["eventType"] = $eventType;
                $result["eventTime"] = $eventTime;
                $result["value"] = $value;
                return $result;
            }
         }
         else
         {
            return "queryKo";
         }
      }   
   }
   
   function logEvent($eventTime, $eventTypeId, $value, $generatorId)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);

      if(!$this->link)
      {
         return "dbConnKo";
      }
      else
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query = "INSERT INTO " . $this->conf["dbName"] . ".eventsLog (time, eventTypeId, value, genId) VALUES ('$eventTime', '$eventTypeId', '$value', '$generatorId')";
         $rs = mysqli_query($this->link, $query); 

         if($rs)
         {
            return "Ok";
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function sendEmails($appName, $generatorId, $generatorName, $generatorType, $containerTitle, $appUsr, $url, $eventTypeId, $eventType, $eventTime, $value, $furtherDetails)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      mysqli_set_charset($this->link, 'utf8');
      mysqli_select_db($this->link, $this->conf["dbName"]);
      
      $results = [];

      if(!$this->link)
      {
         return "dbConnKo";
      }
      else
      {
         mysqli_set_charset($this->link, "utf8");
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query0 = "SELECT * FROM " . $this->conf["dbName"] . ".clientApplications WHERE clientApplications.name = '$appName'";
         $rs0 = mysqli_query($this->link, $query0);
         
         if($rs0)
         {
            $row0 = mysqli_fetch_assoc($rs0);
            $generatorContainerLbl = $row0['containerTitleLabel'];
            $generatorNameLbl = $row0['genTitleLabel'];
            $generatorTypeLbl = $row0['genTypeLabel'];
         }
         else
         {
            $generatorContainerLbl = "Event generator container (e.g. dashboard, page...)";
            $generatorNameLbl = "Event generator name (e.g. widget, metric name)";
            $generatorTypeLbl = "Event generator type (e.g. metric type)";
         }
         
         $query1 = "SELECT notif.id as notId, notif.name as notName, notif.genId as genId, notif.msgId as msgId, notif.val as val, notif.valStart as valStart, notif.valEnd as valEnd " .
                   "FROM " . $this->conf["dbName"] . ".emailNotifications AS notif " .
                   "WHERE notif.genId = $generatorId AND notif.eventId = $eventTypeId";
         $rs1 = mysqli_query($this->link, $query1);

        if($rs1)
        {
           if(mysqli_num_rows($rs1) > 0)
           {
              while($row1 = mysqli_fetch_assoc($rs1))
              {
                 $notId = $row1['notId'];
                 $msgId = $row1['msgId'];
                 $val = $row1['val'];
                 $valStartString = $row1['valStart'];
                 $valEndString = $row1['valEnd'];

                 $query2 = "SELECT book.adr AS adr FROM " . $this->conf["dbName"] . ".emailNotificationRecipientsRelation rel " .
                           "INNER JOIN " . $this->conf["dbName"] . ".emailAddrBook book " .
                           "ON rel.recId = book.id " .
                           "WHERE rel.notId = $notId";
                 
                 $rs2 = mysqli_query($this->link, $query2);
                 if($rs2)
                 {
                    if(mysqli_num_rows($rs2) > 0)
                    {
                       $recList = [];
                       while($row2 = mysqli_fetch_assoc($rs2))
                       {
                          array_push($recList, $row2['adr']);
                       }

                       //Le email vengono mandate solo per notifiche in corso di validità
                       $timezoneSetting = date_default_timezone_set("Europe/Rome");
                       $valStart = new DateTime($valStartString);
                       $valEnd = new DateTime($valEndString);
                       $now = new DateTime();

                       if(($val == 1)&&($now >= $valStart)&&($now <= $valEnd))
                       {
                            $query3 = "SELECT * FROM " . $this->conf["dbName"] . ".emailBook WHERE id = $msgId";
                            $rs3 = mysqli_query($this->link, $query3);
                            if($rs3)
                            {
                               $row3 = mysqli_fetch_assoc($rs3);
                               
                               if($row3["sub"] == "[[Auto]]")
                               {
                                  $sub = "[" . $appName . "][" . $containerTitle . "]["  . $generatorName . "][" . $generatorType . "][" . $eventType . "]";
                               }
                               else
                               {
                                  $sub = $row3["sub"];
                               }
                               
                               $customTxt = $row3["txt"];

                               $eventType = str_replace("<", "&lt;", $eventType);
                               $eventType = str_replace(">", "&gt;", $eventType);

                               $mailer = new PHPMailer;
                               $mailer->isSMTP(); 
                               $mailer->Host = $this->conf["smtpHost"];

                               if($this->conf["smtpAuth"] == "true")
                               {
                                  $mailer->SMTPAuth = true;
                                  $mailer->SMTPSecure = $this->conf["smtpSecure"];
                                  $mailer->Port = $this->conf["smtpPort"]; 
                                  $mailer->Username = $this->conf["smtpUser"];
                                  $mailer->Password = $this->conf["smtpPassword"];
                               } else {
                                  $mailer->SMTPAuth = false;                        
                               }
                               $mailer->From = $this->conf["emailFromAddress"];
                               $mailer->FromName = $this->conf["emailFromName"];
                               $mailer->isHTML(true);
                               $mailer->Subject = $sub;

                               if($value !== 'x')
                               {
                                  $eventDetails = "<ul>" .
                                                "<li>" .
                                                   "Event time: <b>" . $eventTime . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "Application: <b>" . $appName . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   $generatorContainerLbl . " (with link): <a href='" . $url . "' target='blank'>" . $containerTitle . "</a>" .
                                                "</li>" .
                                                "<li>" .
                                                   $generatorNameLbl . ": <b>" . $generatorName . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   $generatorTypeLbl . ": <b>" . $generatorType . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "Event type: <b>" . $eventType . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "Registered value: <b>" . $value . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "User: <b>" . $appUsr . "</b>" .
                                                "</li>";
                               }
                               else
                               {
                                  $eventDetails = "<ul>" .
                                                "<li>" .
                                                   "Event time: <b>" . $eventTime . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "Application: <b>" . $appName . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   $generatorContainerLbl . " (with link): <a href='" . $url . "' target='blank'>" . $containerTitle . "</a>" .
                                                "</li>" .
                                                "<li>" .
                                                   $generatorNameLbl . ": <b>" . $generatorName . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   $generatorTypeLbl . ": <b>" . $generatorType . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "Event type: <b>" . $eventType . "</b>" .
                                                "</li>" .
                                                "<li>" .
                                                   "User: <b>" . $appUsr . "</b>" .
                                                "</li>";
                               }
                               
                               if($furtherDetails != "x")
                               {
                                  $eventDetails = $eventDetails . "<li>Further details: <b>" . nl2br($furtherDetails) . "</b></li></ul>";
                               }
                               else
                               {
                                  $eventDetails = $eventDetails . "</ul>";
                               }

                               if(strpos($customTxt, "[[EventDetails]]") != false)
                               {
                                  $fullTxt = str_replace("[[EventDetails]]", $eventDetails, $customTxt);
                               }
                               else
                               {
                                  $fullTxt = $customTxt . "<br><b>Event details:</b><br>" . $eventDetails;
                               }
                               
                               $mailer->Body = $fullTxt;
                               
                               for($i = 0; $i < count($recList); $i++)
                               {
                                  $mailer->addAddress($recList[$i]);
                               }
                               
                               if(!$mailer->send()) 
                               { 
                                  $results[$notId] = "Ko";
                                  error_log("sendEmails: FAILED SEND EMAIL $appName  $generatorName");
                               } 
                               else 
                               {
                                  $results[$notId] = "Ok";
                                  error_log("sendEmails: email sent $appName  $generatorName");
                               }
                            }
                            else
                            {
                               $results[$notId] = "QueryKo";
                               error_log("sendEmails: failed query rs3 $query3");
                            }
                       }
                       else
                       {
                          $results[$notId] = "NotificationNotActive";
                          error_log("sendEmails: NOTIFICATION NOT ACTIVE $appName  $generatorName");

                       }
                    }
                    else
                    {
                       $results[$notId] = "noRecipientsForThisNotification";
                       error_log("sendEmails: NO RECIPIENTS $query2");
                    }
                 }
                 else
                 {
                    $results[$notId] = "queryKo";
                    error_log("sendEmails: failed query rs2 $query2");
                 }
              }
              
              return $results;
           }
           else
           {
              error_log("sendEmails: NO NOTIFICATIONS FOR EVENT $query1");
              return "noNotificationsForThisEvent";
           }
        }
        else
        {
           error_log("sendEmails: failed query rs1 $query1");
           return "queryKo";
        }
      }
   }
   
   function getEventsLogList($startDate, $endDate, $appName, $searchText)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      mysqli_set_charset($this->link, 'utf8');
      mysqli_select_db($this->link, $this->conf["dbName"]);

      if(!$this->link)
      {
         return "dbConnKo";
      }
      else
      {
         mysqli_set_charset($this->link, "utf8");
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $username = $_SESSION['loginUsr'];
         
         if($_SESSION['usrRole'] == "ToolAdmin" || $_SESSION['usrRole'] == "RootAdmin")
         {
            if($appName != "All")
            {
               $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                        "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                        "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                        "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                        "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                        "ON events.eventTypeId = evtTypes.id " .
                        "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                        "ON evtTypes.genId = generators.id " .
                        "WHERE STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') >= STR_TO_DATE('$startDate','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') <= STR_TO_DATE('$endDate','%Y-%m-%d %H:%i:%s') " .
                        "AND generators.appName = '$appName' ";
               
                        if(strlen(trim($searchText)) == 0)
                        {
                           $query = $query . "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
                        }
                        else
                        {
                           $query = $query . 
                                    "AND ((generators.containerName LIKE '%$searchText%')||(generators.generatorOriginalName LIKE '%$searchText%')||(generators.generatorOriginalType LIKE '%$searchText%')||(generators.appUsr LIKE '%$searchText%')||(evtTypes.eventType LIKE '%$searchText%')) " .
                                    "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
                        }
            }
            else
            {
               $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                        "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                        "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                        "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                        "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                        "ON events.eventTypeId = evtTypes.id " .
                        "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                        "ON evtTypes.genId = generators.id " .
                        "WHERE STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') >= STR_TO_DATE('$startDate','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') <= STR_TO_DATE('$endDate','%Y-%m-%d %H:%i:%s') ";
                        if(strlen(trim($searchText)) == 0)
                        {
                           $query = $query . "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
                        }
                        else
                        {
                           $query = $query . 
                                    "AND ((generators.containerName LIKE '%$searchText%')||(generators.generatorOriginalName LIKE '%$searchText%')||(generators.generatorOriginalType LIKE '%$searchText%')||(generators.appUsr LIKE '%$searchText%')||(evtTypes.eventType LIKE '%$searchText%')) " .
                                    "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
                        }
            }
         }
         else
         {
            $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                     "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                     "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                     "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                     "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                     "ON events.eventTypeId = evtTypes.id " .
                     "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                     "ON evtTypes.genId = generators.id " .
                     "WHERE generators.appName = '$appName' AND generators.appUsr = '$username' " .
                     "AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') >= STR_TO_DATE('$startDate','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') <= STR_TO_DATE('$endDate','%Y-%m-%d %H:%i:%s') ";
                     if(strlen(trim($searchText)) == 0)
                     {
                        $query = $query . "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
                     }
                     else
                     {
                        $query = $query . 
                                 "AND ((generators.containerName LIKE '%$searchText%')||(generators.generatorOriginalName LIKE '%$searchText%')||(generators.generatorOriginalType LIKE '%$searchText%')||(evtTypes.eventType LIKE '%$searchText%')) " .
                                 "ORDER BY STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') DESC";
                     }
         }
         
         $rs = mysqli_query($this->link, $query); 

         if($rs)
         {
            $events = [];
            while($row = mysqli_fetch_assoc($rs))
            {
               $event = [];
               $event['id'] = $row['id'];
               $event['time'] = $row['eventTime'];
               $event['type'] = str_replace("<", "&lt;", $row['eventType']);
               $event['type'] = str_replace(">", "&gt;", $event['type']);
               $event['type'] = str_replace("=", "&equals;", $event['type']);
               $event['appName'] = $row['appName'];
               $event['appUsr'] = $row['appUsr'];
               $event['genName'] = $row['genName'];
               $event['genType'] = $row['genType'];
               $event['genContainer'] = $row['genContainer'];
               $event['url'] = $row['url'];

               array_push($events, $event);
            }

            return $events;
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function getClientApps()
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      mysqli_set_charset($this->link, 'utf8');
      mysqli_select_db($this->link, $this->conf["dbName"]);

      if(!$this->link)
      {
         return "dbConnKo";
      }
      else
      {
         mysqli_set_charset($this->link, "utf8");
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query = "SELECT * FROM " . $this->conf["dbName"] . ".clientApplications"; 
         $rs = mysqli_query($this->link, $query); 
         
         if($rs)
         {
            $apps = [];
            while($row = mysqli_fetch_assoc($rs))
            {
               $app = [];
               $app["name"] = $row["name"];
               $app["ldapName"] = $row["ldapName"];
               $app["containerTitleLabel"] = $row["containerTitleLabel"];
               $app["genTitleLabel"] = $row["genTitleLabel"];
               $app["genTypeLabel"] = $row["genTypeLabel"];
               $app["genLinkLabel"] = $row["genLinkLabel"];
               
               $apps[$app["ldapName"]] = $app;
            }
            
            return $apps;
         }
         else
         {
            return "queryKo";
         }
      }  
   }
   
   function setGeneratorValidity($appName, $generatorOriginalName, $generatorNewName, $generatorOriginalType, $containerName, $validity, $containerUrl, $appUsr/*, $setEventsValidityTrue*/)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorNewName = mysqli_real_escape_string($this->link, $generatorNewName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      $validity = mysqli_real_escape_string($this->link, $validity);
      //$setEventsValidityTrue = mysqli_real_escape_string($this->link, $setEventsValidityTrue);
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         //mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
         
         $query1 = "UPDATE " . $this->conf["dbName"] . ".eventGenerators " . 
                   "SET eventGenerators.val = $validity, eventGenerators.generatorOriginalName = '" . $generatorNewName . "' " .
                   "WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName'";
         
         $rs1 = mysqli_query($this->link, $query1);
         
         if($rs1)
         {
            while($row != mysqli_fetch_assoc($rs1))
            {
                return "Ok";
            }
            
            // REGISTRA NEW EVENTGENERATOR AUTOMATIC REPAIR GP
        //    RestController::repairEventGenerator($appName, $appUsr, $containerUrl, $generatorNewName, $generatorOriginalType, $containerName, $generatorNewName, $validity); // CTR QUESTA ISTRUZIONE SE REGISTRA OK NEW egentGenerator
         //   $this->conf = parse_ini_file("./conf/conf.ini");

        /*    $query2 = "UPDATE " . $this->conf["dbName"] . ".eventGenerators " . 
                   "SET eventGenerators.val = $validity, eventGenerators.generatorOriginalName = '" . $generatorNewName . "' " .
                   "WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName'";
         
            $rs2 = mysqli_query($this->link, $query2);
         
            if($rs2) {
                return "Ok";
            }   */
            /*$query2 = "UPDATE " . $this->conf["dbName"] . ".emailNotifications " .
                      "SET emailNotifications.val = $validity " .
                      "WHERE emailNotifications.genId = (SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorNewName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";
               
            $rs2 = mysqli_query($this->link, $query2);

            if($rs2)
            {
               $query3 = "UPDATE " . $this->conf["dbName"] . ".events " .
                         "SET events.val = $validity " .
                         "WHERE events.genId = (SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorNewName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";

               $rs3 = mysqli_query($this->link, $query3);

               if($rs3)
               {
                  mysqli_commit($this->link);
                  return "Ok";
               }
               else
               {
                  mysqli_rollback($this->link);
                  return "queryKo";
               }  
            }
            else
            {
               mysqli_rollback($this->link);
               return "queryKo";
            } */
         }
         else
         {
            //mysqli_rollback($this->link);
            return "queryKo";
         }  
      }
   }
   
    function repairEventGenerator($appName, $appUsr, $url, $generatorOriginalName, $generatorOriginalType, $containerName, $generatorNewName, $validity) {
        $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
        $appName = mysqli_real_escape_string($this->link, $appName);
        $appUsr = mysqli_real_escape_string($this->link, $appUsr);
     //   $url = mysqli_real_escape_string($containerName);
        $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
        $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
        $containerName = mysqli_real_escape_string($this->link, $containerName);

        if(!$this->link)
        {
           return "dbConnectionKo";
        }
        else 
        {
           mysqli_set_charset($this->link, 'utf8');
           mysqli_select_db($this->link, $this->conf["dbName"]);

           //Controlliamo se è già presente
           $query0 = "SELECT count(*) AS isPresent FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '" . $appName . "' AND appUsr = '" . $appUsr . "' AND generatorOriginalName = '" . $generatorOriginalName . "' AND generatorOriginalType = '" . $generatorOriginalType . "' AND containerName = '" . $containerName . "'";
           $rs0 = mysqli_query($this->link, $query0);

           if($rs0)
           {
              $row = mysqli_fetch_assoc($rs0);
              $isPresent = $row['isPresent'];

              if($isPresent > 0)
              {
                 $query1 = "UPDATE " . $this->conf["dbName"] . ".eventGenerators SET val = 1";
                 $rs1 = mysqli_query($this->link, $query1);

                 if($rs1)
                 {
                    return "Ok";
                 }
                 else
                 {
                    return "queryKo";
                 } 
              }
              else
              {
                $query1 = "INSERT INTO " . $this->conf["dbName"] . ".eventGenerators (appName, generatorOriginalName, generatorOriginalType, appUsr, url, containerName) " .
                   "VALUES ('$appName', '$generatorOriginalName', '$generatorOriginalType', '$appUsr', '$url', '$containerName')";

                $rs1 = mysqli_query($this->link, $query1);

                if($rs1)
                {
                //   return "Ok";
                }
                else
                {
                //   return "queryKo";
                } 
              }
               $query2 = "UPDATE " . $this->conf["dbName"] . ".eventGenerators " . 
                  "SET eventGenerators.val = $validity, eventGenerators.generatorOriginalName = '" . $generatorNewName . "' " .
                  "WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName'";

               $rs2 = mysqli_query($this->link, $query2);

               if($rs2) {
                   return "Ok";
               } 
           }
           else
           {
              return "queryKo";
           } 
        }
   }
   
   function setEventValidity($appName, $generatorOriginalName, $generatorOriginalType, $containerName, $eventType, $validity)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      $eventType = mysqli_real_escape_string($this->link, $eventType);
      $validity = mysqli_real_escape_string($this->link, $validity);
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         $query = "UPDATE " . $this->conf["dbName"] . ".events, " . $this->conf["dbName"] . ".emailNotifications " .
                  "SET events.val = $validity, " .
                  "emailNotifications.val = $validity " .
                  "WHERE events.eventType = '$eventType' " .
                  "AND events.genId = (SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName') " .
                  "AND events.id = emailNotifications.eventId";
         
         $rs = mysqli_query($this->link, $query);
         
         if($rs)
         {
            return "Ok";
         }
         else
         {
            return "queryKo";
         }   
      }
   }
   
   function updateEventType($appName, $generatorOriginalName, $generatorOriginalType, $containerName, $oldEventType, $newEventType)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $dbName = $this->conf["dbName"];
      mysqli_close($this->link);
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      $oldEventType = mysqli_real_escape_string($this->link, $oldEventType);
      $newEventType = mysqli_real_escape_string($this->link, $newEventType);
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);

         //1)Aggiungiamo nuovo tipo di evento
         $query1 = "SELECT id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE generatorOriginalName = '$generatorOriginalName' AND generatorOriginalType = '$generatorOriginalType' AND appName = '$appName' AND containerName = '$containerName'";           
         $rs1 = mysqli_query($this->link, $query1);

         if($rs1)
         {
            $row1 = mysqli_fetch_assoc($rs1);
            $genId = $row1['id'];

            $query2 = "SELECT id FROM " . $this->conf["dbName"] . ".events WHERE events.genId = $genId AND events.eventType = '$oldEventType'";
            $rs2 = mysqli_query($this->link, $query2);
            
            if($rs2)
            {
               $row2 = mysqli_fetch_assoc($rs2);
               $oldEventId = $row2['id'];
               
               $query3 = "INSERT INTO " . $this->conf["dbName"] . ".events (genId, eventType, thrCnt, val) VALUES ('$genId', '$newEventType', 1, 1) ON DUPLICATE KEY UPDATE val = 1";
               $rs3 = mysqli_query($this->link, $query3);

               if($rs3)
               {
                  $newEventId = mysqli_insert_id($this->link);
                  
                  //2) Aggiorniamo il tipo di evento a tutte le notifiche che erano relative al vecchio evento su questo generatore
                  $query4 = "UPDATE " . $this->conf["dbName"] . ".emailNotifications SET emailNotifications.eventId = $newEventId WHERE emailNotifications.genId = $genId AND emailNotifications.eventId = $oldEventId";
                  $rs4 = mysqli_query($this->link, $query4);
                  
                  if($rs4)
                  {
                     //3) Settiamo a zero la validità del vecchio tipo di evento
                     $query5 = "UPDATE " . $this->conf["dbName"] . ".events SET events.val = 0 WHERE events.id = $oldEventId";
                     $rs5 = mysqli_query($this->link, $query5);
                     
                     if($rs5)
                     {
                        return "Ok";
                     }
                     else
                     {
                        return "queryKo";
                     }
                  }
                  else
                  {
                     return "queryKo";
                  }
               }
               else
               {
                  return "queryKo";
               }
            }
            else
            {
               return "queryKo";
            }
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   function deleteEventType($appName, $generatorOriginalName, $generatorOriginalType, $containerName, $eventType)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      mysqli_close($this->link);
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      $eventType = mysqli_real_escape_string($this->link, $eventType);
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         //0) Reperiamo l'ID del tipo di evento da "cancellare"
         $query0 = "SELECT id FROM " . $this->conf["dbName"] . ".events " .
                   "WHERE events.eventType = '$eventType' " .
                   "AND events.genId = (SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";
         
         $rs0 = mysqli_query($this->link, $query0);
         
         if($rs0)
         {
            $row0 = mysqli_fetch_assoc($rs0);
            $eventId = $row0['id'];
            
            $beginTransactionResult = mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
         
            //1) Si setta a zero la validità del tipo di evento da "cancellare"
            $query1 = "UPDATE " . $this->conf["dbName"] . ".events " .
                      "SET events.val = 0 " .
                      "WHERE events.id = $eventId";

            $rs1 = mysqli_query($this->link, $query1);

            if($rs1)
            {
               //2) Cancelliamo le notifiche relative a questo tipo di evento: le relazioni coi destinatari vengono cancellate automaticamente grazie ad un vincolo di chiave esterna su DB
               $query2 = "DELETE FROM " . $this->conf["dbName"] . ".emailNotifications WHERE emailNotifications.eventId = $eventId";
               $rs2 = mysqli_query($this->link, $query2);

               if($rs2)
               {
                  $commit = mysqli_commit($this->link);
                  return "Ok";
               }
               else
               {
                  $rollbackResult = mysqli_rollback($this->link);
                  return "queryKo";
               }
            }
            else
            {
               $rollbackResult = mysqli_rollback($this->link);
               return "queryKo";
            }   
         }
         else
         {
            return "queryKo";
         }
      }
   }
   
   //Invalida tutti i tipi di evento di un generatore e tutte le notifiche associate a ciascun tipo di evento invalidato SENZA CANCELLARE NIENTE FISICAMENTE
   function disableAllGeneratorEventTypes($appName, $generatorOriginalName, $generatorOriginalType, $containerName)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      mysqli_close($this->link);
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
         
         //1) Disabilitiamo le notifiche relative a questi tipi di evento: le relazioni coi destinatari vengono cancellate automaticamente grazie ad un vincolo di chiave esterna su DB
         $query1 = "UPDATE " . $this->conf["dbName"] . ".emailNotifications " .
                   "SET emailNotifications.val = 0 " .
                   "WHERE emailNotifications.genId IN(SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";
         $rs1 = mysqli_query($this->link, $query1);

         if($rs1)
         {
            //2) Si setta a zero la validità dei tipi di evento del generatore in esame
            $query2 = "UPDATE " . $this->conf["dbName"] . ".events " .
                      "SET events.val = 0 " .
                      "WHERE events.genId IN(SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";          
            $rs2 = mysqli_query($this->link, $query2);
            
            if($rs2)
            {
               mysqli_commit($this->link);
               return "Ok";
            }
            else
            {
               mysqli_rollback($this->link);
               return "queryKo";
            }
         }
         else
         {
            mysqli_rollback($this->link);
            return "queryKo";
         }
      }
   }
   
   //Invalida tutti i tipi di evento di un generatore e CANCELLA FISICAMENTE tutte le notifiche associate a ciascun tipo di evento invalidato
   function deleteAllGeneratorEventTypes($appName, $generatorOriginalName, $generatorOriginalType, $containerName)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      mysqli_close($this->link);
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = mysqli_real_escape_string($this->link, $containerName);
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         mysqli_begin_transaction($this->link, MYSQLI_TRANS_START_READ_WRITE);
         
         //1) Cancelliamo le notifiche relative a questi tipi di evento: le relazioni coi destinatari vengono cancellate automaticamente grazie ad un vincolo di chiave esterna su DB
         $query1 = "DELETE FROM " . $this->conf["dbName"] . ".emailNotifications WHERE emailNotifications.genId IN(SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";
         $rs1 = mysqli_query($this->link, $query1);

         if($rs1)
         {
            //2) Si setta a zero la validità dei tipi di evento del generatore in esame
            $query2 = "UPDATE " . $this->conf["dbName"] . ".events " .
                      "SET events.val = 0 " .
                      "WHERE events.genId IN(SELECT eventGenerators.id FROM " . $this->conf["dbName"] . ".eventGenerators WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName')";          
            $rs2 = mysqli_query($this->link, $query2);
            
            if($rs2)
            {
               mysqli_commit($this->link);
               return "Ok";
            }
            else
            {
               mysqli_rollback($this->link);
               return "queryKo";
            }
         }
         else
         {
            mysqli_rollback($this->link);
            return "queryKo";
         }
      }
   }
   
   //Chiamata quando un generatore viene cancellato dall'applicazione client: qui viene settata a 0 la validità del generatore e dei suoi eventi, mentre le notifiche relative vengono cancellate fisicamente
   function deleteGenerator($appName, $generatorOriginalName, $generatorOriginalType, $containerName)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      
      $appName = mysqli_real_escape_string($this->link, $appName);
      $generatorOriginalName = mysqli_real_escape_string($this->link, $generatorOriginalName);
      $generatorOriginalType = mysqli_real_escape_string($this->link, $generatorOriginalType);
      $containerName = urldecode(mysqli_real_escape_string($this->link, $containerName));
      
      if(!$this->link)
      {
         return "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         //1) Settiamo a zero la validità del generatore
         $query1 = "UPDATE " . $this->conf["dbName"] . ".eventGenerators " . 
                   "SET eventGenerators.val = 0 " .
                   "WHERE eventGenerators.appName = '$appName' AND eventGenerators.generatorOriginalName = '$generatorOriginalName' AND eventGenerators.generatorOriginalType = '$generatorOriginalType' AND eventGenerators.containerName = '$containerName'";
         
         $rs1 = mysqli_query($this->link, $query1);
         
         if($rs1)
         {
            //2) Settiamo a zero la validità degli eventi di questo generatore, cancelliamo fisicamente le relative notifiche
            $result = $this->deleteAllGeneratorEventTypes($appName, $generatorOriginalName, $generatorOriginalType, $containerName);
            return $result;
         }
         else
         {
            mysqli_rollback($this->link);
            return "queryKo";
         }
         
      }
   }
   
   function getNewEvents($clientLastEventId, $clientLastEventTime)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      $response = [];
      $clientLastEventId = mysqli_real_escape_string($this->link, $clientLastEventId);
      $clientLastEventTime = mysqli_real_escape_string($this->link, $clientLastEventTime);
      
      if(!$this->link)
      {
         $response['result'] = "dbConnectionKo";
      }
      else 
      {
         mysqli_set_charset($this->link, 'utf8');
         mysqli_select_db($this->link, $this->conf["dbName"]);
         
         if(($clientLastEventId == null) && ($clientLastEventTime == null))
         {
            if(isset($_SESSION['usrRole']))
            {
               $usrRole = $_SESSION['usrRole'];
               $username = $_SESSION['loginUsr'];
               
               if($usrRole == "ToolAdmin" || $usrRole == "RootAdmin")
               {
                  $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                           "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                           "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                           "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                           "ON events.eventTypeId = evtTypes.id " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                           "ON evtTypes.genId = generators.id " .
                           "ORDER BY eventTime DESC LIMIT 1";
               }
               else
               {
                  $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                           "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                           "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                           "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                           "ON events.eventTypeId = evtTypes.id " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                           "ON evtTypes.genId = generators.id " .
                           "WHERE generators.appUsr = '$username' " .
                           "ORDER BY eventTime DESC LIMIT 1";
               }
               
               $rs = mysqli_query($this->link, $query);

               if($rs)
               {
                  if(mysqli_num_rows($rs) > 0)
                  {
                     $row = mysqli_fetch_assoc($rs);
                     $row['time'] = $row['eventTime'];
                     $row['type'] = $row['eventType'];
                     $response['result'] = "Ok";
                     $response['detail'] = "firstCall";
                     $response['loggedIn'] = "yes";
                     $response['resultNumber'] = mysqli_num_rows($rs);
                     $response['updatedLastEvent'] = $row;
                  }
                  else
                  {
                     $response['result'] = "Ok";
                     $response['detail'] = "firstCall";
                     $response['loggedIn'] = "yes";
                     $response['resultNumber'] = mysqli_num_rows($rs);
                  }
               }
               else
               {
                  $response['result'] = "queryKo";
                  $response['loggedIn'] = "yes";
               }
            }
            else
            {
               $response['result'] = "Ok";
               $response['detail'] = "firstCall";
               $response['loggedIn'] = "no";
            }
         }
         else
         {
            if(isset($_SESSION['usrRole']))
            {
               $usrRole = $_SESSION['usrRole'];
               $username = $_SESSION['loginUsr'];
               
               if($usrRole == "ToolAdmin" || $usrRole == "RootAdmin")
               {
                  $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                           "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                           "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                           "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                           "ON events.eventTypeId = evtTypes.id " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                           "ON evtTypes.genId = generators.id ";

                           if($clientLastEventTime != 0)
                           {
                              $query = $query . "WHERE STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') > STR_TO_DATE('$clientLastEventTime','%Y-%m-%d %H:%i:%s') " .
                                                "ORDER BY events.time DESC";
                           }
                           else
                           {
                              $query = $query . "ORDER BY events.time DESC";
                           }
               }
               else
               {
                  $query = "SELECT events.id AS id, events.time AS eventTime, evtTypes.eventType AS eventType, " .
                           "generators.appName AS appName, generators.appUsr AS appUsr, generators.generatorOriginalName AS genName, " . 
                           "generators.generatorOriginalType AS genType, generators.containerName AS genContainer, generators.url AS url " .
                           "FROM " . $this->conf["dbName"] . ".eventsLog AS events " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".events AS evtTypes " .
                           "ON events.eventTypeId = evtTypes.id " .
                           "LEFT JOIN " . $this->conf["dbName"] . ".eventGenerators AS generators " .
                           "ON evtTypes.genId = generators.id " .
                           "WHERE generators.appUsr = '$username' ";

                           if($clientLastEventTime != 0)
                           {
                              $query = $query . "AND STR_TO_DATE(events.time,'%Y-%m-%d %H:%i:%s') > STR_TO_DATE('$clientLastEventTime','%Y-%m-%d %H:%i:%s') " .
                                                "ORDER BY events.time DESC";
                           }
                           else
                           {
                              $query = $query . "ORDER BY events.time DESC";
                           }
               }
               
               $rs = mysqli_query($this->link, $query);

               if($rs)
               {
                  $newEvents = [];
                  while($row = mysqli_fetch_assoc($rs))
                  {
                     $row['time'] = $row['eventTime'];
                     $row['type'] = $row['eventType'];
                     array_push($newEvents, $row);
                  }

                  $response['result'] = "Ok";
                  $response['detail'] = "standardCall";
                  $response['loggedIn'] = "yes";
                  $response['newEvents'] = $newEvents;
               }
               else
               {
                  $response['result'] = "queryKo";
                  $response['loggedIn'] = "yes";
               }
            }
            else
            {
               $response['result'] = "Ok";
               $response['detail'] = "standardCall";
               $response['loggedIn'] = "no";
            }
         }
      }
      return $response;
   }
   
   function updateContainerName($appName, $oldContainerName, $newContainerName)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      $response = [];
      $appName = mysqli_real_escape_string($this->link, $appName);
      $oldContainerName = mysqli_real_escape_string($this->link, $oldContainerName);
      $newContainerName = mysqli_real_escape_string($this->link, $newContainerName);
      
      if(!$this->link)
      {
         $response['detail'] = "dbConnectionKo";
         return $response;
      }
      else 
      {
         $q = "UPDATE " . $this->conf["dbName"] . ".eventGenerators SET containerName = '$newContainerName' WHERE containerName = '$oldContainerName' AND appName = '$appName'";
         $r = mysqli_query($this->link, $q);

         if($r)
         {
            $response = "Ok"; 
            return $response;
         }
         else
         {
            $response = "queryKo";
            return $response;
         }  
      }
   }
   
   
   function updateGeneratorName($appName, $oldGeneratorName, $newGeneratorName, $containerName)
   {
      $this->conf = parse_ini_file("./conf/conf.ini");
      $this->link = mysqli_connect($this->conf["dbHost"], $this->conf["dbUsr"], $this->conf["dbPwd"]);
      $response = [];
      $appName = mysqli_real_escape_string($this->link, $appName);
      $oldGeneratorName = mysqli_real_escape_string($this->link, $oldGeneratorName);
      $newGeneratorName = mysqli_real_escape_string($this->link, $newGeneratorName);
      
      if(!$this->link)
      {
         $response['detail'] = "dbConnectionKo";
       //  return $response;
      }
      else 
      {
         $q = "UPDATE " . $this->conf["dbName"] . ".eventGenerators SET generatorOriginalName = '$newGeneratorName' WHERE generatorOriginalName = '$oldGeneratorName' AND appName = '$appName' AND containerName = '$containerName'";
         $r = mysqli_query($this->link, $q);

         if($r)
         {
            $response = "Ok"; 
          //  return $response;
         }
         else
         {
            $response = "queryKo";
          //  return $response;
         }  
      }
   }
   
   
}//Fine classe
