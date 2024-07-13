<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/minty/bootstrap.min.css" />
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('products.index') }}">products list.</a>
            <div class="collapse navbar-collapse">
                <a href="{{ route('products.create') }}" class="btn btn-primary ms-auto">add product</a>
            </div>
        </div>
    </nav>

    <div id="app">
        @yield('content')
    </div>
</body>

</html>
