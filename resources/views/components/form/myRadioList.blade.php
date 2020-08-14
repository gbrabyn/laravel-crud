@php
    $helper = new \App\Models\FormFieldHelper($name, $errors, $attributes);
@endphp
<ul {!! $helper->getAttributesString() !!}>
@foreach ($options as $v => $label)
    <li class="list-group-item">
        <label>
            {!! Form::radio($name, $v) !!}
            {{ $label }}
        </label>
    </li>
@endforeach
</ul>
@error($helper->getErrorName())
<br><span class="alert alert-danger msgBelow">{{ $message }}</span>
@enderror