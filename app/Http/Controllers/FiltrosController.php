<?php

namespace App\Http\Controllers;

use App\GrupoEconomico;
use App\Bandeira;
use App\Unidade;
use App\Funcionario;
use App\Traits\Filtros;
use Illuminate\Http\Request;

class FiltrosController extends Controller
{
    public function index()
    {
        $filtros = $this->montarFiltros(['gruposEconomicos', 'bandeiras', 'unidades', 'funcionarios']);
        return view('pesquisas', compact('filtros'));
    }

    public function gruposEconomicos(Request $req)
    {
        switch ($req->gruposEconomicosPesquisa) {
            case '2':
                $campos = ['gec_documento', 'gec_nome_fantasia'];
                break;
            default:
                $campos = ['id', 'gec_razao_social'];
                break;
        }
        $gruposEconomicos = GrupoEconomico::select('id', 'gec_nome_fantasia', 'gec_razao_social', 'gec_documento')
            ->where(function ($query) use ($req, $campos) {
                foreach ($campos as $campo) {
                    $query->orWhere($campo, 'like', '%' . $req->pesquisa . '%');
                }
            })
            ->paginate();

        $resultado = [];
        foreach ($gruposEconomicos as $gruposEconomico) {
            $resultado[] = [
                'id' => $gruposEconomico->id,
                'text' => $gruposEconomico->{$campos[0]} . '-' . $gruposEconomico->{$campos[1]}
            ];
        }

        return response()->json([
            'results' => $resultado,
            'pagination' => [
                'more' => $gruposEconomicos->hasMorePages(),
            ],
        ]);
    }

    public function bandeiras(Request $req)
    {
        switch ($req->bandeirasPesquisa) {
            case '2':
                $campos = ['id', 'ban_nome'];
                break;
            default:
                $campos = ['ban_documento', 'ban_nome'];
                break;
        }
        $bandeiras = Bandeira::select('id', 'ban_nome', 'ban_documento')
            ->where(function ($query) use ($req, $campos) {
                foreach ($campos as $campo) {
                    $query->orWhere($campo, 'like', '%' . $req->pesquisa . '%');
                }
            })
            ->paginate();

        $resultado = [];
        foreach ($bandeiras as $bandeira) {
            $resultado[] = [
                'id' => $bandeira->id,
                'text' => $bandeira->{$campos[0]} . '-' . $bandeira->{$campos[1]}
            ];
        }

        return response()->json([
            'results' => $resultado,
            'pagination' => [
                'more' => $bandeiras->hasMorePages(),
            ],
        ]);
    }

    public function unidades(Request $req)
    {
        switch ($req->unidadesPesquisa) {
            case '2':
                $campos = ['uni_documento', 'uni_razao_social'];
                break;
            case '3':
                $campos = ['id', 'uni_nome_fantasia'];
                break;
            default:
                $campos = ['uni_documento', 'uni_razao_social'];
                break;
        }
        $unidades = Unidade::select('id', 'uni_nome_fantasia', 'uni_documento', 'uni_razao_social')
            ->where(function ($query) use ($req, $campos) {
                foreach ($campos as $campo) {
                    $query->orWhere($campo, 'like', '%' . $req->pesquisa . '%');
                }
            })
            ->paginate();

        $resultado = [];
        foreach ($unidades as $unidade) {
            $resultado[] = [
                'id' => $unidade->id,
                'text' => $unidade->{$campos[0]} . '-' . $unidade->{$campos[1]}
            ];
        }

        return response()->json([
            'results' => $resultado,
            'pagination' => [
                'more' => $unidades->hasMorePages(),
            ],
        ]);
    }

    public function funcionarios(Request $req)
    {
        switch ($req->funcionariosPesquisa) {
            case '2':
                $campos = ['id', 'fun_nome'];
                break;
            case '3':
                $campos = ['uni_nome_fantasia', 'fun_nome'];
                break;
            default:
                $campos = ['fun_documento', 'fun_nome'];
                break;
        }
        $funcionarios = Funcionario::select('funcionario.id', 'uni_id', 'fun_nome', 'fun_documento', 'unidade.uni_nome_fantasia')
            ->leftJoin('unidade', 'uni_id', '=', 'unidade.id')
            ->where(function ($query) use ($req, $campos) {
                foreach ($campos as $campo) {
                    $query->orWhere($campo === 'id' ? 'funcionario.id' : $campo, 'like', '%' . $req->pesquisa . '%');
                }
            })
            ->paginate();

        $resultado = [];
        foreach ($funcionarios as $funcionario) {
            $resultado[] = [
                'id' => $funcionario->id,
                'text' => $funcionario->{$campos[0]} . '-' . $funcionario->{$campos[1]}
            ];
        }

        return response()->json([
            'results' => $resultado,
            'pagination' => [
                'more' => $funcionarios->hasMorePages(),
            ],
        ]);
    }

    private function montarFiltros(array $campos)
    {
        $filtros = [
            'gruposEconomicos' => [
                'pesquisa' => [
                    1 => [
                        'texto' => 'Código e Nome',
                        'placeholder' => 'Pesquise por código ou nome',
                    ],
                    2 => [
                        'texto' => 'Documento e Nome fantasia',
                        'placeholder' => 'Pesquise por nome fantasia, cpf ou cnpj',
                    ],
                ],
                'atividade' => [
                    1 => 'Ativo',
                    0 => 'Inativo',
                ],
                'multiplo' => true,
                'titulo' => 'Grupos Econômicos',
                'nome' => 'gruposEconomicos',
                'placeholder' => 'Pesquise grupos econômicos',
                'rota' => route('filtros.grupoEconomico'),
            ],
            'bandeiras' => [
                'pesquisa' => [
                    1 => [
                        'texto' => 'Documento e Nome',
                        'placeholder' => 'Pesquise por documento ou nome',
                    ],
                    2 => [
                        'texto' => 'Id e Nome',
                        'placeholder' => 'Pesquise por id e nome',
                    ],
                ],
                'atividade' => [
                    1 => 'Ativo',
                    0 => 'Inativo',
                ],
                'multiplo' => true,
                'titulo' => 'Bandeiras',
                'nome' => 'bandeiras',
                'placeholder' => 'Pesquise bandeiras',
                'rota' => route('filtros.bandeira'),
            ],
            'unidades' => [
                'pesquisa' => [
                    1 => [
                        'texto' => 'Documento e Nome Fantasia',
                        'placeholder' => 'Pesquise por documento ou nome fantasia',
                    ],
                    2 => [
                        'texto' => 'Documento e Razão Social',
                        'placeholder' => 'Pesquise por documento e razão social',
                    ],
                    3 => [
                        'texto' => 'Id e Nome Fantasia',
                        'placeholder' => 'Pesquise por id ou nome fantasia',
                    ],
                ],
                'atividade' => [
                    1 => 'Ativo',
                    0 => 'Inativo',
                ],
                'multiplo' => true,
                'titulo' => 'Unidades',
                'nome' => 'unidades',
                'placeholder' => 'Pesquise unidades',
                'rota' => route('filtros.unidade'),
            ],
            'funcionarios' => [
                'pesquisa' => [
                    1 => [
                        'texto' => 'Documento e Nome',
                        'placeholder' => 'Pesquise por documento ou nome',
                    ],
                    2 => [
                        'texto' => 'Id e Nome',
                        'placeholder' => 'Pesquise por id ou nome',
                    ],
                    3 => [
                        'texto' => 'Nome e nome fantasia',
                        'placeholder' => 'Pesquise por celular ou nome fantasia',
                    ],
                ],
                'atividade' => [
                    1 => 'Ativo',
                    0 => 'Inativo',
                ],
                'multiplo' => true,
                'titulo' => 'Funcionarios',
                'nome' => 'funcionarios',
                'placeholder' => 'Pesquise funcionario',
                'rota' => route('filtros.funcionario'),
            ],
        ];

        $result = [];
        foreach ($filtros as $filtro => $dados) {
            if (!in_array($filtro, $campos))
                continue;

            $result[$filtro] = view('filtrosAjax', compact('dados'))->render();
        }

        return $result;
    }
}
