<fieldset class="so-icons">
    <legend>Choose an icon</legend>
    <div class="so-icons__grid">
        @foreach($icons as $key => $label)
            <label class="so-icon-choice">
                <input type="radio" name="icon_key" value="{{ $key }}" @checked($selectedIcon === $key) required>
                <span><x-work-icon :type="$key" /><small>{{ $label }}</small></span>
            </label>
        @endforeach
    </div>
</fieldset>
