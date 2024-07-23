<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }
    public function table()
    {
        $data = Users::orderBy('name');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($i) {
                $action =
                    '
                    <button type="button" class="btn btn-primary" onclick="edit($(this))" data-id="' . $i->id . '">Edit</button>
                    <button type="button" class="btn btn-danger" onclick="hapus($(this))" data-id="' . $i->id . '">Hapus</button>
                    ';
                return $action;
            })
            ->rawColumns(['action'])
            ->make('true');
    }
    public function getData($id)
    {
        $data = Users::find($id);
        return $data;
    }
    public function simpan(Request $request)
    {
        DB::beginTransaction();
        try {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                Users::create($data);
                DB::commit();
                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    public function edit(Request $request)
    {
        DB::beginTransaction();
        try {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $id = $request->id;
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                Users::find($id)->update($data);
                DB::commit();
                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    public function hapus(Request $request)
    {
        DB::beginTransaction();
        try {
            $rule = $this->cekRequest($request);
            if (!$rule['success']) {
                return response()->json($rule);
            } else {
                $id = $request->id;
                Users::find($id)->delete();
                DB::commit();
                return $this->jsonResponse(true, 'Data berhasil disimpan');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Data gagal disimpan', 'alert' => $e->getMessage()]);
        }
    }
    function cekRequest($request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['success'] = false;
            $data['messages'] = $validator->getMessageBag()->toArray();
        } else {
            $data['success'] = true;
            $data['messages'] = '';
        }
        return $data;
    }
    private function jsonResponse($success, $message, $status = 200)
    {
        return response()->json(['success' => $success, 'messages' => $message], $status);
    }
}
