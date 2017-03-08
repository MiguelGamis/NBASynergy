<?php
require("queries.php");
require("classes.php");

$playerID = intval($_GET['playerID']);

$query = "SELECT firstname, lastname FROM player WHERE playerID = ?";
$sh = $db->prepare($query);
$sh->execute(array($playerID));

$html = "<table>
<tr>
<th>Firstname</th>
<th>Lastname</th>
</tr>";
while($row = $sh->fetch()) {
    $html .= "<tr>";
    $html .= "<td>" . $row['firstname'] . "</td>";
    $html .= "<td>" . $row['lastname'] . "</td>";
    $html .= "</tr>";
}
$html .= "</table>";

echo $html;
?>