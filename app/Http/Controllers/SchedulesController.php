<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Schedule;
use App\Models\SyncJobLog;
use App\Models\Products\ChangeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Excel, DB;

class SchedulesController extends Controller
{
    public function __construct()
    {
//        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $schedules = Schedule::whereMerchantId(auth()
                             ->id())
                             ->orderBy('run_at', 'ASC')
                             ->get();
        
        /*$query = SyncJobLog::with('changeLogs')
            ->whereMerchantId(auth()->id());*/
        $query = SyncJobLog//::with('changeLogs')
            ::whereMerchantId(auth()->id());

        $totalCount = $query->count();
        $syncLogs = $query->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($request->except('page'));

        // To be used with hours
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = Carbon::createFromTime($i, 0, 0);
        }

        return view('pages.schedules.index', compact('schedules', 'hours', 'syncLogs', 'totalCount'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'channel' => 'required|in:amazon,ebay',
            'run_at'  => 'required',
        ]);

        Schedule::firstOrCreate(array_merge(
            $request->only('channel', 'run_at'),
            ['merchant_id' => auth()->id()]
        ));

        session()->flash('success', 'Successfully added schedule.');

        return redirect()->back();
    }

    public function destroy($id)
    {
        Schedule::whereMerchantId(auth()->id())->whereId($id)->first()->delete();

        return 'good';
    }
    
    public function clearHistory()
    {
        $merchantId = auth()->id();
        SyncJobLog::where('merchant_id', $merchantId)->delete();
        
        return redirect('/schedules');
    }
    
    public function downloadHistory($logId)
    {
        $log = SyncJobLog::find($logId);
        
        $reportData = [];
        $reportData[] = [
            'Channel : ' . $log->channel
        ];
        $reportData[] = [
            'Product',
            'Action',
            'Action Details',
            'Date'
        ];
        
        foreach($log->changeLogs as $row) {
            $reportData[] = [
                $row->product->name,
                ucfirst($row->action),
                $this->getActionDetails($row->data),
                $row->created_at,
            ];  
        }
        
        $fileName = sprintf('%s-%s-%s', "Job History", $log->channel, $log->created_at);
        Excel::create($fileName, function($excel) use ($reportData) {
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Job History');
            // $excel->setCreator('CSL')->setCompany('CSL');
            //$excel->setDescription('Grantee Reports Quarters');    
            
            $excel->sheet('sheet1', function($sheet) use ($reportData) {
                // data
                $sheet->fromArray($reportData, null, 'B1', false, false);
                
                // common formatting
                $sheet->setFreeze('A3');
                $sheet->setFontSize(12);
                
                // first row formatting
                $sheet->mergeCells('B1:E1')
                    ->cell('B1', function($cell) {
                        $cell->setBackground('#bfbfbf');
                        $cell->setFontWeight('bold');
                    });
                    
                // second row formatting
                $sheet->cells('B2:E2', function($cells) {
                    $cells->setBackground('#d9d9d9')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center');
                    });
            });
            
        })->download('xlsx');
    }
    
    private function getActionDetails($data) {
		$result = '';
		if(isset($data['reason'])) {
			$result .= $data['reason'];
		}
		if(isset($data['before'])) {
			$result .= " Before:{$data['before']}";
		}
		if(isset($data['after'])) {
			$result .= ", After:{$data['after']}";
		}
        return $result;
    }
}
