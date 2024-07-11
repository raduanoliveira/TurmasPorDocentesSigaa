<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ApiSinfo extends Model
{
    use HasFactory;

    public static function getClienteSIG(){
        $arquivoJson = "sig.json";

        if (!Storage::disk('sig')->exists($arquivoJson)) {

            throw new InvalidArgumentException(sprintf('arquivo "%s" não existe', $arquivoJson));
        } else {
            return Storage::disk('sig')->json($arquivoJson);
        }
    }

    public static function recuperarTokenAplicacao($credenciais)
    {
        return Http::post(
            $credenciais['url_base_autenticacao'] .
                "/authz-server/oauth/token?client_id=" .
                $credenciais['client_id'] . "" .
                "&client_secret=" . $credenciais['client_secret'] .
                "&grant_type=client_credentials"
        )->throwUnlessStatus(200)->json();
    }

    public static function pegarInformacoesAplicacao()
    {

        $credenciais =  self::getClienteSIG();

        $tokenAplicacao = self::recuperarTokenAplicacao($credenciais);

        $headersAplicacao = [
            "Authorization" => "Bearer " . $tokenAplicacao['access_token'],
            "x-api-key" => $credenciais['x_api_key'],
            "Accept" => "application/json;charset=UTF-8"
        ];

        return ['credenciais' => $credenciais, 'headersAplicacao' => $headersAplicacao];
    }
}
