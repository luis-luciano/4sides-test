@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Edit User') }}</div>
                    <div class="card-body">
                            <div class="row">
                                @if (session()->has('message'))
                                    <div id="alert-success" class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session()->get('message'); }}
                                         <button id="alert-success-close" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="name" class="form-label">{{ __('Name') }}</label>
                                        <input id="name" type="text" class="form-control @error('usuario_nombre') is-invalid @enderror" name="usuario_nombre" value="{{ old('usuario_nombre', $user->usuario_nombre) }}" required autocomplete="usuario_nombre" disabled>
                                    </div>
                                    <div class="row">
                                        <label for="is_admin" class="form-label">{{ __('Es administrador') }}</label>
                                        <input id="is_admin" type="text" class="form-control"   name="is_admin" value="{{ $user->isAdminDescription }}" required autocomplete="is_admin" disabled>
                                    </div>
                                    <div class="row">
                                        <label for="usuario_alias" class="form-label">{{ __('Alias') }}</label>
                                        <input id="usuario_alias" type="text" class="form-control   name="usuario_nombre" value="{{ old('usuario_alias', $user->usuario_alias) }}" required autocomplete="usuario_alias" disabled>
                                    </div>
                                    <div class="row">
                                        <label for="usuario_email" class="form-label">{{ __('Email') }}</label>
                                        <input id="usuario_email" type="text" class="form-control"   name="usuario_nombre" value="{{ old('usuario_email', $user->usuario_email) }}" required autocomplete="usuario_email" disabled>
                                    </div>
                                    <div class="row">
                                        <label for="usuario_estado" class="form-label">{{ __('Estado') }}</label>
                                        <input id="usuario_estado" type="text" class="form-control"   name="usuario_estado" value="{{ old('usuario_estado', $user->usuario_estado) }}" required autocomplete="usuario_estado" disabled>
                                    </div>
                                    <div class="row">
                                        <label for="usuario_conectado" class="form-label">{{ __('Conectado') }}</label>
                                        <input id="usuario_conectado" type="text" class="form-control"   name="usuario_nombre" value="{{ old('usuario_conectado', $user->userConnected) }}" required autocomplete="usuario_conectado" disabled>
                                    </div>
                                    <div class="row">
                                        <label for="usuario_ultima_conexion" class="form-label">{{ __('Ultima conexión') }}</label>
                                        <input id="usuario_ultima_conexion" type="text" class="form-control"   name="usuario_ultima_conexion" value="{{ old('usuario_ultima_conexion', $user->lastConnected) }}" required autocomplete="usuario_ultima_conexion" disabled>
                                    </div>
                                </div>

                                <form action="{{ route('users.profiles.update', $user->idUsuario) }}" method="POST" enctype="multipart/form-data" class="col-md-6">
                                    @csrf
                                    @method('PUT')
                                    <img id="profile-preview" src="{{ $user->profile }}" class="form-control img-thumbnail">

                                    <input name="profile_image" type="file" id="profile-input" class="form-control mt-2" style="display: none;" accept="image/*">
                                    @can('update', $user)
                                        <button href="#" id="upload-trigger" class="form-control btn btn-success">
                                            {{ __('Adjuntar foto') }}
                                        </button>

                                        <div id="action-buttons" class="mt-2" style="display: none;">
                                            <button id="save-button" class="btn btn-primary" type="submit">Guardar</button>
                                            <button id="cancel-button" class="btn btn-secondary">Cancelar</button>
                                        </div>
                                    @endcan
                                </form>
                            </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('alert-success')?.addEventListener('click', function () {
        timeout(() => {
            this.style.display = 'none';
        }, 3000);
    });
    document.getElementById('upload-trigger').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('profile-input').click();
    });

    document.getElementById('profile-input').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById('profile-preview').src = event.target.result;
                document.getElementById('action-buttons').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('cancel-button').addEventListener('click', function () {
        document.getElementById('profile-preview').src = "{{ $user->profile }}";
        document.getElementById('profile-input').value = '';
        document.getElementById('action-buttons').style.display = 'none';
    });
</script>
@endpush
