<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="keywords" content="">
  <meta name="author" content="">
  <meta name="robots" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- @todo Validar depois -->
  <meta name="description" content="">
  <meta property="og:title" content="">
  <meta property="og:description" content="">
  <meta property="og:image" content="">
  <meta name="format-detection" content="telephone=no">

  <!-- PAGE TITLE HERE -->
  <title>Ordem de Serviço | <?php echo $this->renderSection('titulo') ?></title>

  <!-- FAVICONS ICON -->
  <link rel="shortcut icon" type="image/png" href="<?= site_url('recursos') ?>/images/favicon.png">
  <link href="<?= site_url('recursos') ?>/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
  <link href="<?= site_url('recursos') ?>/css/style.css" rel="stylesheet">

</head>

<body class="vh-100">
  <div class="authincation h-100">
    <div class="container-fluid h-100">

      <!-- Espaço reservado para renderizar o conteudo de cada view e extender esse layout -->
      <?php echo $this->include('Layout/_mensagens'); ?>

      <?php echo $this->renderSection('conteudo'); ?>

    </div>
  </div>

  <!--**********************************
	Scripts
***********************************-->
  <!-- Required vendors -->
  <script src="<?= site_url('recursos') ?>/vendor/global/global.min.js"></script>
  <script src="<?= site_url('recursos') ?>/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
  <script src="<?= site_url('recursos') ?>/js/deznav-init.js"></script>

  <script src="<?= site_url('recursos') ?>/js/custom.js"></script>
  <!-- <script src="<?= site_url('recursos') ?>/js/styleSwitcher.js"></script> -->

  <!-- Espaço reservado para renderizar os scripts de cada view e extender esse layout -->
  <?php echo $this->renderSection('scripts'); ?>

</body>

</html>