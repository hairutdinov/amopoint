<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка текстового файла</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .green-circle {
            background-color: green;
        }
        .red-circle {
            background-color: red;
        }
    </style>
</head>
<body>

<?php
    require_once '../vendor/autoload.php';
    use classes\UploadFile;
    error_reporting(E_ERROR | E_PARSE);

    $show_messages = true;

    if (isset($_POST['submit'])) {
        if (!isset($_POST["show_messages_checkbox"])) {
            $show_messages = false;
        }
    }
?>
<div class="container mt-5">
    <h2>Загрузка текстового (.txt) файла</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <div class="form-group">
                <label for="file">Выберите файл</label>
                <input type="file" class="form-control-file" id="file" name="file" required>
            </div>
            <div class="form-group">
                <label for="symbolForBreakdown">Символ для разбивки</label>
                <input type="text" class="form-control"
                       id="symbolForBreakdown"
                       aria-describedby="symbolForBreakdown"
                       name="symbol_for_breakdown"
                       placeholder="Символ для разбивки"
                       required
                       value="<?= $_POST["symbol_for_breakdown"] ?? "" ?>"
                >
            </div>
            <div class="form-group">
                <label class="label"><input type="checkbox" name="show_messages_checkbox" <?= $show_messages ? "checked" : "" ?> value="1"> Выводить подробные сообщения</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Загрузить</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        try {
            if (empty($_POST["symbol_for_breakdown"] ?? "")) {
                throw new Exception("Необходимо заполнить поле \"Символ для разбивки\"");
            }

            $upload_file = new UploadFile("files/", true);
            $result = $upload_file->upload($_FILES["file"]);

            if ($show_messages) {
                echo "<div class='alert alert-success mt-3' role='alert'>" . $result["message"] . "</div>";
            }
            echo "<div class='mt-3 circle green-circle'></div>";

            $content = file_get_contents($result["target_file"]);

            foreach (explode($_POST["symbol_for_breakdown"] ?? ";", $content) as $substring) {
                $count = 0;

                if (preg_match_all('/\d/', $substring, $matches)) {
                    $count = count($matches[0]);
                }

                echo "<p>$substring = $count</p>";
            }

        } catch (Exception $e) {
            if ($show_messages) {
                echo "<div class='alert alert-danger mt-3' role='alert'>" . $e->getMessage() . "</div>";
            }
            echo "<div class='mt-3 circle red-circle'></div>";
        }
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
