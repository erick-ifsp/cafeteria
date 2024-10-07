@extends('layouts.gerenciamento')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header text-center" style="background-color: #35221B; color: #f1f1f1">
                    <h3>Pagamento</h3>
                </div>
                <div class="card-body p-4">
                    <form id="pagamentoForm" action="" method="POST">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        @csrf
                        <div class="mb-4">
                            <label for="metodo_pagamento" class="form-label">Método de Pagamento</label>
                            <select id="metodo_pagamento" name="metodo_pagamento" class="form-select" required>
                                <option value="" selected disabled>Escolha um método</option>
                                <option value="cartao">Cartão</option>
                                <option value="pix">PIX</option>
                                <option value="dinheiro">Dinheiro</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="entrega" class="form-label">Tipo de Entrega</label>
                            <select id="entrega" name="entrega" class="form-select" required>
                                <option value="" selected disabled>Escolha uma opção</option>
                                <option value="retirada">Retirar na loja</option>
                                <option value="entrega">Entrega</option>
                            </select>
                        </div>

                        <div id="endereco-section" class="d-none mb-4">
                            <label for="endereco" class="form-label">Escolha um endereço</label>
                            <select id="endereco" name="endereco" class="form-select">
                                @forelse ($enderecos as $endereco)
                                    <option value="{{ $endereco->id }}">{{ $endereco->rua }}, {{ $endereco->cidade }} -
                                        {{ $endereco->estado }} - {{ $endereco->cep }}
                                    </option>
                                @empty
                                    <option value="">Nenhum endereço cadastrado</option>
                                @endforelse
                                <option value="novo">Adicionar Novo Endereço</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <h4>Total: <span id="total-carrinho">R$
                                    {{ number_format($carrinhoItems->sum(function ($item) {
    return $item->preco * $item->quantidade; }), 2, ',', '.') }}</span>
                            </h4>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" id="checkout-test-button" class="btn"
                                style="background-color: #98C9A3">
                                Seguir para o pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adicionarEnderecoModal" tabindex="-1" aria-labelledby="adicionarEnderecoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Novo Endereço</h5>
                </div>
                <div class="modal-body">
                    <form id="adicionarEnderecoForm" action="{{ route('enderecos.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="cpf" class="form-label">CPF:</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite o CPF:"
                                required>
                            <div id="cpf-error" class="text-danger mb-3" style="display:none;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="rua" class="form-label">Rua</label>
                            <input type="text" id="rua" name="rua" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" id="cidade" name="cidade" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" id="estado" name="estado" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000"
                                required>
                            <div id="cep-error" class="text-danger mt-2" style="display: none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn" style="background-color: #98C9A3">Adicionar
                                Endereço</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const metodoPagamentoSelect = document.getElementById('metodo_pagamento');
        const pagamentoForm = document.getElementById('pagamentoForm');

        metodoPagamentoSelect.addEventListener('change', function () {
            let rotaPagamento = '';

            if (this.value === 'cartao') {
                rotaPagamento = "{{ route('pagamento.cartao') }}";
            } else if (this.value === 'pix') {
                rotaPagamento = "{{ route('pagamento.pix') }}";
            } else if (this.value === 'dinheiro') {
                rotaPagamento = "{{ route('pagamento.success') }}";
            }

            pagamentoForm.action = rotaPagamento;
        });

        const entregaSelect = document.getElementById('entrega');
        const enderecoSection = document.getElementById('endereco-section');

        entregaSelect.addEventListener('change', function () {
            if (this.value === 'entrega') {
                enderecoSection.classList.remove('d-none');
            } else {
                enderecoSection.classList.add('d-none');
            }
        });

        const enderecoSelect = document.getElementById('endereco');

        enderecoSelect.addEventListener('change', function () {
            if (this.value === 'novo') {
                const modal = new bootstrap.Modal(document.getElementById('adicionarEnderecoModal'));
                modal.show();
                enderecoSelect.value = '';
            }
        });


        document.getElementById('adicionarEnderecoForm').addEventListener('submit', function (e) {
            const cep = document.getElementById('cep').value;
            const regex = /^124(60|6[1-9]|7[0-9]|8[0-9]|89)-\d{3}$/;
            const errorDiv = document.getElementById('cep-error');

            if (!regex.test(cep)) {
                e.preventDefault();
                errorDiv.textContent = 'Infelizmente não fazemos entrega para esse endereço';
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
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