@props(['name', 'class' => 'w-5 h-5'])

@switch($name)
    @case('home')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
             d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        @break
    @case('users')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
             d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20h6M6 12a4 4 0 1 0 8 0 4 4 0 0 0-8 0z"/></svg>
        @break
    @case('cog')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
             d="M11.05 2.928a1 1 0 0 1 1.9 0l.518 1.497a8.001 8.001 0 0 1 3.35 0l.518-1.497a1 1 0 0 1 1.9 0l.519 1.497a8.001 8.001 0 0 1 2.832 2.833l1.496.518a1 1 0 0 1 0 1.9l-1.496.519a8.001 8.001 0 0 1 0 3.35l1.496.518a1 1 0 0 1 0 1.9l-1.496.519a8.001 8.001 0 0 1-2.832 2.832l-.519 1.496a1 1 0 0 1-1.9 0l-.518-1.496a8.001 8.001 0 0 1-3.35 0l-.518 1.496a1 1 0 0 1-1.9 0l-.519-1.496a8.001 8.001 0 0 1-2.832-2.832l-1.497-.519a1 1 0 0 1 0-1.9l1.497-.518a8.001 8.001 0 0 1 0-3.35l-1.497-.518a1 1 0 0 1 0-1.9l1.497-.519a8.001 8.001 0 0 1 2.832-2.833l.519-1.497zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
        @break
@endswitch
