<?php

namespace App\Observers;

use App\Models\ServiceRequest;
use App\Services\RequestNumberService;

class ServiceRequestObserver
{
    public function created(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->updateQuietly([
            'request_number' => RequestNumberService::generate(),
            'display_number' => RequestNumberService::nextDisplayNumber(),
        ]);
    }
}
