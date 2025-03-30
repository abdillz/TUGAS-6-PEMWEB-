<?php
include 'config.php';
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $sql);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Insert new record
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $sql = "INSERT INTO products (name, price, description) VALUES ('$name', $price, '$description')";
        mysqli_query($conn, $sql);
    } elseif (isset($_POST['update'])) {
        // Update record
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $sql = "UPDATE products SET name='$name', price=$price, description='$description' WHERE id=$id";
        mysqli_query($conn, $sql);
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id=$id";
    mysqli_query($conn, $sql);
    header('Location: index.php');
    exit();
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern CRUD Application</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 2.5rem;
            position: relative;
            padding-bottom: 10px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background-color: var(--accent-color);
            border-radius: 2px;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(41, 56, 126, 0.2);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
            margin-right: 10px;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #d1145a;
        }

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-success:hover {
            background-color:rgb(12, 52, 69);
        }

        .action-buttons {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #f1f3ff;
        }

        .actions-cell {
            display: flex;
            gap: 10px;
        }

        .action-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 4px;
            transition: var(--transition);
        }

        .action-link:hover {
            background-color: rgba(67, 97, 238, 0.1);
        }

        .action-link.edit {
            color: var(--success-color);
        }

        .action-link.delete {
            color: var(--danger-color);
        }

        .action-link.delete:hover {
            background-color: rgba(247, 37, 133, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .price {
            font-weight: 600;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .card {
                padding: 15px;
            }
            
            th, td {
                padding: 10px;
            }
            
            .actions-cell {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Management</h1>
        
        <div class="card">
            <!-- Add/Edit Form -->
            <form method="POST">
                <input type="hidden" name="id" value="<?= isset($_GET['edit']) ? htmlspecialchars($_GET['edit']) : '' ?>">
                
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?= isset($_GET['edit']) ? htmlspecialchars($products[array_search($_GET['edit'], array_column($products, 'id'))]['name']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" step="0.01" name="price" value="<?= isset($_GET['edit']) ? htmlspecialchars($products[array_search($_GET['edit'], array_column($products, 'id'))]['price']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"><?= isset($_GET['edit']) ? htmlspecialchars($products[array_search($_GET['edit'], array_column($products, 'id'))]['description']) : '' ?></textarea>
                </div>
                
                <div class="action-buttons">
                    <?php if (isset($_GET['edit'])): ?>
                        <button type="submit" name="update" class="btn btn-success">Update Product</button>
                        <a href="index.php" class="btn btn-outline">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn">Add Product</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="card">
            <!-- Products Table -->
            <?php if (count($products) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td class="price">$<?= number_format(htmlspecialchars($product['price']), 2) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td class="actions-cell">
                                <a href="index.php?edit=<?= $product['id'] ?>" class="action-link edit">Edit</a>
                                <a href="index.php?delete=<?= $product['id'] ?>" class="action-link delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i>📦</i>
                    <h3>No Products Found</h3>
                    <p>Add your first product using the form above</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
