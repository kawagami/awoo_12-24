<?php
function GetSQLValueString($theValue, $theType)
{
    switch ($theType) {
        case "string":
            $theValue = ($theValue != "") ? filter_var($theValue, FILTER_SANITIZE_MAGIC_QUOTES) : "";
            break;
        case "int":
            $theValue = ($theValue != "") ? filter_var($theValue, FILTER_SANITIZE_NUMBER_INT) : "";
            break;
    }
    return $theValue;
}
require_once("connMysql.php");
session_start();

//檢查是否經過登入
if (!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"] == "")) {
    header("Location: login.php");
}
//執行登出動作
if (isset($_GET["logout"]) && ($_GET["logout"] == "true")) {
    unset($_SESSION["loginMember"]);
    header("Location: index2.php");
}
//繫結登入會員資料
$query_RecMember = "SELECT * FROM memberdata WHERE m_username='{$_SESSION["loginMember"]}'";
$RecMember = $db_link->query($query_RecMember);
$row_RecMember = $RecMember->fetch_assoc(); //存為陣列
$m_id = $row_RecMember["m_id"];


//新增廚房
if (isset($_POST["action"]) && ($_POST["action"] == "add")) {
    $query_insert = "INSERT INTO kitchen_data (kit_title ,kit_county ,kit_city ,kit_add ,kit_dese ,kit_startdate ,
   kit_enddate ,kit_starttime ,kit_endtime ,kit_capacity ,kit_price ,kit_cleanfee ,m_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $db_link->prepare($query_insert);
    $stmt->bind_param(
        "sssssssssiiii",
        GetSQLValueString($_POST["kit_title"], "string"),
        GetSQLValueString($_POST["kit_county"], "string"),
        GetSQLValueString($_POST["kit_city"], "string"),
        GetSQLValueString($_POST["kit_add"], "string"),
        GetSQLValueString($_POST["kit_dese"], "string"),
        GetSQLValueString($_POST["kit_startdate"], "string"),
        GetSQLValueString($_POST["kit_enddate"], "string"),
        GetSQLValueString($_POST["kit_starttime"], "string"),
        GetSQLValueString($_POST["kit_endtime"], "string"),
        GetSQLValueString($_POST["kit_capacity"], "int"),
        GetSQLValueString($_POST["kit_price"], "int"),
        GetSQLValueString($_POST["kit_cleanfee"], "int"),
        GetSQLValueString($m_id, "int"));
    $stmt->execute();

    //取得新增的相簿編號
    $kit_id = $stmt->insert_id;
    $stmt->close();

    for ($i = 0; $i < count($_FILES["kit_picurl"]["name"]); $i++) {
        if ($_FILES["kit_picurl"]["tmp_name"][$i] != "") {
            $query_insert = "INSERT INTO kitchen_photo (kit_id, kit_date, kit_picurl, kit_subject) VALUES (?, NOW(), ?, ?)";
            $stmt = $db_link->prepare($query_insert);
            $stmt->bind_param(
                "iss",
                GetSQLValueString($kit_id, "int"),
                GetSQLValueString($_FILES["kit_picurl"]["name"][$i], "string"),
                GetSQLValueString($_POST["kit_subject"][$i], "string"));
            $stmt->execute();
            if (!move_uploaded_file($_FILES["kit_picurl"]["tmp_name"][$i], "photos/" . $_FILES["kit_picurl"]["name"][$i])) die("檔案上傳失敗！");
            $stmt->close();
        }
    }
    //重新導向到修改畫面
    header("Location: kit_index.php");
}

//系統日期
$today = date("Y-m-d");
?>

<html>
    <head>
        <link rel="stylesheet" href="index2.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 shrink-to-fit=no">
		<meta charset="utf-8">
		<title>上傳我的廚房</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript" src="scripts/city.js"></script>
		<script language="javascript">
		function checkForm() {
            if (document.kit_upload.kit_title.value == "") {
                alert("請填寫廚房名稱!");
                document.kit_upload.kit_title.focus();
                return false;
            }
            if (document.kit_upload.kit_county.value == "") {
                alert("請填寫縣市!");
                document.kit_upload.kit_county.focus();
                return false;
            }
            if (document.kit_upload.kit_city.value == "") {
                alert("請填寫鄉鎮市區!");
                document.kit_upload.kit_city.focus();
                return false;
            }
            if (document.kit_upload.kit_add.value == "") {
                alert("請填寫地址!");
                document.kit_upload.kit_add.focus();
                return false;
            }
            if (document.kit_upload.kit_dese.value == "") {
                alert("請填寫描述!");
                document.kit_upload.kit_dese.focus();
                return false;
            }
            if (document.kit_upload.kit_startdate.value == "") {
                alert("請填寫起始日期!");
                document.kit_upload.kit_startdate.focus();
                return false;
            }
            if (document.kit_upload.kit_enddate.value == "") {
                alert("請填寫結束日期!");
                document.kit_upload.kit_enddate.focus();
                return false;
            }
            if (document.kit_upload.kit_starttime.value == "") {
                alert("請填寫開始時間!");
                document.kit_upload.kit_starttime.focus();
                return false;
            }
            if (document.kit_upload.kit_endtime.value == "") {
                alert("請填寫結束時間!");
                document.kit_upload.kit_endtime.focus();
                return false;
            }
            if (document.kit_upload.kit_capacity.value == "" || document.kit_upload.kit_capacity.value == 0) {
                alert("請填寫容納人數!");
                document.kit_upload.kit_capacity.focus();
                return false;
            }
            if (!checkcapacity(document.kit_upload.kit_capacity)) {
                document.kit_upload.kit_capacity.focus();
                return false;
            }
            if (document.kit_upload.kit_price.value == "" || document.kit_upload.kit_price.value == 0) {
                alert("請填寫價格!");
                document.kit_upload.kit_price.focus();
                return false;
            }
            if (!checkprice(document.kit_upload.kit_price)) {
                document.kit_upload.kit_price.focus();
                return false;
            }
            if (!checkcleanfee(document.kit_upload.kit_cleanfee)) {
                document.kit_upload.kit_cleanfee.focus();
                return false;
            }
            return confirm('確定送出嗎？');
        }

        function checkcapacity(kit_capacity) {
            var filter = /^[1-9][0-9]*$/;
            if (filter.test(kit_capacity.value)) {
                return true;
            }
            alert("容納人數 請填寫半形數字!");
            return false;
        }

        function checkprice(kit_price) {
            var filter = /^[1-9][0-9]*$/;
            if (filter.test(kit_price.value)) {
                return true;
            }
            alert("價格 請填寫半形數字!");
            return false;
        }

        function checkcleanfee(kit_cleanfee) {
            var filter = /^[0-9]*$/;
            if (filter.test(kit_cleanfee.value)) {
                return true;
            }
            alert("清潔費用 請填寫半形數字!");
            return false;
        }

        $('document').ready(function() {
            $("#kit_startdate").change(function() {
                document.getElementById("kit_enddate").min = document.getElementById("kit_startdate").value;
            });
        });

        $('document').ready(function() {
            $("#kit_starttime").change(function() {
                document.getElementById("kit_endtime").min = document.getElementById("kit_starttime").value;
            });
        });

        $('document').ready(function() {
            $("#select_add1").citySelect({cityTraget: "kit_county"});
        })
    </script>

    <style type="text/css">
        .kit_add{
            width:60%;
            margin:auto;
        }

        .title{
            text-align:center;
        }

    </style>
    
	</head>
    <body style="font-family: Microsoft JhengHei;">
	<div class="id_wrapper">
		<header class="header">
            <a href="index2.php" class="logo" style="text-decoration:none;color:#fff;">來吃飯</a>
            <input class="menu-btn" type="checkbox" id="menu-btn" />
            <label class="menu-icon" for="menu-btn"><span class="nav-icon"></span></label>
            <ul class="menu">
				<li><a href="about.php">品牌起源</a></li>
				<li><a href="kitchen_index.php">看看環境</a></li>
				<li><a href="#subscribe">?????</a></li>
				<?php if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){?>
				<li><a href="member_index.php">Sign in</a></li><br>
				<!--這段更改--><a href="member_admin.php"><?php }else{ echo "<li><a href='member_center.php'>會員中心</a></li>"."<li><a href='?logout=true'>Sign out&nbsp[".$_SESSION["loginMember"]."]</a></li>";?><?php }?></a>
			</ul>
        </header>
		<div class="id_content">
			<div class="container" style="margin: auto;width:100%;"> 
                <div class="kit_add">
                    <table>
                        <tr>
                        <form id="kit_upload" name="kit_upload" method="POST" enctype="multipart/form-data" action="" onSubmit="return checkForm();">
                        <h1 class="title">上傳我的廚房</h1>
                        <hr style="border: 0; height: 1px; background: #333; background-image: linear-gradient(to right, #ccc, #333, #ccc);">
                        <div class="kit_info">
                            <p>廚房名稱　<input class="normalinput" id="kit_title" name="kit_title" type="text"></p>
                            <p>地　　址　<span id="select_add1"></span>
                            <input id="kit_county" name="kit_county" type="hidden" value="">
                            <input id="kit_city" name="kit_city" type="hidden" value="">
                            <input class="normalinput" id="kit_add" name="kit_add" type="text" size="40" />
                   
                            <p>描　　述　<textarea class="normalinput" id="kit_dese" name="kit_dese" cols="45" rows="5"></textarea></p>
                            <p>開放日期　<input class="normalinput" id="kit_startdate" name="kit_startdate" type="date" min="<?php echo $today; ?>"> - <input class="normalinput" id="kit_enddate" name="kit_enddate" type="date"></p>
                            <p>開放時間　<input class="normalinput" id="kit_starttime" name="kit_starttime" type="time"> - <input class="normalinput" id="kit_endtime" name="kit_endtime" type="time"></p>
                            <p>容納人數　<input class="normalinput" id="kit_capacity" name="kit_capacity" type="text"> 人
                            <p>價　　格　<input class="normalinput" id="kit_price" name="kit_price" type="text"> 元</p>
                            <p>清潔費用　<input class="normalinput" id="kit_cleanfee" name="kit_cleanfee" type="text"> 元</p>
                        </div> 

                        <div class="new_photo"> 
                            <h3><strong>環境照片</strong></h3>                       
                            <p>照片1 <input type="file" name="kit_picurl[]" id="kit_picurl[]" />
                                說明1：<input class="normalinput" type="text" name="kit_subject[]" id="kit_subject[]" /></p>
                            <p>照片2 <input type="file" name="kit_picurl[]" id="kit_picurl[]" />
                                說明2：<input class="normalinput" type="text" name="kit_subject[]" id="kit_subject[]" /></p>
                            <p>照片3 <input type="file" name="kit_picurl[]" id="kit_picurl[]" />
                                說明3：<input class="normalinput" type="text" name="kit_subject[]" id="kit_subject[]" /></p>
                            <p>照片4 <input type="file" name="kit_picurl[]" id="kit_picurl[]" />
                                說明4：<input class="normalinput" type="text" name="kit_subject[]" id="kit_subject[]" /></p>
                            <p>照片5 <input type="file" name="kit_picurl[]" id="kit_picurl[]" />
                                說明5：<input class="normalinput" type="text" name="kit_subject[]" id="kit_subject[]" /></p>
                        </div>

                        <div class="ctl">
                            <input id="action" name="action" class="button button1" type="hidden" value="add">
                            <input id="button" name="button" class="button button1" type="submit" value="送出" />
                            <input id="reset" name="reset" class="button button1" type="reset" value="重設資料">
                            <input id="button2" type="button" name="button2" class="button button1" value="回上一頁" onClick="window.history.back();" />
                        </div>        
                        </form>   
                        </tr>
                    </table>
                </div>               
			</div> 
        </div>    		    
		<footer class="id_footer">
        © 2019
		</footer>

	</div>
	</body>
</html>