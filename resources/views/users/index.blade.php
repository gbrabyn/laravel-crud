@php
    /** @var $users Illuminate\Database\Eloquent\Collection */
    /** @var $user App\Models\User   */
    
    Form::considerRequest(true);
@endphp

@push('scripts')
<script src="{{ mix('/build/users/index.js') }}" defer></script>
@endpush

@extends('layouts.app')

@section('content')

<div class="container">
    {{ Breadcrumbs::render('users') }}
@can('viewDeleteLinks', App\Models\User::class)
    <form method="POST" id="deleteUser">
        <input name="_method" type="hidden" value="DELETE">
        @csrf()
    </form>
@endcan
    {!! Form::open(['route'=>['users'], 'method'=>'get', 'id'=>'searchUsers', 'class'=>'form-inline']) !!}
        <div class="form-group mr-2">
            <label class="sr-only" for="organisation">Organisation</label>
            {{ Form::mySelect('organisation', $organisations, null, ['id'=>'organisation', 'class'=>'form-control', 'placeholder'=>'any organisation']) }}
        </div>
        <div class="form-group mr-2">
            <label class="sr-only" for="type">Type</label>
            {{ Form::mySelect('type', $types, null, ['id'=>'type', 'class'=>'form-control', 'placeholder'=>'any type']) }}
        </div>
        <div class="form-group mr-2">        
            <label class="sr-only" for="nameOrEmail">Name or Email</label>
            {!! Form::myInput('text', 'nameOrEmail', null, ['id'=>'nameOrEmail', 'placeholder'=>'name or email address...']) !!}  
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    {!! Form::close() !!}
    <br>
@can('create', App\Models\User::class)
    <p><a href="{{ route('users.create') }}">Create User</a></p>
@endcan
    <div class="table-responsive">
        <table class="table">
            <caption>Total found: {{ number_format($users->total()) }}</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Organisation</th>
                    <th>Type</th>
                @can('viewDeleteLinks', App\Models\User::class)
                    <th></th>
                @endcan 
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}">
                            {{ $user->id }}
                        </a>
                    @else
                        {{ $user->id }}
                    @endcan
                    </td>
                    <td>
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}">
                            {{ $user->name }}
                        </a>
                    @else
                        {{ $user->name }}
                    @endcan
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ optional($user->organisation)->name }}</td>
                    <td>{{ $user->type }}</td>

                @can('viewDeleteLinks', App\Models\User::class)
                    <td>
                    @can('delete', $user)
                        <button type="button" 
                                class="deleteBtn" 
                                data-url="{{ route('users.delete', $user) }}"
                                data-confirm-message="Confirm you wish to delete {{ $user->email }}"
                                >
                            Delete
                        </button>
                    @endcan
                    </td>
                @endcan 
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->withQueryString()->links() }}
</div>
@endsection