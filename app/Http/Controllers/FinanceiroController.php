<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Financeiro;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Adicionar essa importação

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $nome = $request->input('nome');
        $ordem = $request->input('ordem');
        $tipo = $request->input('tipo');
        $produto = $request->input('produto');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $query = Financeiro::query();

        if ($nome) {
            $query->where('nome', 'like', "%{$nome}%");
        }

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if (!$dataInicio && !$dataFim) {
            $dataInicio = Carbon::now()->subDays(30)->startOfDay();
            $dataFim = Carbon::now()->endOfDay();
            $query->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio && $dataFim) {
            $query->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            $query->whereDate('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            $query->whereDate('updated_at', '<=', $dataFim);
        }

        if ($produto) {
            $query->where('descricao', 'like', "%{$produto}%");
        }

        switch ($ordem) {
            case 'az':
                $query->orderBy('nome', 'asc');
                break;
            case 'za':
                $query->orderBy('nome', 'desc');
                break;
            case 'preco_asc':
                $query->orderBy('valor', 'asc');
                break;
            case 'preco_desc':
                $query->orderBy('valor', 'desc');
                break;
            case 'ultima_editada':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'primeira_editada':
                $query->orderBy('updated_at', 'asc');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        $financeiros = $query->get();
        $total = $financeiros->sum('valor');

        return view('financeiro.index', [
            'financeiros' => $financeiros,
            'total' => $total,
        ]);
    }

    public function show($id)
    {
        $transacao = Financeiro::where('user_id', Auth::id())->findOrFail($id);
        return view('financeiro.show', compact('transacao'));
    }

    public function generatePdf(Request $request)
    {
        $query = Financeiro::query();

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('updated_at', [$request->input('data_inicio'), $request->input('data_fim')]);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        $financeiros = $query->get();

        $vendasQuantidade = $financeiros->where('tipo', 'Venda')
            ->groupBy('nome')
            ->map(function ($vendas) {
                return [
                    'quantidade' => $vendas->count(),
                    'valor_total' => $vendas->sum('valor')
                ];
            });

        $totalVendas = $financeiros->where('tipo', 'Venda')->sum('valor');
        $totalEstoque = $financeiros->where('tipo', 'Estoque')->sum('valor');
        $totalDespesas = $financeiros->where('tipo', 'Despesa')->sum('valor');
        $totalSalarios = $financeiros->where('descricao', 'Salários')->sum('valor');

        $taxaImposto = 0.18;
        $valorImposto = $totalVendas * $taxaImposto;
        $lucroAntesImposto = $totalVendas + $totalDespesas;
        $lucroLiquido = $lucroAntesImposto - $valorImposto;

        $timestamp = now()->format('d/m/Y H:i');
        $data = [
            'vendasQuantidade' => $vendasQuantidade,
            'financeiros' => $financeiros,
            'totalVendas' => $totalVendas,
            'totalEstoque' => $totalEstoque,
            'totalDespesas' => $totalDespesas,
            'totalSalarios' => $totalSalarios,
            'valorImposto' => $valorImposto,
            'lucroAntesImposto' => $lucroAntesImposto,
            'lucroLiquido' => $lucroLiquido,
            'timestamp' => $timestamp,
        ];

        $pdf = Pdf::loadView('financeiro.pdf', $data);

        $senha = $request->input('senha');

        $pdf->setEncryption($senha);

        return $pdf->download('relatorio-financeiro.pdf');
    }
}
