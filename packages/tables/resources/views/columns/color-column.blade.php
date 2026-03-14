@php
    $state = $getState();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class(['filament-tables-color-column relative ml-4 flex h-6 w-6 rounded-md'])
    }}
    @if ($state)
        style="background-color: {{ $state }}"
        @if ($isCopyable())
            x-on:click="(async () => {
                try {
                    await window.navigator.clipboard.writeText(@js($state))
                    $tooltip(@js($getCopyMessage()), { timeout: @js($getCopyMessageDuration()) })
                } catch (error) {
                    console.error('Failed to copy color:', error)
                    $tooltip('Failed to copy', { timeout: 2000 })
                }
            })()"
        @endif
    @endif
>
</div>
