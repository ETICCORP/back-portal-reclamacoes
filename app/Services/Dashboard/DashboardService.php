<?php

namespace App\Services\Dashboard;

use App\Enum\TypeEntity;
use App\Services\Entities\EntitiesService;
use App\Services\Entities\RiskAssessmentService;

class DashboardService
{
    public function __construct(
        private readonly EntitiesService $entitiesService,
        private readonly RiskAssessmentService $riskAssessmentService
    ) {}

    public function totalGeralData()
    {
        $totalEntities = $this->entitiesService->getTotalEntites();
        $totalRiskAssessments = $this->riskAssessmentService->getTotalRiskAssessments();
        $totalEntitiesColectivo = $this->entitiesService->getEntitiesByType(TypeEntity::COLECTIVA);
        $totalEntitiesSingular = $this->entitiesService->getEntitiesByType(TypeEntity::SINGULAR);
        $lastsAssessment = $this->riskAssessmentService->getLastAssessment(3);

        return [
            'total_entities' => $totalEntities,
            'total_risk_assessments' => $totalRiskAssessments,
            'total_entities_colective' => $totalEntitiesColectivo,
            'total_entities_singular' => $totalEntitiesSingular,
            'lasts_assessment' => $lastsAssessment,
            'lasts_entities' => $this->entitiesService->getLastEntities(3)
        ];
    }
}
