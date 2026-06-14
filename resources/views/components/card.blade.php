@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200 ' . $class]) }}>
    {{ $slot }}
</div>