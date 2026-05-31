<button {{ $attributes->merge([
'class'=>'bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-800 cursor-pointer' ]) }}
>
    {{$slot}}
</button>