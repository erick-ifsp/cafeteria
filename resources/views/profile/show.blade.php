@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="text-center mb-4 mt-4">Perfil</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-3 vh-100">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action active" id="dados-pessoais-tab"
                    style="background-color: #f1f1f1; color: #35221B">Dados
                    Pessoais</a>
                <a href="#" class="list-group-item list-group-item-action" id="enderecos-tab"
                    style="background-color: #f1f1f1; color: #35221B">Endereços</a>
            </div>
        </div>

        <div class="col-md-9">
            <div id="dados-pessoais-content" class="content-section">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" maxlength="50" id="name" name="name"
                            value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" maxlength="100" id="email" name="email"
                            value="{{ $user->email }}" required>
                    </div>
                    <button type="submit" class="btn  " style="background-color: #35221B; color: #f1f1f1">Atualizar
                        Perfil</button>
                </form>

                <hr class="my-4">

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label">Senha Atual</label>
                        <input type="password" maxlength="50" class="form-control" id="current_password"
                            name="current_password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input type="password" maxlength="50" class="form-control" id="password" name="password"
                            required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" maxlength="50" class="form-control" id="password_confirmation"
                            name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn  " style="background-color: #35221B; color: #f1f1f1">Atualizar
                        Senha</button>
                </form>
            </div>

            <div id="enderecos-content" class="content-section d-none">
                <h4 class="mb-3">Meus Endereços</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">CPF</th>
                            <th scope="col">Rua</th>
                            <th scope="col">Cidade</th>
                            <th scope="col">Estado</th>
                            <th scope="col">CEP</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enderecos as $endereco)
                            <tr>
                                <td>{{ $endereco->cpf }}</td>
                                <td>{{ $endereco->rua }}</td>
                                <td>{{ $endereco->cidade }}</td>
                                <td>{{ $endereco->estado }}</td>
                                <td>{{ $endereco->cep }}</td>
                                <td>
                                    <button type="button" class="btn" data-edit-endereco='@json($endereco)'
                                        style="background-color: #35221B; color: #f1f1f1">
                                        Editar
                                    </button>
                                    <form action="{{ route('profile.endereco.destroy', $endereco->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn"
                                            style="background-color: #35221B; color: #f1f1f1">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn" style="background-color: #35221B; color: #f1f1f1"
                    data-bs-toggle="modal" data-bs-target="#addEnderecoModal">
                    Adicionar Endereço
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEnderecoModal" tabindex="-1" aria-labelledby="addEnderecoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEnderecoModalLabel">Cadastrar Novo Endereço</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="modal-body">
                            <form id="enderecoForm" action="{{ route('profile.endereco.store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="cpf" class="form-label">CPF:</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf"
                                        placeholder="Digite o CPF:" required>
                                    <div id="cpf-error" class="text-danger mb-3" style="display:none;"></div>
                                </div>
                                <input type="hidden" id="endereco_id" name="endereco_id">
                                <div class="form-group mb-3">
                                    <label for="rua" class="form-label">Rua</label>
                                    <input type="text" class="form-control" id="rua" maxlength="100" name="rua"
                                        placeholder="Digite o nome da rua" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" maxlength="50" id="cidade" name="cidade"
                                        placeholder="Digite o nome da cidade" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <input type="text" class="form-control" maxlength="50" id="estado" name="estado"
                                        placeholder="Digite o nome do estado" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000"
                                        required>
                                </div>
                                <div id="cep-error" class="text-danger mb-3" style="display:none;"></div>
                                <button type="submit" class="btn"
                                    style="background-color: #35221B; color: #f1f1f1">Salvar Endereço</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function mostrarConteudo(id) {
            document.querySelectorAll('.content-section').forEach(function (section) {
                section.classList.add('d-none');
            });
            document.getElementById(id + '-content').classList.remove('d-none');
        }

        document.querySelectorAll('.list-group-item').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.list-group-item').forEach(function (item) {
                    item.classList.remove('active');
                });
                this.classList.add('active');
                mostrarConteudo(this.id.replace('-tab', ''));
            });
        });

        document.querySelectorAll('[data-edit-endereco]').forEach(function (button) {
            button.addEventListener('click', function () {
                const endereco = JSON.parse(this.getAttribute('data-edit-endereco'));
                const form = document.getElementById('enderecoForm');
                form.action = '{{ route('profile.endereco.update', '') }}/' + endereco.id;
                document.getElementById('addEnderecoModalLabel').textContent = 'Editar Endereço';
                document.getElementById('cpf').value = endereco.cpf;
                document.getElementById('rua').value = endereco.rua;
                document.getElementById('cidade').value = endereco.cidade;
                document.getElementById('estado').value = endereco.estado;
                document.getElementById('cep').value = endereco.cep;

                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
                document.getElementById('endereco_id').value = endereco.id;

                new bootstrap.Modal(document.getElementById('addEnderecoModal')).show();
            });
        });
    });

    document.getElementById('enderecoForm').addEventListener('submit', function (e) {
        const cep = document.getElementById('cep').value;
        const cpf = document.getElementById('cpf').value;
        const regexCep = /^124(60|6[1-9]|7[0-9]|8[0-9]|89)-\d{3}$/;
        const cepErrorDiv = document.getElementById('cep-error');
        const cpfErrorDiv = document.getElementById('cpf-error');

        cepErrorDiv.style.display = 'none';
        cpfErrorDiv.style.display = 'none';

        if (!regexCep.test(cep)) {
            e.preventDefault();
            cepErrorDiv.textContent = 'Infelizmente não fazemos entrega para esse endereço';
            cepErrorDiv.style.display = 'block';
        }

        if (!validarCPF(cpf)) {
            e.preventDefault();
            cpfErrorDiv.textContent = 'CPF inválido. Por favor, insira um CPF válido.';
            cpfErrorDiv.style.display = 'block';
        }
    });

    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false;
        }

        let soma = 0;
        let resto;

        for (let i = 1; i <= 9; i++) {
            soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
        }

        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) {
            resto = 0;
        }
        if (resto !== parseInt(cpf.substring(9, 10))) {
            return false;
        }

        soma = 0;
        for (let i = 1; i <= 10; i++) {
            soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
        }

        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) {
            resto = 0;
        }

        if (resto !== parseInt(cpf.substring(10, 11))) {
            return false;
        }

        return true;
    }
</script>

@endsection