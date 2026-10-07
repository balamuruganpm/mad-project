<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Megawin QMS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 16px;
            border: none;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .form-control-custom {
            border-radius: 8px;
            padding: 12px 16px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            background-color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .btn-primary-custom {
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            background: linear-gradient(to right, #0d6efd, #0043a8);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>
<script>
    // Clear old localStorage role on logout/login page
    localStorage.removeItem('role_id');
</script>
<div class="login-card">
    <div class="text-center mb-4">
        <div class="mb-3">
            <i class="fa-solid fa-shield-halved fa-3x text-primary"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Megawin QMS</h3>
        <p class="text-muted small text-uppercase fw-semibold tracking-wide">Enterprise Quality Control</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger border-0 border-start border-danger border-4 bg-danger bg-opacity-10 text-danger" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/login" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label text-secondary fw-semibold small text-uppercase">Username / Role ID</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="username" id="username" class="form-control form-control-custom border-start-0" placeholder="e.g., admin or pt_user" required>
            </div>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label text-secondary fw-semibold small text-uppercase">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control form-control-custom border-start-0" placeholder="Enter password" required value="user123">
            </div>
        </div>
        <button type="submit" class="btn btn-primary-custom text-white w-100">Authenticate <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i></button>
    </form>
</div>

</body>
</html>
