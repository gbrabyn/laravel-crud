@extends('layouts.app')

@section('content')
<div class="container">
    {{ isset($user) ? Breadcrumbs::render('users.edit', $user) : Breadcrumbs::render('users.create') }}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1>{{ isset($user) ? 'Edit' : 'Add' }} User</h1>
                </div>
                @if ($errors->any())
                    <p class="alert alert-danger">Please fix the errors in the form below.</p>
                @endif
                <div class="card-body">
                @if(!empty($user))
                {!! Form::model($user, [
                                'route'=>['users.update', $user], 
                                'method'=>'put', 
                                'id'=>"user"
                    ]) 
                !!}
                @else
                {!! Form::open(['route'=>['users.store'], 'method'=>'post', 'id'=>"user"]) !!}
                @endif
                    <div class="form-group">
                      <label for="name">Name</label>
                      {!! Form::myInput('text', 'name', null, [
                      'id'=>'name', 'placeholder'=>'Name', 'class'=>'form-control', 'required', 'maxlength'=>'255']) !!}  
                    </div>
                    <div class="form-group">
                      <label for="email">Email</label>
                      {!! Form::myInput('email', 'email', null, [
                      'id'=>'email', 'placeholder'=>'Email address', 
                      'class'=>'form-control', 'required', 'maxlength'=>'255']) !!}  
                    </div>
                    <div class="form-group">
                      <label>Type</label>
                      {!! Form::myRadioList('type', $types, [
                      'id'=>'type', 'class'=>'list-group']) !!}  
                    </div>
                    <div class="form-group">
                      <label for="organisation">Organisation</label>
                      {!! Form::mySelect('organisation_id', $organisations, null, [
                      'id'=>'organisation', 'class'=>'form-control', 'placeholder'=>'Organisation...']) !!}  
                    </div>
                    <input type="submit" value="Save">
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection