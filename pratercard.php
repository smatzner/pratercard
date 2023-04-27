<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratercard</title>
</head>
<body>
    <!-- Upload Button -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="userfile[]" webkitdirectory multiple>
        <br>
        <select name="type" id="type">
            <option value="PC">PC</option>
            <option value="AC">AC</option>
        </select>
        <br>
        <input type="number" name="price" id="price" value="<?= $_POST['price'] ?>">
        <br>
        <input type="text" name="costumer" id="costumer" value="<?= $_POST['costumer'] ?>">
        <br>
        <input type="date" name="date" id="date">
        <br>
        <input type="text" name="email" id="email" value="<?= $_POST['email'] ?>">
        <br>
        <input type="hidden" name="form_folder" value="upload">
        <input type="submit" value="Submit">
    </form>
    <?php
        if(isset($_POST['form_folder'])&&($_POST['form_folder'] == 'upload')){
            echo "<pre>";
            var_dump($_POST['type']);

            if(!empty($_POST['date'])){
                $date = new DateTimeImmutable($_POST['date']);
                $date = $date->format('d.m.y');
            }
            else{
                $date = date('d.m.y');
            }

            echo "</pre>";



            $vouchers = [];
            foreach ($_FILES['userfile']['name'] as $filename) {
                if(str_contains($filename,'prater_order')){
                    preg_match('/\d{4}/',$filename,$matches);
                    $billnum = 'WEB-'.$matches[0];
                }
                if(str_contains($filename,'Pratercard_voucher')){
                    preg_match('/\d{16}/',$filename,$matches);
                    array_push($vouchers,$matches);
                }
            }




            echo '<table border=1 id="table">';
            echo '<tr>';
            echo '<th>Typ</th>';
            echo '<th>Kartennummer</th>';
            echo '<th>Wert</th>';
            echo '<th>Rechnungsnummer</th>';
            echo '<th>paypal</th>';
            echo '<th>praterwebshop_</th>';
            echo '<th>Kunde</th>';
            echo '<th>Rechungsdatum</th>';
            echo '<th>Erstellungadatum</th>';
            echo '<th>Postdatum</th>';
            echo '<th>Porto</th>';
            echo '<th>Banküberweisung</th>';
            echo '<th>Email Adresse</th>';
            echo '</tr>';
            foreach($vouchers as $voucher){
                echo '<tr>';
                echo '<td>'.$_POST['type'].'</td>';
                echo '<td>'.$voucher[0].'</td>';
                echo '<td>'.$_POST['price'].'</td>';
                echo '<td>'.$billnum.'</td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td>'.$_POST['costumer'].'</td>';
                echo '<td>'.$date.'</td>';
                echo '<td>'.$date.'</td>';
                echo '<td>digitale Zustellung</td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td>'.$_POST['email'].'</td>';
                echo '</tr>';

            }
            echo '</table>';

            echo $_POST['type'].' '.$vouchers[0][0];
        }


    ?>

    <button name="copy" id="copybtn" onclick="copyText()">Copy Text</button> <span id="msg"></span> 

    <script>
        function copyText()
        {
        var mytext = document.getElementById("table");	
            
        mytext.select(); //select text field

        document.execCommand("copy");  //Copy text

        document.getElementById("msg").innerHTML = "Copied";
        }	
    </script>
</body>
</html>