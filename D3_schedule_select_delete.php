<?php
include("include_list.php");

// D9_刪schedule表_重建隨機預約

$sql_cmd1 = "DROP TABLE `schedule`;";

$sql_cmd2 = "CREATE TABLE `schedule` (
    `table_id` int(5) NOT NULL,
    `date` date NOT NULL,
    `0800` int(11) DEFAULT NULL,
    `0900` int(11) DEFAULT NULL,
    `1000` int(11) DEFAULT NULL,
    `1100` int(11) DEFAULT NULL,
    `1200` int(11) DEFAULT NULL,
    `1300` int(11) DEFAULT NULL,
    `1400` int(11) DEFAULT NULL,
    `1500` int(11) DEFAULT NULL,
    `1600` int(11) DEFAULT NULL,
    `1700` int(11) DEFAULT NULL,
    `1800` int(11) DEFAULT NULL,
    `1900` int(11) DEFAULT NULL,
    `2000` int(11) DEFAULT NULL,
    `2100` int(11) DEFAULT NULL,
    `2200` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql_cmd3 = "ALTER TABLE `schedule` ADD PRIMARY KEY (`table_id`);";

$sql_cmd4 = "ALTER TABLE `schedule` MODIFY `table_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;";

$db_link->query($sql_cmd1);
$db_link->query($sql_cmd2);
$db_link->query($sql_cmd3);
$db_link->query($sql_cmd4);

// 隨機新增預約資料
$time_list = array("`0800`", "`0900`", "`1000`", "`1100`", "`1200`", "`1300`", "`1400`", "`1500`", "`1600`", "`1700`", "`1800`", "`1900`", "`2000`", "`2100`", "`2200`");
for ($i = 0; $i < 100; $i++) {
    $re = rand(0, 100) . "<br>";
    $d = strtotime("+$i days");
    $tim = date('Y-m-d', $d);
    if ($re < 10) {
        // echo "<font color='red'>null</font><br>";
        $sql_cmd = "INSERT INTO `schedule` (`date`) VALUES ('$tim');";
        // echo $sql_cmd;
        $res = $db_link->query($sql_cmd);
    } elseif (9 < $re && $re < 101) {
        $ti_sta = $time_list[array_rand($time_list, 1)];
        $rand_member=rand(2,13);
        $sql_cmd2 = "INSERT INTO `schedule` (`date`,$ti_sta) VALUES ('$tim',$rand_member);";
        // echo $sql_cmd2;
        $res = $db_link->query($sql_cmd2);
    }
}
header("Location: D3_schedule_select.php");

// // 無資料表格
// for ($i = 0; $i < 100; $i++) {
//     $re = rand(0, 100) . "<br>";
//     $d = strtotime("+$i days");
//     $tim = date('Y-m-d', $d);
//     $sql_cmd = "INSERT INTO `schedule` (`date`) VALUES ('$tim');";
//     $res = $db_link->query($sql_cmd);
// }
// header("Location: D3_schedule_select.php");
