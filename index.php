<?php
session_start();
require_once('function.php');

if (isset($_SESSION['error'])) {
    echo $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_POST['submit']) && !empty($_POST['keywords'])) {
    
    $keywords = $_POST['keywords'];

    # separate keywords into an array by new line
    $keywords = explode("\n", $keywords);
    $keywords = array_map('trim', $keywords);
    foreach ($keywords as $keyword) {
        if (strlen($keyword) == 0) {
            unset($keywords[array_search($keyword, $keywords)]);
        }
    }

    $firstKeyword = $keywords[0];
    $result = get_all_links($keywords);

    unset($keywords[0]); // firt keyword removed from list
    $mainLinks = $result[1];
    unset($result[1]);

    $ratios = array();
    $i=1;
    foreach($result as $data){
        $ratios[$i] = get_ratio($mainLinks,$data);
        $i++;
    }

    #sorting for result
    $ratio = array();
    foreach($keywords as $key => $value){
        $ratio[$value] = $ratios[$key];
    }
    
    write_to_xlsx($keywords,$ratio);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Keywords</title>
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Keyword Ratio Compare</span>
        </div>
    </nav>

    <div class="container mt-3">
        <form method="POST">
            <div class="row">
                <div class="col-md-8 col-12">
                    <div class="form-floating">
                        <textarea class="form-control" placeholder="Enter Keywords List Here" id="floatingTextarea2" style="height: 15em" name="keywords"></textarea>
                        <label for="floatingTextarea2">Keywords List</label>
                    </div>
                </div>
                <div class="col-md-4 col-12 mt-4 pt-4">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th class="table-success">Firt Keyword</th>
                    </tr>
                    <tr>
                        <td><?= isset($firstKeyword) ? $firstKeyword : '' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <button class="btn btn-success" id="exportBtn">Export All</button>
                <table class="table table-bordered">
                    <tr>
                        <th class="table-success">Sub Keyword</th>
                        <th class="table-success">Ratio</th>
                    </tr>

                    <?php if (isset($keywords)) : ?>
                        <?php foreach ($keywords as $keyword) : ?>
                            <tr>
                                <td><?= $keyword ?></td>
                                <td><?= $ratio[$keyword] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {
            // download the xlsx file from this directory
            window.location.href = 'Keyword Ratio.xlsx';
        });
    </script>
</body>

</html>