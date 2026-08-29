<?php

namespace App\SpectoraEngine\Incidents;

enum IncidentState: string
{
    case HEALTHY = 'healthy';
    case DEGRADED = 'degraded';
    case DOWN = 'down';
    case RECOVERED = 'recovered';
}
