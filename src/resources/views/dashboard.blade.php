<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>{{ config('ci.names.dashboard') }}</title>
	</head>

    <script>
        window.laravel = JSON.parse('{!! $laravel !!}');
    </script>

    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <style>
        {!! file_get_contents(CI_PATH.'/src/public/css/app.css'); !!}
    </style>

	<body>
        <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse fixed-top">
            <a class="navbar-brand" href="#">{{ config('ci.names.dashboard') }}</a>
        </nav>

        <div id="app">
            <div class="container-fluid">
                <div class="container-fluid">
                    <div class="row row-offcanvas row-offcanvas-right">
                        <div class="col-6 col-md-3 sidebar-offcanvas" id="sidebar">


                            <projects></projects>
                        </div><!--/span-->

                        <div class="col-12 col-sm-9">
                            <div class="table-container">
                                <div class="table-responsive" id="table-container">
                                    <tests>
                                    </tests>
                                </div>
                            </div>
                        </div><!--/span-->
                    </div><!--/row-->
                </div><!--/.container-->
            </div>
        </div>

        <script>
            {!! file_get_contents(CI_PATH.'/src/public/js/app.js'); !!}
        </script>

        @if(config('app.env') == 'local')
            <script src="http://localhost:35729/livereload.js"></script>
        @endif
    </body>
</html>
