# Theme pagination and motion

Use `<x-theme-pagination>` for the application's standard pagination: circular previous/next arrows, violet active pill and muted dots. No visible page-number text.

For JavaScript carousels, connect the component with `TasmiyaPager(element, {count, onChange})`. `count` may be a number or function for responsive sliders. The returned `set(index, notify)` synchronizes swipe/scroll position; `current()` returns the selected page.

For simple card grids, add `data-theme-page-size="6"` to the section, `data-page-item` to its cards and a hidden pagination component inside the section. Controls appear only when more than one page exists.

Use `data-theme-counter="NUMBER"` on a number with its real server-rendered value as fallback. It counts up once when entering the viewport. Use `theme-glass` on a CTA for the moving translucent pane. Both effects respect reduced-motion preferences.
