<?php
#Load libs
require '../vendor/autoload.php';

#Start session
session_start();

#Check Session
if (!isset($_SESSION["passconf"]) || !isset($_SESSION["ipconf"]) || !isset($_SESSION["portconf"])) {
  header('Location: login.php');
}
#Config
use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => $_SESSION["ipconf"],
    'rpcPort'     => intval($_SESSION["portconf"]),
    'rpcPassword' => $_SESSION["passconf"],
];

$walletd = new Walletd\Client($config);

$uctrans = $walletd->getDelayedTransactionHashes()->getBody()->getContents();

$decuctrans = json_decode($uctrans, true);
$uctcount = count($decuctrans["result"]["transactionHashes"]);

$transc = count($_SESSION["thistory"]);
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>History(session)</title>
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
    <link rel="stylesheet" href="css/history.css">
  </head>
  <body>
    <a href="index.php"><img height="4%" width="4%" src="img/back.png" alt="Back"></a></p>
    Sent transactions:<br>
    <?php
    for ($i=1; $i < $transc; $i++) {
      echo "<a target='_blank' href='https://turtle-coin.com/?hash=" .  $_SESSION["thistory"][$i] . "#blockchain_transaction'>" .  $_SESSION["thistory"][$i] . "</a><br>";
    }
    if ($transc == 1) {
      echo "<span>No transactions found!</span>";
    }
     ?>
   </p>Transactions you didn't confirm<br>
     <?php
     for ($i=0; $i < $uctcount; $i++) {
       $yeslink = 'javascript:window.location = "transact.php?send=' . $decuctrans["result"]["transactionHashes"][$i] . '"';
       $nolink = 'javascript:window.location = "transact.php?cancel=' . $decuctrans["result"]["transactionHashes"][$i] . '"';
       echo $decuctrans["result"]["transactionHashes"][$i] . "<button onclick='" . $yeslink . "'>Confirm</button><button onclick='" . $nolink . "'>Cancel</button>";
     }
     if ($uctcount == 0) {
       echo "<span>No transactions found!</span>";
     }
      ?>
  </body>
</html>
