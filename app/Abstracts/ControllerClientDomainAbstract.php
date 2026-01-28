<?php

namespace App\Abstracts;

use App\Enums\DomainEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControllerClientDomainAbstract extends Controller
{
    protected DomainEnum $clientDomain;

    public function __construct(Request $request)
    {
        $this->clientDomain = DomainEnum::from($request->header('client-domain', DomainEnum::NORMAL->value));
    }
}
