<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form name="price" action="controler.php" method="post">
    <div>
        <input type="submit" name="Print"/>
    </div>

</form>
<? if ($_GET['SHOW_TABLE'] == 'YES') {

    $jsonArray = file_get_contents('JsonData.json');
    $array = array(json_decode($jsonArray, true));
    
    echo '<table cellpadding="5" cellspacing="0" border="1">';
    ?>
    <tr>
        <td>ID</td>
        <td>Наименование товара</td>
        <td>Стоимость, руб</td>
        <td>Стоимость опт, руб</td>
        <td>Наличие на складе 1, шт</td>
        <td>Наличие на складе 2, шт</td>
        <td>Страна производства</td>
        <td>Примечание</td>
        <?
        $minValue = PHP_INT_MAX;
        $maxValue = 0;
        $price = array_column($array[0], 'price');
        $price_opt = array_column($array[0], 'price OPT');
        $stock1 = array_column($array[0], 'availability in w1');
        $stock2 = array_column($array[0], 'availability in w2');


        foreach ($price_opt as $value) {
            if ($value < $minValue) {
                $minValue = $value;
            }
        }
        foreach ($price as $value) {
            if ($value > $maxValue) {
                $maxValue = $value;
            }
        }

        ?>
    </tr>
    <?
    foreach ($array[0] as $key => $value) {

        ?>
        <tr>
            <?
            $comment = '';
            foreach ($value as $itemKey => $item) {
                $style = 'white';
                if ($itemKey == 'price OPT' && $item == $minValue) {
                    $style = 'green';
                }
                if ($itemKey == 'price' && $item == $maxValue) {
                    $style = 'red';
                }
                if ($itemKey == 'availability in w1' && $item < 20 || $itemKey == 'availability in w2' && $item < 20) {
                    $comment = "Осталось мало!! Срочно докупите!!!";
                }
                ?>
                <td style="background: <?= $style ?>"><?= $item ?></td>
                <?
            } ?>
            <td><?= $comment ?></td>
        </tr>
        <?
    }
    echo "</table>";
}


$sum_stock1 = $_SESSION ['$sum_stock1'];
$sum_stock2 = $_SESSION ['$sum_stock2'];
$avg_price = $_SESSION ['$avg_price'];
$avg_wholesale = $_SESSION ['$avg_wholesale'];

echo "
            <p>Общее количество товаров на складе 1: " . round($sum_stock1, 2) . " шт.</p>
            <p>Общее количество товаров на складе 2: " . round($sum_stock2, 2) . " шт.</p>
            <p>Средняя стоимость розничной цены товара: " . round($avg_price, 2) . " руб.</p>
            <p>Средняя стоимость оптовой цены товара: " . round($avg_wholesale, 2) . " руб.</p>;"

?>
</body>
</html>