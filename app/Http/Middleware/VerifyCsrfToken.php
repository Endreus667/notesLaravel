<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Adicione rotas que você deseja excluir da verificação CSRF aqui
        // Exemplo: 'api/*' para excluir todas as rotas com prefixo "api"
    ];
}
