<?php
require_once("queries.php");
require_once("classes.php");
require_once("basic.php");

$playerID = intval($_GET['playerID']);
$opponentID = intval($_GET['opponentID']);

$query = "SELECT * FROM (SELECT * FROM shot WHERE playerID = ?) as playershot INNER JOIN (SELECT * FROM shift WHERE playerID = ?) as opponentshift ON opponentshift.gameID = playershot.gameID WHERE playershot.time >= opponentshift.starttime AND playershot.time <= opponentshift.endtime AND playershot.home <> opponentshift.home GROUP BY shotID;";

$sh = $db->prepare($query);
$sh->execute(array($playerID, $opponentID));

$html = "<table>
<tr>
<th>Time</th>
<th>Type</th>
<th>Made</th>
</tr>";
while($row = $sh->fetch()) {
    $html .= "<tr>";
    $html .= "<td>" . timeformat($row['time']) . "</td>";
    $html .= "<td>" . $row['type'] . "</td>";
    $html .= "<td>" . $row['made'] . "</td>";
    $html .= "</tr>";
}
$html .= "</table>";

echo $html;
?>