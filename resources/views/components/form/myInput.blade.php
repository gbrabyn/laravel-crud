@php
    $helper = new \App\Models\FormFieldHelper($name, $errors, $attributes);
@endphp

{{ Form::input($type, $name, $value, $helper->getAttributes()) }}
@error($helper->getErrorName())
<br><span class="alert alert-danger msgBelow">{{ $message }}</span>
@enderror