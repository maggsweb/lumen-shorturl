@extends('default')

@section('content')

    <div class="container">



        This form is included purely to create the cURL request code...

        <form action="/test" method="post">
            <input type="url" name="long_url" id="long_url" value="{{$long_url}}" required />
            <button>Shorten</button>
        </form>


        <div class="pre" style="width: 500px">
            <pre>
{{ $create }}
            </pre>
        </div>


        <hr>


        <form action="/user" method="post">
            <button>List User</button>
        </form>

    </div>

@endsection
