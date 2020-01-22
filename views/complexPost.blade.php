
<h1>Test Case 1 - Encapsuled If-Else</h1>
@if ($randomElement < 4)
    {{$randomElement}}
@else
    {{$randomElement}}
    @if ($randomElement < 10)
        {{$randomElement}}
        @if ($randomElement < 3)
            {{$randomElement}}
        @else
            {{$randomElement}}
        @endif
    @else
        {{$randomElement}}
    @endif
@endif

<h1>Test Case 2 - If in Directive</h1>

<h2>Normal Case</h2>
@randomValFromArr([$name, $company, $text])

<h2>IF-Case</h2>
@randomValFromArr([
    $randomElement < 10 ? "Hi" : "Goodbye",
    $randomElement < 10 ? "Hi" : "Goodbye" , $text
])

<h2>IF-Case Complex</h2>
@randomValFromArr([
($randomElement < 10 ? "Hi" : ($randomElement < 10 ? ($randomElement < 10 ? "Hi" : ($randomElement < 10 ? "Hi" : ($randomElement < 10 ? "Hi" : "Goodbye"))) : "Goodbye")),
($randomElement < 10 ? ($randomElement < 10 ? ($randomElement < 10 ? "Hi" : "Goodbye") : "Goodbye") : "Goodbye")
])