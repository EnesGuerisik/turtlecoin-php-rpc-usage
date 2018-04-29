<?php
require '../vendor/autoload.php';

session_start();

use chillerlan\QRCode\QRCode;

if (!isset($_SESSION["passconf"]) || !isset($_SESSION["ipconf"]) || !isset($_SESSION["portconf"])) {
  header('Location: login.php');
}
use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => $_SESSION["ipconf"],
    'rpcPort'     => intval($_SESSION["portconf"]),
    'rpcPassword' => $_SESSION["passconf"],
];
$walletd = new Walletd\Client($config);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Maintain</title>
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
    <link rel="stylesheet" href="css/address.css">
  </head>
  <body>
    <a href="index.php"><img height="4%" width="4%" src="img/back.png" alt="Back"></a></p>
    Create address
    <form action="maintain.php" method="post">
      <input type="hidden" name="method" value="gen">
      <input type="submit" value="Generate">
    </form>
    Delete address !WARNING!: You can only restore your wallet with the public and private spend key on commandline
      <form action="maintain.php" method="post">
      <input type="hidden" name="method" value="del">
      <input type="text" name="addr" size="85%" placeholder="Address to delete">
      <input type="submit" value="Delete">
    </form>
    <?php
    #Check request method
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      #Check what to exec
      if ($_POST["method"] == "gen") {
        #Generate new address
        $gen = $walletd->createAddress()->getBody()->getContents();
        #decode
        $decgen = json_decode($gen, true);
        #Show address with qr code
        $naddr = $decgen["result"]["address"];
        echo $naddr;
        echo '<br><img src="'.(new QRCode)->render($naddr).'" />';
      }
      elseif ($_POST["method"] == "del") {
        #Delete address
        $resp = $walletd->deleteAddress($_POST["addr"])->getBody()->getContents();
        #Decode
        $decresp = json_decode($resp, true);
        #Check for errors
        if (isset($decresp["error"])) {
          echo "<script>alert('The address is invalid, or doesn\'t exists!')</script>";
        }
        else {
          echo "<script>alert('Address deleted!')</script>";
        }
      }
    }
     ?>
  </body>
</html>
