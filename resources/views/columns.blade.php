<!DOCTYPE html>
<html>
    <head>
        <title>Importador SVLabs - Colunas não cadastradas</title>
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
        <!-- Select2 -->
        {{ Html::style('library/js/select2-4.0.3/dist/css/select2.min.css') }}
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
                    <a class="navbar-brand" href="/">Importador SVLabs</a>
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
            <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fa fa-table"></i>
                    <h3 class="box-title">Importador de Planilhas</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    {{ Form::open(array('url' => '/columns/put', 'files' => true)) }}
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Coluna</th>
                                    <th>Tipo de dado</th>
                                    <th>Pertence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($columns as $column)
                                <tr>
                                    <td>{{ $column->column }}</td>
                                    <td>{{ $column->value }}</td>
                                    <td>{{ Form::select('columns[name][' . $column->column . ']', $fields, array('class' => 'form-control')) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary pull-right">Enviar</button>
                    {{ Form::close() }}
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="pull-right">
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container -->

        <div class="footer">
            <p class="pull-left">Copyright &copy; 2016 - SVLabs</p>
            <p class="pull-right">Todos os direitos reservados</p>
        </div>

        <!-- Latest compiled and minified JavaScript -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        <!-- Pace -->
        {{ Html::script('library/js/pace.min.js') }}
        <!-- Select2 -->
        {{ Html::script('library/js/select2-4.0.3/dist/js/select2.min.js') }}

        <script type="text/javascript" charset="utf-8">
            $('select').select2();
        </script>
    </body>
</html>