<?php echo $this->extend('Layout/principal') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<!-- Aqui coloco o conteudo a view -->
<h1>Extendendo o layout principal através da view, index de home</h1>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<!-- Aqui coloco os scripts da view -->
<?php echo $this->endSection() ?>