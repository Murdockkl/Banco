<?php
$conn = new mysqli("localhost", "root", "", "sapos");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descricao = $_POST["descricao"];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["imagem"]["tmp_name"]);

    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "O arquivo não é uma imagem.";
        $uploadOk = 0;
    }

    if ($uploadOk && move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO post (titulo, path_imagem, descricao) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $titulo, $target_file, $descricao);

        if ($stmt->execute()) {
            echo "<p class='success'>Imagem e dados inseridos com sucesso!</p>";
        } else {
            echo "<p class='error'>Erro ao inserir dados: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p class='error'>Erro ao fazer o upload da imagem.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Inserir Imagem</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }
        h2 {
            color: #333;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .form-container input[type="text"],
        .form-container input[type="file"],
        .form-container textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .form-container .view-button {
            margin-top: 15px;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Inserir Nova Imagem</h2>
    <div class="form-container">
        <form action="inserir_imagem.php" method="post" enctype="multipart/form-data">
            <input type="text" name="titulo" placeholder="Título" required>
            <input type="file" name="imagem" required>
            <textarea name="descricao" rows="4" placeholder="Descrição"></textarea>
            <input type="submit" value="Enviar">
        </form>
        <a href="exibir_imagens.php" class="view-button">Ver Imagens</a>
    </div>
</body>
</html>