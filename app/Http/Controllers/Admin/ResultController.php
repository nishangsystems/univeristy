<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Config;
use App\Models\Result;
use App\Models\Sequence;
use App\Models\StudentClass;
use App\Models\Students;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Redirect;
use Session;

class ResultController extends Controller
{

    public function index(Request $request)
    {
        $data['releases'] = \App\Models\Config::orderBy('id', 'desc')->get();
        $data['title'] = "All result releases";
        return view('admin.setting.result.index')->with($data);
    }

    public function create(Request $request)
    {
        $data['title'] = "Add Release";
        return view('admin.setting.result.create')->with($data);
    }

    public function edit(Request $request, $id)
    {
        $data['title'] = "Edit result releases";
        $data['release'] = \App\Models\Config::find($id);
        return view('admin.setting.result.edit')->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required',
            'seq_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        Config::create($request->all());
        return redirect()->to(route('admin.result_release.index'))->with('success', "Release created successfully");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'year_id' => 'required',
            'seq_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $release = \App\Models\Config::find($id);
        $release->update($request->all());
        return redirect()->to(route('admin.result_release.index'))->with('success', "Release updated successfully");
    }

    public function destroy(Request $request, $id)
    {
        $config = Config::find($id);
        if (\App\Models\Config::all()->count() > 0) {
            $config->delete();
            return redirect()->back()->with('success', "Release deleted successfully");
        } else {
            return redirect()->back()->with('error', "Change current academic year");
        }
    }

    public function import()
    {
        return view('admin.result.import');
    }

    public function importPost(Request $request)
    {
        // Validate request
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlxs',
        ]);

        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension();
        $filename = "Names." . $extension;

        $valid_extension = array("csv", "xls");
        if (in_array(strtolower($extension), $valid_extension)) {
            // File upload location
            $location = public_path() . '/files/';
            // Upload file
            $file->move($location, $filename);
            $filepath = public_path('/files/' . $filename);

            $file = fopen($filepath, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 100, ",")) !== FALSE) {
                $num = count($filedata);
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);

            \DB::beginTransaction();
            try {
                foreach ($importData_arr as $k => $importData) {
                    if ($k > 0) {
                        $result = Result::where([
                            'student_id' =>  $importData[1],
                            'class_id' => $importData[2],
                            'sequence' => $importData[3],
                            'subject_id' => $importData[4],
                            'batch_id' => $importData[0]
                        ])->first();

                        if ($result == null) {
                            $result = new Result();
                        }

                        $result->batch_id = $importData[0];
                        $result->student_id =  $importData[1];
                        $result->class_id =  $importData[2];
                        $result->sequence =  $importData[3];
                        $result->subject_id =  $importData[4];
                        $result->score =  $importData[5];
                        $result->coef =  $importData[6] ?? 1;
                        $result->remark = $importData[7];
                        $result->class_subject_id =  $importData[0];
                        $result->save();
                    }
                }

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollback();
                echo ($e->getMessage());
            }
            Session::flash('message', 'Import Successful.');
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");

        } else {
            Session::flash('message', 'Invalid File Extension.');
        }
        return redirect()->back()->with('success', 'Result Imported successfully!');
    }

    public function export()
    {
        return view('admin.result.export');
    }

    public function exportPost(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'sequence' => 'required',
        ]);


        $results = Result::where(['batch_id' => $request->year, 'sequence' => $request->sequence])->get();
        $year = Batch::find($request->year);
        $sequence = Sequence::find($request->sequence);

        $fileName = $sequence->name . ' ' . $year->name . ' ' . 'results.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('batch_id', 'student_id', 'class_id', 'sequence', 'subject_id', 'score', 'coef', 'remark', 'class_subject_id');

        $callback = function () use ($results, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($results as $result) {
                fputcsv($file, array($result->batch_id, $result->student_id, $result->class_id, $result->sequence, $result->subject_id, $result->score, $result->coef, $result->remark, $result->class_subject_id));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
