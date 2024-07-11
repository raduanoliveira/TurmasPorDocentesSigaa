<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Unidade extends Model
{
    use HasFactory;

    public static function acessarUnidadePeloId($idUnidade)
    {

        if (is_null($idUnidade)) {
            return false;
        }

        $informacoesAplicacao = ApiSinfo::pegarInformacoesAplicacao();

        return Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
            $informacoesAplicacao['credenciais']['url_base'] .
                "/unidade/" .
                $informacoesAplicacao['credenciais']['versao'] . "" .
                "/unidades?id-unidade=" . $idUnidade
        )->throwUnlessStatus(200)->json();

    }

    public static function acessarUnidadePelaSigla($sigla)
    {

        if (is_null($sigla)) {
            return false;
        }

        $informacoesAplicacao = ApiSinfo::pegarInformacoesAplicacao();

        $resultados = Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
            $informacoesAplicacao['credenciais']['url_base'] .
                "/unidade/" .
                $informacoesAplicacao['credenciais']['versao'] . "" .
                "/unidades?sigla=" . $sigla . "&orcamentaria=true&id-unidade-responsavel=605"
        )->throwUnlessStatus(200)->json();

        if($resultados){
            $key = array_search(strtoupper($sigla), array_column($resultados,'sigla'));
            if($key !== false){
                return $resultados[$key];
            }else{
                return null;
            }
        }else{
            return null;
        }
       
    }
}
