<?php echo $this->extend('Layout/Autenticacao/principal_autenticacao') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<!-- Aqui coloco o conteudo a view -->

<div class="row h-100">
  <div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
    <div class="login-form">
      <div class="text-center">
        <!-- <img src="<?= site_url('recursos') ?>/images/logo/logo_pref2.jpg" alt="Prefeitura"> -->
        <img class="mb-2" src="<?= site_url('recursos') ?>/images/logo/logo_prefeitura.png" alt="Prefeitura">
        <h3 class="title">Tela de Login</h3>
        <!-- <p>Acesse sua conta para começar a utilizar o sistema</p> -->
      </div>
      <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate']); ?>
      <div id="response"></div>
      <form action="index.html">

        <div class="mb-4">
          <label class="mb-1 text-dark">Email</label>
          <!-- <input id="login-username" type="text" name="email" required data-msg="Por favor, informe seu email." class="input-material"> -->
          <input id="login-username" type="text" name="email" required placeholder="Informe seu email." class="form-control">
        </div>
        <div class="mb-4 position-relative">
          <label class="mb-1 text-dark">Senha</label>
          <!-- <input id="login-username" type="email" required placeholder="Por favor, informe seu email." class="form-control form-control"> -->
          <input id="login-password" type="password" name="password" required placeholder="Informe a sua senha." class="form-control">

          <!-- <input type="password" id="dz-password" class="form-control" value="123456"> -->
          <span class="show-pass eye">

            <i class="fa fa-eye-slash"></i>
            <i class="fa fa-eye"></i>

          </span>
        </div>
        <div class="form-row d-flex justify-content-between mt-4 mb-2">
          <!-- <div class="mb-4">
            <div class="form-check custom-checkbox mb-3">
              <input type="checkbox" class="form-check-input" id="customCheckBox1" required="">
              <label class="form-check-label" for="customCheckBox1">Remember my preference</label>
            </div>
          </div> -->
          <div class="mb-4">
            <a href="<?php echo site_url('esqueci'); ?>" class="btn-link text-primary">Esqueceu sua senha?</a>
          </div>
        </div>
        <div class="text-center mb-4">
          <input type="submit" id="btn-login" class="btn btn-primary btn-block" value="Entrar" />
        </div>
        <!-- <h6 class="login-title"><span>Or continue with</span></h6>

        <div class="mb-3">
          <ul class="d-flex align-self-center justify-content-center">
            <li><a target="_blank" href="https://www.facebook.com/" class="fab fa-facebook-f btn-facebook"></a></li>
            <li><a target="_blank" href="https://www.google.com/" class="fab fa-google-plus-g btn-google-plus mx-2"></a></li>
            <li><a target="_blank" href="https://www.linkedin.com/" class="fab fa-linkedin-in btn-linkedin me-2"></a></li>
            <li><a target="_blank" href="https://twitter.com/" class="fab fa-twitter btn-twitter"></a></li>
          </ul>
        </div> -->
        <p class="text-center">Not registered?
          <a class="btn-link text-primary" href="page-register.html">Register</a>
        </p>
        <?php echo form_close(); ?>
    </div>
  </div>
  <div class="col-xl-6 col-lg-6">
    <div class="pages-left h-100">
      <div class="login-content">
        <a href="index.html"><img src="<?= site_url('recursos') ?>/images/logo-full.png" class="mb-3 logo-dark" alt=""></a>
        <a href="index.html"><img src="<?= site_url('recursos') ?>/images/logi-white.png" class="mb-3 logo-light" alt=""></a>

        <p>CRM dashboard uses line charts to visualize customer-related metrics and trends over time.</p>
      </div>
      <div class="login-media text-center">
        <img src="<?= site_url('recursos') ?>/images/login.png" alt="">
      </div>
    </div>
  </div>
</div>

<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
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