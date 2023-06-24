<!DOCTYPE html>
<html>
<head>
    <title>Sign In</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <?php
    require "bootstrap.php";
    ?>
</head>
<body>

    <div class="container">
        <h1>Sign In</h1>
        <form id="signin-form" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="param">Parameter:</label>
                <input type="text" id="param" name="param" required>
            </div>
        <button type="submit">Sign In</button>
        </form>
    </div>
    <!--<script src="script.js"></script> -->
</body>
</html>
