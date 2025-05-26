<?php
// admin/includes/admin_header.php
require_once __DIR__ . '/check_auth.php'; // Vérifie l'authentification
require_once __DIR__ . '/../../includes/db.php'; // Connexion à la BDD (chemin relatif)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Espace Événementiel</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <style>
        /* Styles spécifiques pour l'admin, peut être dans un fichier admin.css dédié plus tard */
        body { background-color: #f4f7f6; }
        .admin-header {
            background-color: #2c3e50; /* Couleur foncée pour l'admin */
            padding: 15px 0;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            color: white;
            margin: 0;
            font-size: 1.8em;
        }
        .admin-nav ul {
            list-style: none;
            display: flex;
        }
        .admin-nav ul li {
            margin-left: 25px;
        }
        .admin-nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .admin-nav ul li a:hover {
            color: var(--accent-color);
        }
        .admin-main {
            padding-top: 30px;
            padding-bottom: 50px;
        }
        .admin-section {
            background-color: white;
            padding: 30px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .admin-form .form-group {
            margin-bottom: 20px;
        }
        .admin-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .admin-form input[type="text"],
        .admin-form input[type="email"],
        .admin-form input[type="password"],
        .admin-form input[type="number"],
        .admin-form input[type="file"],
        .admin-form textarea,
        .admin-form select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .admin-form textarea {
            resize: vertical;
            min-height: 100px;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .admin-table th, .admin-table td {
            border: 1px solid #eee;
            padding: 12px;
            text-align: left;
        }
        .admin-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .admin-table tr:nth-child(even) {
            background-color: #f6f6f6;
        }
        .admin-actions a {
            margin-right: 10px;
            text-decoration: none;
            color: var(--primary-color);
        }
        .admin-actions a:hover {
            text-decoration: underline;
        }
        .btn-delete {
            background-color: var(--error-red);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>Administration</h1>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php">Tableau de bord</a></li>
                    <li><a href="gerer_espaces.php">Gérer les Espaces</a></li>
                    <li><a href="gerer_categories.php">Gérer les Catégories</a></li>
                    <li><a href="../index.php">Retour au site</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="admin-main">
        <div class="container">