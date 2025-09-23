<?php
namespace App\Services\Alert\GrupoAlertEmails;

use App\Repositories\Alert\GrupoAlertEmails\GrupoAlertEmailsRepository;
use App\Services\AbstractService;

class GrupoAlertEmailsService extends AbstractService
{
    public function __construct(GrupoAlertEmailsRepository $repository)
    {
        parent::__construct($repository);
    }
}