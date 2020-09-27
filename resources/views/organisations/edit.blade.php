@php
/** @var $organisation App\Models\Organisation   */

@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    {{ isset($organisation) ? Breadcrumbs::render('organisations.edit', $organisation)
                            : Breadcrumbs::render('organisations.create') }}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1>{{ isset($organisation) ? 'Edit' : 'Add' }} Organisation</h1>
                </div>

                <div class="card-body">
                @if(!empty($organisation))
                {!! Form::model($organisation, [
                                'route'=>['organisations.update', $organisation],
                                'method'=>'put',
                                'id'=>"organisation"
                    ])
                !!}
                @else
                {!! Form::open(['route'=>['organisations.store'], 'method'=>'post', 'id'=>"organisation"]) !!}
                @endif
                    <div class="form-group">
                      <label for="name">Name</label>
                      {!! Form::myInput('text', 'name', null, [
                      'id'=>'name', 'placeholder'=>'Organisation name',
                      'class'=>'form-control', 'required', 'maxlength'=>'255']) !!}
                    </div>

                    <input type="submit" value="Save">
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
