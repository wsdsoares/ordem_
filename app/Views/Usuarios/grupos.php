<?php echo $this->extend('Layout/principal') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<link href="<?php echo site_url('recursos\vendor\selectize\selectize.bootstrap4.css'); ?>" rel="stylesheet">
<style>
  /* Estilizando o select para acompanhar a formatação do template */

  .selectize-input,
  .selectize-control.single .selectize-input.input-active {
    background: #2d3035 !important;
  }

  .selectize-dropdown,
  .selectize-input,
  .selectize-input input {
    color: #777;
  }

  .selectize-input {
    /*        height: calc(2.4rem + 2px);*/
    border: 1px solid #444951;
    border-radius: 0;
  }
</style>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-8">

    <div class="user-block block">

      <?php if (empty($gruposDisponiveis)) : ?>

        <?php if ($usuario->full_control == false) : ?>
          <p class="contributions text-info mt-0">Esse usuário já faz parte de todos os grupos disponíveis! </p>
        <?php else : ?>
          <p class="contributions text-white mt-0">Esse usuário já faz parte do grupo ADMINISTRADOR. <br />Para associá-lo a outros grupos, primeiro remova-o do grupo ADMINISTRADOR. </p>
        <?php endif; ?>


      <?php else : ?>

        <!-- Div que exibirá os retornos do backend - via ajax -->
        <div id="response">
        </div><!-- fim div retornos via ajax -->

        <?php echo form_open('/', ['id' => 'form'], ['id' => "$usuario->id"]) ?>

        <div class="form-group">
          <label class="form-control-label">Escolha um ou mais grupos</label>
          <select name="grupo_id[]" multiple class="selectize">
            <option value="">Escolha</option>
            <?php foreach ($gruposDisponiveis as $grupo) : ?>
              <option value="<?php echo $grupo->id; ?>"><?php echo esc($grupo->nome) ?></option>
            <?php endforeach; ?>
          </select>
        </div>



        <div class="form-group mt-5 mb-2">
          <input id="btn-salvar" type="submit" value="Salvar" class="btn btn-danger btn-sm mr-2">
          <a href="<?php echo site_url("usuarios/exibir/$usuario->id"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
        </div>
        <?php echo form_close(); ?>

      <?php endif; ?>


    </div>
  </div>
  <div class="col-lg-8">
    <div class="user-block block">

      <?php if (empty($usuario->grupos)) : ?>

        <p class="contributions text-warning mt-0">Esse usuário não faz parte de nenhum grupo de acesso! </p>
      <?php else : ?>
        <div class="table-responsive">
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>Grupo de acesso</th>
                <th>Descrição</th>
                <th>Excluir</th>
              </tr>
            </thead>
            <tbody>

              <?php foreach ($usuario->grupos as $info) : ?>
                <tr>
                  <td><?php echo esc($info->nome); ?></td>
                  <td><?php echo ellipsize($info->descricao, 32, .5); ?></td>
                  <td>
                    <?php
                    $atributos = [
                      'onSubmit' => "return confirm('Tem certeza da exclusão do grupo de acesso?');",
                    ];
                    ?>
                    <?php echo form_open("usuarios/removegrupo/$info->principal_id", $atributos) ?>
                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>

                    <?php echo form_close(); ?>
                  </td>
                </tr>
              <?php endforeach; ?>

            </tbody>
          </table>
          <div class="mt-3 ml-1">
            <?php echo $usuario->pager->links(); ?>
          </div>
        </div>
      <?php endif; ?>

    </div> <!-- ./ block -->

  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<script type="text/javascript" src="<?php echo site_url('recursos\vendor\selectize\selectize.min.js') ?>"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.selectize').selectize({
      sortField: 'text'
    });

    $("#form").on('submit', function(e) {
      e.preventDefault();

      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('usuarios/salvargrupos'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-salvar").val('Por favor, aguarde...');
        },
        success: function(response) {
          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");

          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            window.location.href = "<?php echo site_url("usuarios/grupos/$usuario->id") ?>";
          }
          if (response.erro) {
            $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');

            if (response.erros_model) {
              $.each(response.erros_model, function(key, value) {
                $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');
              });
            }
          }
        },
        error: function() {
          alert("Não foi possível processar a solicitação. Por favor, entre em contato com o suporte técnico.");
          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");
        }
      });
    });

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    });


  });
</script>
<?php echo $this->endSection() ?>