<html>
    <head>
        <script>
            function showUser(str) {
                if (str == "") {
                    document.getElementById("txtHint").innerHTML = "";
                    return;
                } else {
                    if (window.XMLHttpRequest) {
                        // code for IE7+, Firefox, Chrome, Opera, Safari
                        xmlhttp = new XMLHttpRequest();
                    } else {
                        // code for IE6, IE5
                        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                    xmlhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("txtHint").innerHTML = this.responseText;
                        }
                    };
                    xmlhttp.open("GET", "getuser.php?q=" + str, true);
                    xmlhttp.send();
                }
            }
        </script>
    </head>
    <body>

        <form>
            <?php
            $dbhost = 'localhost';
            $dbuser = 'root';
            $dbpass = '';
            $conn = mysql_connect($dbhost, $dbuser, $dbpass);

            if (!$conn) {
                die('Could not connect: ' . mysql_error());
            }
            $sql = "SELECT * FROM clients";
            mysql_select_db('colleges');
            $retval = mysql_query($sql, $conn);

            if (!$retval) {
                die('Could not get data: ' . mysql_error());
            }
            while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $arrclient[] = $row;
            }
            ?>
            <select name = "users" onchange = "showUser(this.value)">
                <option value = "">Select a person:</option>

                <?php foreach ($arrclient as $key => $value) {
                    ?>
                    <option value = "<?php echo $key; ?>"><?php echo $value['name']; ?></option>
                <?php }
                ?>
            </select>

        </form>
        <br>
        <div id="txtHint"><b>Person info will be listed here...</b></div>

    </body>
</html> 