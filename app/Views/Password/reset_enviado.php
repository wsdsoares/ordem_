<?php echo $this->extend('Layout/Autenticacao/principal_autenticacao') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<!-- Aqui coloco o conteudo a view -->

<div class="row">
  <!-- Logo & Information Panel-->
  <div class="col-lg-8 mx-auto">
    <div class="info d-flex justify-content-center">
      <div class="content">
        <div class="logo text-center">

          <img src="<?php echo site_url(); ?>recursos/img/logo/logo_pref2.jpg" alt="">
        </div>
        <div class="mt-5 text-center">
          <h1><?php echo $titulo; ?></h1>
          <p class="">Não deixe de conferir a caixa de span!</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Form Panel    -->
  <div class="col-lg-6 bg-white d-none">
    <div class="form d-flex align-items-center">
      <div class="content">

      </div>
    </div>
  </div>
</div>

<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<?php echo $this->endSection() ?>