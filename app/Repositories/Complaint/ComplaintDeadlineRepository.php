<?php
namespace App\Repositories\Complaint;

use App\Models\Complaint\ComplaintDeadline;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class ComplaintDeadlineRepository extends AbstractRepository
{
    public function __construct(ComplaintDeadline $model)
    {
        parent::__construct($model);
    }

    public function percentageServicedWithinDeadline(){

     
            $dados = DB::table('complaint_deadlines')
            ->select(
                DB::raw("YEAR(start_date) as ano"),
             

                DB::raw("SUM(CASE WHEN status = 'concluido' AND updated_at <= end_date THEN 1 ELSE 0 END) as percentage"),
                DB::raw("COUNT(*) as total"),
                DB::raw("ROUND(SUM(CASE WHEN status = 'concluido' AND updated_at <= end_date THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as percentage")
            )
            ->where('status', '!=', 'expirado')
            ->groupBy(DB::raw("YEAR(start_date), MONTH(start_date)"))
            ->orderBy(DB::raw("YEAR(start_date), MONTH(start_date)"))
            ->get();

        setlocale(LC_TIME, 'pt_PT.UTF-8');

        foreach ($dados as $dado) {
            $dado->mes = Carbon::createFromDate($dado->ano,  1)
                ->locale('pt_PT')
                ->translatedFormat('F');
        }

        return $dados;
        }
        
    
    
}