<html>
    <body onload="javascript:document.redirectForm.submit();">
        <form action="{{config($config_path.'.url')}}" method="post" name="redirectForm">
            @foreach($parameters as $k=>$v)
                <input type="hidden" value="{{$v}}" name="{{$k}}" />
            @endforeach
        </form>
    </body>
</html>
