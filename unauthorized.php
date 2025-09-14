<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Unauthorized Access</h4>
                    <p>You don't have permission to access this page.</p>
                    <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
                    <a href="logout.php" class="btn btn-primary">Login as Different User</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>