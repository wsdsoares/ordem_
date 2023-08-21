<?php echo $this->extend('Layout/principal') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-4">
    <div class="block">
      <div class="text-center">
        <?php if ($usuario->imagem == null) : ?>
          <img src="<?php echo site_url('recursos/img/usuario_sem_imagem.png') ?>" alt="Usuário sem imagem" class="card-img-top" style="width: 90%;">
        <?php else : ?>
          <img src="<?php echo site_url("usuarios/imagem/$usuario->imagem") ?>" alt="<?php echo esc($usuario->nome); ?>" class="card-img-top" style="width: 90%;">
        <?php endif; ?>
        <a href="<?php site_url("usuarios/editarimagem/$usuario->id") ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
      </div>
      <hr class="border-secondary">

      <h5 class="card-title mt-2"><?php echo esc($usuario->nome); ?></h5>
      <p class="card-text"><?php echo esc($usuario->email); ?></p>
      <p class="card-text">Criado <?php echo $usuario->criado_em->humanize(); ?></p>
      <p class="card-text">Atualizado <?php echo $usuario->atualizado_em->humanize(); ?></p>

      <!-- Example single danger button -->
      <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Ações
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?php echo site_url("usuarios/editar/$usuario->id"); ?>">Editar Usuário</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Separated link</a>
        </div>
      </div>
      <a href="<?php echo site_url("usuarios"); ?>" class="btn btn-secondary ml-2">Voltar</a>
    </div> <!-- ./ block -->

  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<!-- Aqui coloco os scripts da view -->
<?php echo $this->endSection() ?>