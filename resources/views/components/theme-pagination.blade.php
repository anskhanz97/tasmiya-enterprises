@props(['label' => 'Pages', 'hidden' => false])
<nav {{ $attributes->class(['theme-pager']) }} aria-label="{{ $label }}" @if($hidden) hidden @endif>
    <button type="button" data-page-prev aria-label="Previous page">‹</button>
    <span data-page-dots></span>
    <button type="button" data-page-next aria-label="Next page">›</button>
</nav>
