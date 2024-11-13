<?php
$conn = new mysqli("localhost", "root", "", "sapos");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "SELECT path_imagem FROM post WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_path = $row['path_imagem'];

        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                $delete_sql = "DELETE FROM post WHERE id = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                $delete_stmt->bind_param("i", $id);
                $delete_stmt->execute();
                $delete_stmt->close();
                echo "<p class='success'>Post excluído com sucesso!</p>";
            } else {
                echo "<p class='error'>Erro ao excluir a imagem do sistema de arquivos.</p>";
            }
        } else {
            echo "<p class='error'>Arquivo não encontrado: $file_path</p>";
        }
    } else {
        echo "<p class='error'>Imagem não encontrada no banco de dados.</p>";
    }

    $stmt->close();
}

$sql = "SELECT id, titulo, path_imagem, descricao FROM post";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Exibir Imagens</title>
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
        .imagem-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .imagem-box {
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            text-align: center;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
            padding: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .view-button {
            margin-top: 15px;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
    </style>
</head>
<body>
    <h2>Melhores Itens para levar para o ENEM 2024
    </h2>
    <div class="imagem-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='imagem-box'>";
                echo "<h3>" . htmlspecialchars($row["titulo"]) . "</h3>";
                echo "<img src='" . htmlspecialchars($row["path_imagem"]) . "' alt='" . htmlspecialchars($row["titulo"]) . "'>";
                echo "<p>" . htmlspecialchars($row["descricao"]) . "</p>";
                echo "<a href='exibir_imagens.php?delete=" . $row['id'] . "' class='delete-button' onclick='return confirm(\"Você tem certeza que deseja excluir este post?\")'>Excluir</a>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhuma imagem encontrada.</p>";
        }
        ?>
    </div>
    <a href="inserir_imagem.php" class="view-button">Inserir Imagem</a>
</body>
</html>
<?php
$conn->close();
?>
