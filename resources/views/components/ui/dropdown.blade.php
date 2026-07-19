@props(['align' => 'right', 'width' => '48'])

@php
$alignmentClasses = match ($align) {
    'left' => 'origin-top-left',
    'top' => 'origin-bottom',
    'right' => 'origin-top-right',
    default => 'origin-top-right',
};

$width = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    default => $width,
};
@endphp

<div class="relative inline-block text-left" 
     x-data="{ 
        open: false,
        setPosition() {
            let trigger = this.$refs.trigger.getBoundingClientRect();
            let align = '{{ $align }}';
            
            this.$refs.menu.style.position = 'fixed';
            
            if (align === 'top') {
                this.$refs.menu.style.bottom = (window.innerHeight - trigger.top + 8) + 'px';
                this.$refs.menu.style.top = 'auto';
            } else {
                this.$refs.menu.style.top = (trigger.bottom + 4) + 'px';
                this.$refs.menu.style.bottom = 'auto';
            }
            
            if (align === 'right') {
                this.$refs.menu.style.right = (window.innerWidth - trigger.right) + 'px';
                this.$refs.menu.style.left = 'auto';
            } else {
                this.$refs.menu.style.left = trigger.left + 'px';
                this.$refs.menu.style.right = 'auto';
            }
        }
     }" 
     @click.outside="open = false" 
     @scroll.window="open = false"
     @resize.window="open = false">
    <div @click="open = ! open; if(open) $nextTick(() => setPosition())" x-ref="trigger">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-ref="menu"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="z-50 {{ $width }} rounded-md shadow-md {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md border bg-popover text-popover-foreground shadow-md p-1">
            {{ $content }}
        </div>
    </div>
</div>
