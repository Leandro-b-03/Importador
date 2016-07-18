<!DOCTYPE html>
<html>
    <head>
        <title>Importador SVLabs</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Custom Css -->
        {{ Html::style('library/css/custom-css.css') }}
        <!-- Pace -->
        {{ Html::style('library/css/corner_indicator.css') }}
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Importador <strong>SVLabs</strong></a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="columns">Colunas não cadastradas</a>
                        </li>
                        <li>
                            <a href="log-viewer" target="_blank">Ver Logs</a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container -->
        </nav>

        <!-- Page Content -->
        <div class="container">
            <div class="box box-solid box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-upload"></i>
                    <h3 class="box-title">Importador de Planilhas</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    {{ Form::open(array('url' => '/', 'files' => true)) }}
                        <div class="col-md-6 form-group">
                            {{ Form::label('file', 'Arquivo para importação', array('class' => 'cols-sm-2 control-label')) }}
                            <div class="cols-sm-10">
                                <div class="input-group">
                                    {{ Form::file("file", $attributes = array("class" => "form-control", )) }}
                                    <span class="input-group-addon"><i class="fa fa-file-excel-o" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pull-right">
                            <button class="btn btn-primary pull-right">Enviar</button>
                        </div>
                    {{ Form::close() }}
                </div>
            <!-- /.box-body -->
            </div>
            <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fa fa-table"></i>
                    <h3 class="box-title">Importador de Planilhas</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nome do Arquivo</th>
                                <th>Id do projeto</th>
                                <th>Importado corretamente</th>
                                <th>Data de importação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr>
                                <td><a href="file/{{ $file->id }}">{{ $file->name }}</a></td>
                                <td>{{ $file->project_id }}</td>
                                <td>{{ ($file->full_read ? 'Sim' : 'Não') }}</td>
                                <td>{{ with($file->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td><a id="{{ $file->id }}" data-token="{{ csrf_token() }}" class="btn btn-xs btn-danger delete-file">Deletar</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="pull-right">
                        {{ $files->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container -->

        <div class="footer">
            <p class="pull-left">Copyright &copy; {{ \Carbon\Carbon::now()->year }} - <strong>SVLabs</strong></p>
            <p class="pull-right">Todos os direitos reservados</p>
        </div>

        <!-- Latest compiled and minified JavaScript -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        <!-- Pace -->
        {{ Html::script('library/js/pace.min.js') }}
        <script type="text/javascript">
            $('form').submit(function() {
                $(this).find('button').prop('disabled',true);
            });

            $('.delete-file').click(function() {
                var token = $(this).data('token');
                $.ajax({
                    url: 'file/' + $(this).attr('id'),
                    type: 'post',
                    data: {_method: 'delete', _token :token},
                    success: function() {
                        location.reload();
                    }
                });
            });
        </script>
    </body>
</html>