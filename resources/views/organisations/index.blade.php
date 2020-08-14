@php
    /** @var $orgs Illuminate\Database\Eloquent\Collection */
    /** @var $org App\Models\Organisation   */
    
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    {{ Breadcrumbs::render('organisations') }}
    <h1>Organisations</h1>
    <p>
        <a href="{{ route('organisations.create') }}">Create Organisation</a>
    </p>
    <form method="POST" id="deleteOrganisation" action="{{ route('organisations.delete') }}">
        <input name="_method" type="hidden" value="DELETE">
        @csrf()
    </form>
    <div class="table-responsive">
        <table class="table">
            <caption>Total entries: {{ number_format($orgs->count()) }}</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($orgs as $org)
                <tr>
                    <td>{{ $org->id }}</td>
                    <td>
                        <a href="{{ route('organisations.edit', $org) }}">
                            {{ $org->name }}
                        </a>
                    </td>
                    <td>
                        <button type="submit" 
                                form="deleteOrganisation"
                                name="id"
                                value="{{ $org->id }}"
                                class="deleteBtn" 
                                onclick="return confirm('Confirm delete of \'{{ $org->name }}\' and all its employees')"
                                >
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection