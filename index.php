<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./css/bootstrap/bootstrap.min.css">
        <style>
            *{
                font-family:sans-serif;
                font-size: small;
            }
        </style>
        <title>Pratercard</title>
    </head>
    <body>
        <div class="w-75 mx-auto my-5">
        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="input-group">
                <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFile04" aria-label="Upload" name="userfile[]" webkitdirectory multiple>
                <button class="btn btn-outline-secondary" type="submit" id="inputGroupFile04">Upload</button>
                <input type="hidden" name="form_folder" value="upload">
            </div>
        </form>

        <?php
            require_once __DIR__.'/vendor/autoload.php';

            ini_set('log_errors', 1);
            ini_set('error_log', __DIR__.'/logs/error.log');

            if(isset($_POST['form_folder'])&&($_POST['form_folder'] == 'upload')){
                if(!empty(array_filter(($_FILES['userfile']['name'])))){
                    $vouchers = [];
                    foreach ($_FILES['userfile']['name'] as $filename) {
                        if(str_contains($filename,'Pratercard_voucher')){
                            preg_match('/\d{16}/',$filename,$matches);
                            array_push($vouchers,$matches);
                        }
                        if(str_contains($filename,'Adrenalincard_voucher')){
                            preg_match('/\d{10}/',$filename,$matches);
                            array_push($vouchers,$matches);
                        }
                    }

                    foreach($_FILES['userfile']['name'] as $k => $v){
                        if(str_contains($v,'prater_order')){
                            $pdfFile = $v;
                            $pdfTmpPath = $k;
                        }
                    }

                    $parser = new \Smalot\PdfParser\Parser();

                    $file = $_FILES['userfile']['tmp_name'][$pdfTmpPath];

                    $pdf = $parser->parseFile($file);

                    $textContent = $pdf->getText();

                    if(str_contains($textContent,'ADRENALINCARD')){
                        $type = 'AC';
                    }
                    elseif(str_contains($textContent,'Partycard')){
                        $type = 'KD';
                    }
                    else{
                        $type = 'PC';
                    }

                    $pattern = '/€([^(]+)\(/';
                    preg_match($pattern, $textContent, $value);
                    $value = $value[1];

                    $pattern = '/(?<=Rechnungsdatum:\s)(.*)(?=\s*Rechnungsnummer)/';
                    preg_match($pattern, $textContent, $billdate);
                    $billdate = $billdate[0];

                    $pattern = '/(?<=Rechnungsnummer:\s)(.*)(?=\s*Rechnungsadresse)/';
                    preg_match($pattern, $textContent, $billnum);
                    $billnum = $billnum[0];

                    $pattern = '/Rechnungsadresse: ([^,]+)/';
                    preg_match($pattern, $textContent, $costumer);
                    $costumer = $costumer[1];

                    $pattern = '/\(([^)]+)\)/';
                    preg_match($pattern, $textContent, $emailAdress);
                    $emailAdress = $emailAdress[1];


                    echo '<table class="table table-hover my-3">';
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
                    $email = true;
                    $copyData = [];
                    foreach($vouchers as $voucher){

                        array_push($copyData,implode('\t',[$type,$voucher[0],$value,$billnum,'','',$costumer,$billdate,$billdate,'digitale Zustellung','','',$email ? $emailAdress : '']));
                        
                        echo '<tr>';
                        echo '<td>'.$type.'</td>';
                        echo '<td>'.$voucher[0].'</td>';
                        echo '<td>'.$value.'</td>';
                        echo '<td>'.$billnum.'</td>';
                        echo '<td></td>';
                        echo '<td></td>';
                        echo '<td>'.$costumer.'</td>';
                        echo '<td>'.$billdate.'</td>';
                        echo '<td>'.$billdate.'</td>';
                        echo '<td>digitale Zustellung</td>';
                        echo '<td></td>';
                        echo '<td></td>';
                        if($email == true){
                            echo '<td>'.$emailAdress.'</td>';
                            $email = false;
                        }
                        else{
                            echo '<td></td>';
                        }
                        echo '</tr>';

                    }
                    echo '</table>';

                    $copyData = implode('\n',$copyData);

                    echo '<button class="btn btn-outline-secondary" id="copyButton">Copy</button>';
                }
            }
            ?>
        </div>

    <script>
        const copyData = '<?= isset($copyData) ? $copyData : '';?>';
        const submitButton = document.getElementById('copyButton');
        
        if(copyData){
            submitButton.addEventListener('click', () =>{
                const textArea = document.createElement('textArea');
                textArea.value = copyData;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            });
        }

    </script>
    </body>
</html>