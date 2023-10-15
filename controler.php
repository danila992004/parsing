<?php
require('php-excel-reader/excel_reader2.php');
require('php-excel-reader/SpreadsheetReader.php');

session_start();
$dbhost = "localhost";
$dblogin = "root";
$dbpass = "";
$dbname = "task-mysql";
$tablename = "pricelist";
$truncate_table_query = "TRUNCATE TABLE $tablename";

$mysqli = new mysqli($dbhost, $dblogin, $dbpass, $dbname );
$reader = new SpreadsheetReader('pricelist.xls');
$conn = new mysqli("$dbhost", "$dblogin", "$dbpass", "$dbname");


if ($conn->connect_error) 
    die("Ошибка: " . $conn->connect_error);

mysqli_query($conn, $truncate_table_query);

$count = 0;

foreach ($reader as $row) {
    
    if ($count == 0 || $row[1] == 'Стоимость') {
    
        $count++;
        continue;

    }

    $sql = "INSERT INTO `pricelist` ( `name`, `price`, `price OPT`, `availability in w1`, `availability in w2`, `made in`) VALUES (  '$row[0]', '$row[1]', '$row[2]', ' $row[3]' , '$row[4]', '$row[5]' )";
 
    $conn->query($sql);

}

$conn->close();
$query = "SELECT * FROM `pricelist` ";
$result = $mysqli->query($query);


$row = $result->fetch_all(MYSQLI_ASSOC);
$jsonArray = json_encode($row);
file_put_contents('JsonData.json', $jsonArray);


$mysqli->query($sql);

$result = $mysqli->query("SELECT * FROM `pricelist`");
$max = $mysqli->query("SELECT MAX('price') FROM `pricelist`");
$max_price = $max->fetch_row()[0];
$min = $mysqli->query("SELECT MIN(`price OPT`) FROM `pricelist`");
$min_price = $min->fetch_row()[0];
$sum_1 = $mysqli->query("SELECT SUM(`availability in w1`) FROM `pricelist`");
$sum_stock1 = $sum_1->fetch_row()[0];
$sum_2 = $mysqli->query("SELECT SUM(`availability in w2`) FROM `pricelist`");
$sum_stock2 = $sum_2->fetch_row()[0];
$avg_1 = $mysqli->query("SELECT AVG(`price`) FROM `pricelist`");
$avg_price = $avg_1->fetch_row()[0];
$avg_2 = $mysqli->query("SELECT AVG(`price OPT`) FROM `pricelist`");
$avg_wholesale = $avg_2->fetch_row()[0];
$mysqli->close();


$_SESSION ['$sum_stock1'] = $sum_stock1;
$_SESSION ['$sum_stock2'] = $sum_stock2;
$_SESSION ['$avg_price'] = $avg_price;
$_SESSION ['$avg_wholesale'] = $avg_wholesale;
?>
<script>
    window.location.href = "http://test-parsing.com/?SHOW_TABLE=YES";
</script>