<?php

namespace App\Services\Complaint;

use App\Repositories\Complaint\ComplaintDeadlineRepository;
use App\Services\AbstractService;

class ComplaintDeadlineService extends AbstractService
{
    public function __construct(ComplaintDeadlineRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Regra de negócio para prolongar o prazo.
     */
    public function extendDeadline(int $complaintId, int $additionalDays, ?string $reason = null): bool
    {
        // O Service delega a execução transacional ao repositório correspondente
        return $this->repository->extendDeadline($complaintId, $additionalDays, $reason);
    }

    public function percentageServicedWithinDeadline()
    {
        return $this->repository->percentageServicedWithinDeadline();
    }
}
