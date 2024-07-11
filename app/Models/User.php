<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function acessarUsuario($login = null, $nome = null){
        $informacoesAplicacao = ApiSinfo::pegarInformacoesAplicacao();
        
        $url = "";
        if (!is_null($login)) {
            $url = "/usuarios?login=" . $login;
        } else if (!is_null($nome)) {
            $url = "/usuarios?nome=" . $nome;
        } else{
            return false;
        }

        return Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
            $informacoesAplicacao['credenciais']['url_base'] .
                "/usuario/" .
                $informacoesAplicacao['credenciais']['versao'] . "" .
                $url
        )->throwUnlessStatus(200)->json();
    }

    public static function docentesDaUnidade($siglaUnidade){
        $informacoesAplicacao = ApiSinfo::pegarInformacoesAplicacao();

        $unidade = Unidade::acessarUnidadePelaSigla($siglaUnidade);

        if( is_null($unidade)){
            return "Unidade Não encontrada";
        }

        try {
            $offset = 0;
            $limit = 100;
            $todosDocentes = [];

            do {
                $resultados = Http::withHeaders($informacoesAplicacao['headersAplicacao'])->get(
                    $informacoesAplicacao['credenciais']['url_base'] .
                        "/docente/" .
                        $informacoesAplicacao['credenciais']['versao'] . "" .
                        "/docentes?id-unidade=" . $unidade['id-unidade'] . "&id-ativo=1&id-situacao=1".
                        "&offset=" . $offset . "&limit=" . $limit . "&order-asc=nome"
                )->throwUnlessStatus(200)->json();

                if(!empty($resultados)){
                    foreach ($resultados as $docente) {
                        array_push($todosDocentes, $docente);
                    }
                }

                $offset = $offset + 100;
            } while (!empty($resultados));

        } catch (\Throwable $th) {
            throw $th;
        }

        return $todosDocentes;
    }
}
