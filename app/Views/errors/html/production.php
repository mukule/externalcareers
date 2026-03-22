<?php $back = isset($_SERVER['HTTP_REFERER']) ? esc($_SERVER['HTTP_REFERER']) : '/'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= lang('Errors.whoops') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f2f5;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #555;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .divider {
            width: 50px;
            height: 4px;
            background: #e67e22;
            border-radius: 2px;
            margin: 1.2rem auto;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 0.5rem;
        }

        p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #777;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.65rem 1.8rem;
            background: #e67e22;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn:hover { background: #ca6f1e; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>

        <h1><?= lang('Errors.whoops') ?></h1>
        <div class="divider"></div>

        <p><?= lang('Errors.weHitASnag') ?></p>

        <a href="<?= $back ?>" class="btn">← Go Back</a>
    </div>
</body>
</html>