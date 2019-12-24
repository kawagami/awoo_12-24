<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="index4.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<body style="font-family: Microsoft JhengHei;">
    <div class="id_wrapper">
        <header class="header">
            <a href="index2.php" class="logo" style="text-decoration:none;color:#fff;">來吃飯</a>
            <input class="menu-btn" type="checkbox" id="menu-btn" />
            <label class="menu-icon" for="menu-btn"><span class="nav-icon"></span></label>
            <ul class="menu">
                <li><a href="about.html">我悶的起源</a></li>
                <li><a href="#forget">看看環境</a></li>
                <li><a href="orderdetail.php">訂單查詢</a></li>
                <?php if (!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"] == "")) { ?>
                    <li><a href="member_index.php">Sign in</a></li><br>
                    <!--這段更改--><a href="member_admin.php"><?php } else {
                                                            echo "<li><a href='?logout=true'>Sign out&nbsp[" . $_SESSION["loginMember"] . "]</a></li>"; ?><?php } ?></a>
            </ul>
        </header>

        <div class="id_content">
            <?php
            include("connMysql.php");
            session_start();

            //由會員登入後的頁面轉到這個頁面就能在$_SESSION['m_id']抓到mid了
            $mid = $_SESSION['m_id'];
            //設定管理者ID
            $specific_id = 1;
            if ($_SESSION['m_id'] == $specific_id) {
                $cmd = "SELECT * FROM `kitchen_data`";
                $res = $db_link->query($cmd);
                echo "<form action='manage_schedule_main.php' method='post' align='center'>";
                echo "<select name='choice'>";
                echo "<option value=''>選擇要修改的場地</option>";
                while ($result = $res->fetch_assoc()) {
                    //取得使用這頁面的人擁有的場地ID
                    $a = $result['kit_id'];
                    $b = $result['kit_title'];
                    echo "<option value='$a'>$a" . "：" . "$b</option>";
                    // echo "<pre>" . print_r($result, TRUE) . "</pre>";
                }
                echo "</select>";
                echo "<input type='submit' value='確認'>";
                echo "</form>";
            } else {
                $cmd = "SELECT * FROM `kitchen_data` WHERE `m_id`=$mid";
                $res = $db_link->query($cmd);
                echo "<form action='manage_schedule_main.php' method='post' align='center'>";
                echo "<select name='choice'>";
                echo "<option value=''>選擇要修改的場地</option>";
                while ($result = $res->fetch_assoc()) {
                    //取得使用這頁面的人擁有的場地ID
                    $a = $result['kit_id'];
                    $b = $result['kit_title'];
                    echo "<option value='$a'>$a" . "：" . "$b</option>";
                    // echo "<pre>" . print_r($result, TRUE) . "</pre>";
                }
                echo "</select>";
                echo "<input type='submit' value='確認'>";
                echo "</form>";
            }

            // if (isset($_POST["acc"])) {
            //     $loginer[] = array("acc" => $_POST["acc"], "pwd" => $_POST["pwd"]);
            // }
            // // echo "<pre>" . print_r($loginer, TRUE) . "</pre>";
            // echo $loginer[0]["acc"];

            // 預計新增依照登入身分不同 可選擇的場地不同

            // //----------------------------全部場地都顯示 admin用---------------------------------------------------
            // // 場地時間表arr
            // $cmd = "SELECT `productid`,`productname` FROM `product`";
            // $res = $db_link->query($cmd);
            // while ($result = $res->fetch_assoc()) {
            //     $arr[] = array($result['productid'] => $result['productname']);
            // }
            // // echo "<pre>" . print_r($arr , TRUE) . "</pre>";
            // echo "<form action='manage_schedule_main.php' method='post' align='center'>";
            // echo "<select name='choice'>";
            // echo "<option value=''>選擇要修改的場地</option>";
            // foreach ($arr as $l2) {
            //     foreach ($l2 as $a => $b) {
            //         echo "<option value='$a'>$a" . "：" . "$b</option>";
            //     }
            // }
            // echo "</select>";
            // echo "<input type='submit' value='確認'>";
            // echo "</form>";
            // //-------------------------------------------------------------------------------

            //--------------------取得場地擁有者的mid-----------------------------------------------------------
            $check_owner_cmd = "SELECT `m_id` FROM `kitchen_data`GROUP BY `m_id`";
            $check_owner_cmd_res = $db_link->query($check_owner_cmd);
            while ($check_owner_cmd_result = $check_owner_cmd_res->fetch_assoc()) {
                // echo $check_owner_cmd_result["m_id"]."<br>";
                $owner_mid[] = $check_owner_cmd_result["m_id"];
            }
            // echo "<pre>" . print_r($owner_mid, TRUE) . "</pre>";
            //-------------------------------------------------------------------------------

            //--------------------取得場地擁有者X 所擁有的場地資訊-----------------------------------------------
            foreach ($owner_mid as $a => $b) {
                //取得sql cmd
                //依照擁有者分類 取得其下場地所有資訊的sql cmd
                $get_owner_field_info[] = "SELECT * FROM `kitchen_data` WHERE `m_id`=$b";
            }
            // echo "<pre>" . print_r($get_owner_field_info, TRUE) . "</pre>";

            //能進入這頁面的有admin、場地擁有者
            //admin有所有場地的權限 場地擁有者只有他自己場地的權限


            ?>
        </div>

        </html>
        <!-- 123 -->