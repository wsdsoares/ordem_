<?php echo $this->extend('Layout/principal') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-3">
    <div class="user-block block">

      <h5 class="card-title mt-2"><?php echo esc($grupo->nome); ?></h5>
      <p class="contributions mt-0"><?php echo $grupo->exibeSituacao(); ?>

        <?php if ($grupo->deletado_em == null) : ?>

          <a tabindex="0" style="text-decoration:none;" class="" role="button" data-toggle="popover" data-trigger="focus" title='<center><span class="text-info">Importante</span></center>' data-content="Esse grupo 
            <?php echo ($grupo->exibir == true ? '' : 'não'); ?> será exibido como opção na hora de definir um <b>Responsável Técnico</b> pela ordem de serviço">
            &nbsp;&nbsp;<i class="fa fa-question-circle fa-lg text-info"></i>
          </a>

        <?php endif; ?>

      </p>

      <p class="card-text"><?php echo esc($grupo->descricao); ?></p>
      <p class="card-text">Criado <?php echo $grupo->criado_em->humanize(); ?></p>
      <p class="card-text">Atualizado <?php echo $grupo->atualizado_em->humanize(); ?></p>

      <!-- Example single danger button -->
      <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Ações
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?php echo site_url("grupos/editar/$grupo->id"); ?>">Editar grupo de acesso</a>

          <div class="dropdown-divider"></div>
          <?php if ($grupo->deletado_em == null) : ?>
            <a class="dropdown-item" href="<?php echo site_url("grupos/excluir/$grupo->id"); ?>">Excluir grupo de acesso</a>
          <?php else : ?>
            <a class=" dropdown-item" href="<?php echo site_url("grupos/desfazerexclusao/$grupo->id"); ?>">Restaurar grupo de acesso</a>
          <?php endif; ?>
        </div>
      </div>
      <a href=" <?php echo site_url("usuarios"); ?>" class="btn btn-secondary ml-2">Voltar</a>
    </div> <!-- ./ block -->

  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<!-- Aqui coloco os scripts da view -->
<?php echo $this->endSection() ?>