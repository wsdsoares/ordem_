<?php echo $this->extend('Layout/principal') ?>
<?php echo $this->section('titulo') ?><?php echo $titulo; ?><?php echo $this->endSection() ?>

<?php echo $this->section('estilos') ?>
<!-- Aqui coloco os estilos da view -->
<link href="https://cdn.datatables.net/v/bs4/dt-1.13.6/r-2.5.0/datatables.min.css" rel="stylesheet">
<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<!-- Aqui coloco o conteudo a view -->
<div class="row">

  <div class="col-lg-12">
    <div class="block">
      <div class="table-responsive">
        <table id="ajaxTable" class="table table-striped table-sm" style="width: 100%;">
          <thead>
            <tr>
              <th>imagem</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Situação</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>


<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<!-- Aqui coloco os scripts da view -->
<script src="https://cdn.datatables.net/v/bs4/dt-1.13.6/r-2.5.0/datatables.min.js"></script>
<script>
  const DATATABLE_PTBR = {
    "sEmptyTable": "Nenhum registro encontrado",
    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
    "sInfoPostFix": "",
    "sInfoThousands": ".",
    "sLengthMenu": "_MENU_ resultados por página",
    "sLoadingRecords": "Carregando...",
    "sProcessing": "Processando...",
    "sZeroRecords": "Nenhum registro encontrado",
    "sSearch": "Pesquisar",
    "oPaginate": {
      "sNext": "Próximo",
      "sPrevious": "Anterior",
      "sFirst": "Primeiro",
      "sLast": "Último"
    },
    "oAria": {
      "sSortAscending": ": Ordenar colunas de forma ascendente",
      "sSortDescending": ": Ordenar colunas de forma descendente"
    },
    "select": {
      "rows": {
        "_": "Selecionado %d linhas",
        "0": "Nenhuma linha selecionada",
        "1": "Selecionado 1 linha"
      }
    }
  }
  new DataTable('#ajaxTable', {

    oLanguage: DATATABLE_PTBR,
    ajax: "<?php echo site_url('usuarios/recuperaUsuarios'); ?>",
    columns: [{
        data: 'imagem'
      },
      {
        data: 'nome'
      },
      {
        data: 'email'
      },
      {
        data: 'ativo'
      },
    ],
    deferRender: true,
    processing: true,
    language: {
      processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
    },
    responsive: true,
    pagingType: $(window).width() < 768 ? "simple" : "simple_numbers"
  });
</script>
<?php echo $this->endSection() ?>