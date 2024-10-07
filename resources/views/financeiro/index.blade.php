@extends('layouts.gerenciamento')

@section('title', 'Financeiro')

@section('content')

<div class="container custom-container m-5">
    <div class="row">
        <div class="col-md-3">
            <div class="mb-4">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Voltar</a>
            </div>

            <h5 class="mb-3">Filtros</h5>
            <form action="{{ route('financeiro') }}" method="GET">
                <input type="text" name="nome" placeholder="Buscar por nome" class="form-control mb-3"
                    value="{{ request('nome') }}">

                <h5 class="mb-2 mt-3">Tipo</h5>
                <select name="tipo" class="form-control mb-3">
                    <option value="">Todos</option>
                    <option value="venda" {{ request('tipo') == 'venda' ? 'selected' : '' }}>Venda</option>
                    <option value="despesa" {{ request('tipo') == 'despesa' ? 'selected' : '' }}>Despesa</option>
                    <option value="estoque" {{ request('tipo') == 'estoque' ? 'selected' : '' }}>Estoque</option>
                </select>

                <h5 class="mb-2 mt-3">Data</h5>
                <input type="date" name="data_inicio" placeholder="Data Início" class="form-control mb-3"
                    value="{{ request('data_inicio') }}">
                <input type="date" name="data_fim" placeholder="Data Fim" class="form-control mb-3"
                    value="{{ request('data_fim') }}">

                <h5 class="mb-2 mt-3">Ordenar por</h5>
                <select name="ordem" class="form-control mb-3">
                    <option value="">Selecione...</option>
                    <option value="az" {{ request('ordem') == 'az' ? 'selected' : '' }}>Nome (A a Z)</option>
                    <option value="za" {{ request('ordem') == 'za' ? 'selected' : '' }}>Nome (Z a A)</option>
                    <option value="preco_asc" {{ request('ordem') == 'preco_asc' ? 'selected' : '' }}>Preço (Menor ao
                        Maior)</option>
                    <option value="preco_desc" {{ request('ordem') == 'preco_desc' ? 'selected' : '' }}>Preço (Maior ao
                        Menor)</option>
                    <option value="ultima_editada" {{ request('ordem') == 'ultima_editada' ? 'selected' : '' }}>Mais
                        Recente</option>
                    <option value="primeira_editada" {{ request('ordem') == 'primeira_editada' ? 'selected' : '' }}>Mais
                        Antiga</option>
                </select>

                <button type="submit" class="btn btn-block mt-4"
                    style="background-color: #35221B; color: #f1f1f1">Aplicar</button>

                <div class="mt-4">
                    <button type="button" class="btn btn-block mt-4" style="background-color: #35221B; color: #f1f1f1"
                        data-bs-toggle="modal" data-bs-target="#senhaModal">
                        Gerar Relatório
                    </button>
                </div>
            </form>
        </div>

        <div class="modal fade" id="senhaModal" tabindex="-1" aria-labelledby="senhaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="senhaModalLabel">Proteção de PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('financeiro.pdf') }}" method="GET">
                            @csrf
                            <div class="mb-3">
                                <label for="senha" class="form-label">Insira sua senha para proteger o PDF:</label>
                                <input type="password" name="senha" id="senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-block mt-4" style="background-color: #35221B; color: #f1f1f1">Gerar Relatório</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <h5 class="mb-3">{{ $financeiros ? count($financeiros) : 0 }}
                {{ $financeiros && count($financeiros) == 1 ? 'Encontrado' : 'Encontrados' }}
            </h5>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($financeiros as $financeiro)
                        <tr>
                            <td>{{ $financeiro->nome }}</td>
                            <td>{{ $financeiro->descricao }}</td>
                            <td>{{ $financeiro->tipo }}</td>
                            <td>R$ {{ number_format($financeiro->valor, 2, ',', '.') }}</td>
                            <td>{{ $financeiro->updated_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <h5>Total: R$ {{ number_format($total, 2, ',', '.') }}</h5>
            </div>

            <div class="mt-5">
                <h5>Gráfico de Lucros, Despesas e Estoque</h5>
                <canvas id="financeiroChart"></canvas>

                <h5 class="mt-5">Lucro por produto vendido</h5>
                <div style="max-width: 50%; margin: auto;">
                    <canvas id="vendasChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-container {
        max-width: 1425px;
    }
</style>

<script>
    function acumularValores(valores) {
        let acumulado = 0;
        return valores.map(valor => {
            acumulado += parseFloat(valor);
            return acumulado;
        });
    }

    var ctx = document.getElementById('financeiroChart').getContext('2d');

    var vendas = @json($financeiros->where('tipo', 'Venda')->pluck('valor')->toArray());
    var despesas = @json($financeiros->where('tipo', 'Despesa')->pluck('valor')->toArray());
    var estoque = @json($financeiros->where('tipo', 'Estoque')->pluck('valor')->toArray());

    var groupedVendas = @json($financeiros->where('tipo', 'Venda')->groupBy('updated_at')->map(function ($group) {
    return $group->sum('valor');
})->toArray());

    var groupedDespesas = @json($financeiros->where('tipo', 'Despesa')->groupBy('updated_at')->map(function ($group) {
    return $group->sum('valor');
})->toArray());

    var groupedEstoque = @json($financeiros->where('tipo', 'Estoque')->groupBy('updated_at')->map(function ($group) {
    return $group->sum('valor');
})->toArray());

    var datas = Object.keys(groupedVendas).concat(Object.keys(groupedDespesas)).concat(Object.keys(groupedEstoque)).sort();
    var vendasAcumuladas = acumularValores(datas.map(date => groupedVendas[date] || 0));
    var despesasAcumuladas = acumularValores(datas.map(date => groupedDespesas[date] || 0));
    var estoqueAcumulados = acumularValores(datas.map(date => groupedEstoque[date] || 0));

    var financeiroChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: datas,
            datasets: [
                {
                    label: 'Vendas',
                    data: vendasAcumuladas,
                    borderColor: 'green',
                    fill: false
                },
                {
                    label: 'Despesas',
                    data: despesasAcumuladas,
                    borderColor: 'red',
                    fill: false
                },
                {
                    label: 'Estoque',
                    data: estoqueAcumulados,
                    borderColor: 'blue',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Datas'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Valores (R$)'
                    }
                }
            }
        }
    });

    var produtosMaisVendidos = @json($financeiros->where('tipo', 'Venda')->groupBy('nome')->map(function ($vendas) {
    $valorUnitario = $vendas->first()->valor_unitario ?? 1;
    return $vendas->sum('valor') / $valorUnitario;
})->toArray());

    var nomesProdutos = Object.keys(produtosMaisVendidos);
    var quantidadesProdutos = Object.values(produtosMaisVendidos);

    var ctx2 = document.getElementById('vendasChart').getContext('2d');
    var vendasChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: nomesProdutos,
            datasets: [{
                data: quantidadesProdutos,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Produtos Mais Vendidos'
                }
            }
        }
    });
</script>
@endsection