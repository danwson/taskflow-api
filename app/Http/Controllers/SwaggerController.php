<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'TaskFlow API',
    version: '1.0.0',
    description: 'API REST de gestão de tarefas para times com suporte a webhooks.'
)]
#[OA\Server(url: 'http://taskflow-api.test', description: 'Local')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class SwaggerController {}
