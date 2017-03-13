<?php
require_once("queries.php");
require_once("classes.php");
require_once("basic.php");

$playerID = intval($_GET['playerID']);

$query = "SELECT * FROM shot WHERE playerID = ?";

$sh = $db->prepare($query);
$sh->execute(array($playerID));

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