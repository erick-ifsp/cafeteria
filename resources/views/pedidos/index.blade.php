@extends('layouts.gerenciamento')

@section('content')
<div class="container mt-4">
    @can('func')
        <div class="row">
            <h2 class="mt-3">Todos os Pedidos</h2>
            @if($pedidos->isEmpty())
                <p>Não há pedidos disponíveis.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Usuário</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                            <th>Nota Fiscal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->id }}</td>
                                <td>{{ $pedido->usuario->name }}</td>
                                <td>{{ number_format($pedido->total, 2, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('pedidos.status', $pedido->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                            <option value="Pendente" {{ $pedido->status == 'Pendente' ? 'selected' : '' }}>Pendente
                                            </option>
                                            <option value="Em Produção" {{ $pedido->status == 'Em Produção' ? 'selected' : '' }}>Em
                                                Produção</option>
                                            <option value="A Caminho" {{ $pedido->status == 'A Caminho' ? 'selected' : '' }}>A Caminho
                                            </option>
                                            <option value="Concluído" {{ $pedido->status == 'Concluído' ? 'selected' : '' }}>Concluído
                                            </option>
                                            <option value="Estorno" {{ $pedido->status == 'Estorno' ? 'selected' : '' }}>Estorno
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn"
                                        style="background-color: #35221B; color: #f1f1f1">Ver Detalhes</a>
                                </td>
                                <td>
                                    @if($pedido->status === 'Concluído')
                                        <a href="{{ route('pedidos.nota-fiscal', $pedido->id) }}" class="btn"
                                            style="background-color: #35221B; color: #f1f1f1">Gerar Recibo</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @else
        <div class="row">
            <h2>Seus Pedidos</h2>
            @if($pedidos->isEmpty())
                <p>Você ainda não tem pedidos.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                            <th>Nota Fiscal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->id }}</td>
                                <td>{{ number_format($pedido->total, 2, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('pedidos.status', $pedido->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select" onchange="this.form.submit()" disabled>
                                            <option value="Pendente" {{ $pedido->status == 'Pendente' ? 'selected' : '' }}>Pendente
                                            </option>
                                            <option value="Em Produção" {{ $pedido->status == 'Em Produção' ? 'selected' : '' }}>Em
                                                Produção</option>
                                            <option value="A Caminho" {{ $pedido->status == 'A Caminho' ? 'selected' : '' }}>A Caminho
                                            </option>
                                            <option value="Concluído" {{ $pedido->status == 'Concluído' ? 'selected' : '' }}>Concluído
                                            </option>
                                            <option value="Estorno" {{ $pedido->status == 'Estorno' ? 'selected' : '' }}>Estorno
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn"
                                        style="background-color: #35221B; color: #f1f1f1">Ver Detalhes</a>
                                </td>
                                <td>
                                    @if($pedido->status === 'Concluído')
                                        <a href="{{ route('pedidos.nota-fiscal', $pedido->id) }}" class="btn"
                                            style="background-color: #35221B; color: #f1f1f1">Gerar Recibo</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endcan
</div>

<div class="row mt-4 fixed-bottom bg-white py-3 border-top">
    <div class="col-md-11 text-end mb-2 mt-2">
        <a href="{{ route('pedidos.create') }}" class="btn" style="background-color: #98C9A3; color: #f1f1f1">Adicionar
            Pedido</a>
    </div>
</div>

@endsection