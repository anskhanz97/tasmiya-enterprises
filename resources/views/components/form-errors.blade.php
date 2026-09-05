{{-- Form Errors Component --}}
@props(['field' => null])

@if($errors->any())
    @if($field)
        @if($errors->has($field))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong>{{ ucfirst(str_replace('_', ' ', $field)) }} Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->get($field) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @else
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endif
