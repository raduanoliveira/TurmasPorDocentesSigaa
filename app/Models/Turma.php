<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class Turma extends Model
{
    use HasFactory;

    public static function tipoParticipantes()
    {
        $informacoesAplicacao = ApiSinfo::pegarInformacoesAplicacao();

        //tipos participantes
        // 1 docente
        // 4 discente

        return Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
            $informacoesAplicacao['credenciais']['url_base'] .
                "/turma/" .
                $informacoesAplicacao['credenciais']['versao'] . "" .
                "/participantes/tipos-participantes"
        )->throwUnlessStatus(200)->json();
    }

    public static function turmasPorDocentesDaUnidade($sigla)
    {

        $docentes = User::docentesDaUnidade($sigla);

        if (is_null($docentes) || $docentes == "Unidade Não encontrada") {
            return "Unidade Não encontrada";
        }

        $objetosDocentes = json_decode(json_encode($docentes), FALSE);
        $informacoesAplicacao = ApiSinfo::pegarInformacoesAplicacao();

        foreach ($objetosDocentes as $docente) {
            info('-------------------------------------------------------');
            info('docente');
            info($docente->nome);
            $turmas = Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
                $informacoesAplicacao['credenciais']['url_base'] .
                    "/turma/" .
                    $informacoesAplicacao['credenciais']['versao'] . "" .
                    "/turmas?id-docente=" . $docente->{'id-docente'} . "&ano=" . date("Y") . "&limit=100&offset=0"
            )->throwUnlessStatus(200)->json();

            $docente->qtd_turmas = count($turmas);

            if (!empty($turmas)) {
                $docente->turmas = json_decode(json_encode($turmas), FALSE);
            }

            if (!empty($turmas) && count($docente->turmas) > 0) {
                foreach ($docente->turmas as $turma) {
                    info('nome componente');
                    info($turma->{'nome-componente'});
                    info('periodo');
                    info($turma->periodo);
                    info('codigo turma');
                    info($turma->{'codigo-turma'});

                    try {
                        $offset = 0;
                        $limit = 100;
                        $todosParticipantes = [];

                        do {
                            $participantes = Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
                                $informacoesAplicacao['credenciais']['url_base'] .
                                    "/turma/" .
                                    $informacoesAplicacao['credenciais']['versao'] . "" .
                                    "/participantes?id-turma=" . $turma->{'id-turma'} . "&id-tipo-participante=4". "&offset=" . $offset . "&limit=" . $limit
                            )->throwUnlessStatus(200)->json();

                            if (!empty($participantes)) {
                                foreach ($participantes as $resultadoParticipante) {
                                    array_push($todosParticipantes, $resultadoParticipante);
                                }
                            }

                            $offset = $offset + 100;
                        } while (!empty($participantes));
                    } catch (\Throwable $th) {
                        throw $th;
                    }

                    info('participantes');
                    info(count($todosParticipantes));
                    $turma->qtd_participantes = count($todosParticipantes);
                }
            }
            //tire depois dos testes
            //break;
        }

        Excel::store(new GerarRelatorioExcel($objetosDocentes, 'relatorios.turmaDocentes'), "docentesTurmas_$sigla.xlsx",'planilhas');
        return $objetosDocentes;
    }
}
