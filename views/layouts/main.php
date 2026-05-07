<?php /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Vite &amp; Gourmand</title>
    <meta name="description" content="<?= isset($pageDescription) ? e($pageDescription) : 'Vite & Gourmand, traiteur événementiel à Bordeaux. Découvrez nos menus et commandez en ligne.' ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>

<a href="#contenu-principal" class="skip-link">Aller au contenu principal</a>

<?php require __DIR__ . '/../partials/navbar.php'; ?>

<main id="contenu-principal">
    <?php if ($msg = flash('success')): ?>
        <div class="container"><div class="alert alert-success" role="alert"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="container"><div class="alert alert-error" role="alert"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?php if ($msg = flash('info')): ?>
        <div class="container"><div class="alert alert-info" role="alert"><?= e($msg) ?></div></div>
    <?php endif; ?>

    <?= $content ?? '' ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<script src="<?= asset('js/app.js') ?>"></script>
<?php if (!empty($pageScripts)): foreach ($pageScripts as $s): ?>
    <script src="<?= asset($s) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
