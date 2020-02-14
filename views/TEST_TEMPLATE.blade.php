
<h1>Test Case 1 - Encapsuled If-Else</h1>
@if($randomElement < 4)
    {{$randomElement}}
@endif

@if($randomElement < 5)
    {{$randomElement}}
@endif


<h1>Synonym</h1>

@synonym(["test1","test2","test3" ,"test0"])


<h1>Display Data</h1>

{{$randomElement}}
