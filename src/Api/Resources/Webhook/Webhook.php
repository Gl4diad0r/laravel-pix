<?php

namespace Junges\Pix\Api\Resources\Webhook;

use Illuminate\Http\Client\Response;
use Junges\Pix\Api\Api;
use Junges\Pix\Api\Contracts\ApplyApiFilters;
use Junges\Pix\Api\Contracts\ConsumesWebhookEndpoints;
use Junges\Pix\Api\Contracts\FilterApiRequests;
use Junges\Pix\Support\Endpoints;
use RuntimeException;

class Webhook extends Api implements ConsumesWebhookEndpoints, FilterApiRequests
{
    private array $filters = [];

    public function withFilters($filters): Webhook
    {
        if (! is_array($filters) && ! $filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function create(string $pixKey, string $callbackUrl): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl . Endpoints::CREATE_WEBHOOK . $pixKey);

        return $this->request()->put($endpoint, ['webhookUrl' => $callbackUrl]);
    }

    public function getByPixKey(string $pixKey): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl . Endpoints::GET_WEBHOOK . $pixKey);

        return $this->request()->get($endpoint);
    }

    public function delete(string $pixKey): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl . Endpoints::DELETE_WEBHOOK . $pixKey);

        return $this->request()->delete($endpoint);
    }

    public function all(): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl . Endpoints::GET_WEBHOOKS);

        return $this->request()->get($endpoint, $this->filters);
    }
}