<?php echo $this->extend('Layout/Autenticacao/principal_autenticacao') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<!-- Aqui coloco o conteudo a view -->

<div class="row">
  <!-- Logo & Information Panel-->
  <div class="col-lg-6">
    <div class="info d-flex justify-content-center">
      <div class="content">
        <div class="logo text-center">

          <img src="<?php echo site_url(); ?>recursos/img/logo/logo_pref2.jpg" alt="">
        </div>
        <div class="mt-5 text-center">
          <h1>Prefeitura Municipal de Itamarandiba</h1>
          <p class="pt-5">Guarda Municipal</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Form Panel    -->
  <div class="col-lg-6 bg-white">
    <div class="form d-flex align-items-center">
      <div class="content">
        <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate']); ?>
        <div id="response"></div>
        <div class="container-fluid">
          <!-- Espaço reservado para renderizar o conteudo de cada view e extender esse layout -->

        </div>

        <div class="form-group">

          <label for="login-username" class="label-material">Seu e-email de acesso</label>
        </div>
        <div class="form-group">
          <input id="login-password" type="password" name="password" required data-msg="Por favor, informe a sua senha" class="input-material">
          <label for="login-password" class="label-material">Senha</label>
        </div>
        <input id="btn-login" type="submit" class="btn btn-primary " value="Entrar" />
        <?php echo form_close(); ?>
        <a href="<?php echo site_url('esqueci'); ?>" class="forgot-pass mt-3">Esqueceu a sua senha?</a>
      </div>
    </div>
  </div>
</div>

<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<!-- Aqui coloco os scripts da view -->
<!-- Aqui coloco os scripts da view -->
<script>
  $(document).ready(function() {
    $("#form").on('submit', function(e) {
      e.preventDefault();

      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('login/criar'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-login").val('Por favor, aguarde...');
        },
        success: function(response) {
          $("#btn-login").val('Entrar');
          $("#btn-login").removeAttr("disabled");

          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            //tudo certo com a atualização do usuário
            window.location.href = "<?php echo site_url() ?>" + response.redirect;
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
          $("#btn-login").val('Entrar');
          $("#btn-login").removeAttr("disabled");
        }
      });
    });

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    });
  });
</script>
<?php echo $this->endSection() ?>