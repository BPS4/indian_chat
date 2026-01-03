<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{

    public function index($customerId = null)
    {

        try {
            $page_title = 'Role';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Role',
                    'url' => '',
                ]
            ];

            $roles = Role::orderBy('id', 'desc')->get();

            return view('admin.pages.adminrole.list', compact('page_title', 'page_description', 'breadcrumbs',  'roles'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function add(Request $request, $roleId = null)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'role' => 'required',
                ], [
                    'role.required' => 'Enter role title.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();


                $array = [
                    'role' => $request->role,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,

                ];
                $UserMobile = Role::where('role', $array['role'])->exists();
                if ($UserMobile) {
                    return redirect()->back()->withErrors(['Role title already exist.'])->withInput($request->all());
                }
                // dd(  $array );
                $response = Role::UpdateOrCreate(['id' => null], $array);
                DB::commit();
                return redirect('admin/role/list')->with('success', 'Role details added successfully.');
            }

            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];


            return view('admin.pages.adminrole.add', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'role' => 'required',
                ], [
                    'role.required' => 'Enter role title.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();


                $array = [
                    'role' => $request->role,
                    'updated_by' => Auth::user()->id,

                ];
                $UserEmail = Role::where('role', $array['role'])->where('id', '!=', $id)->exists();
                if ($UserEmail) {
                    return redirect()->back()->withErrors(['Role title already exist.'])->withInput($request->all());
                }

                // dd(  $array );
                $response = Role::UpdateOrCreate(['id' => $id], $array);
                DB::commit();
                return redirect('admin/role/list')->with('success', 'Role details added successfully.');
            }

            $details = Role::where('id',   $id)->first();
            if ($details) {

                $pageSettings = $this->pageSetting('edit');

                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];


                return view('admin.pages.adminrole.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
            } else {
                return redirect()->back()->withErrors(['Role details not exist.']);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    public function permissions(Request $request, $id)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());

                DB::beginTransaction();

                $permissionArray = [];
                $modules = modulesList();
                if ($modules) {
                    foreach ($modules as $key => $module) {
                        if (isset($request['permissions'][$module['id']])) {
                            $permission = $request['permissions'][$module['id']];
                        } else {
                            $permission = 0;
                        }
                        $permissionArray[$module['slug']] = (int) $permission;
                    }
                }
                $extraPermissions = [
                    'permission_hold_status' => (int) $request->permission_hold_status,
                    'permission_order_quantity' => (int) $request->permission_order_quantity,
                    'permission_order_cancel' => (int) $request->permission_order_cancel,
                    'permission_download_pdf' => (int) $request->permission_download_pdf,
                ];
                $permissionArray = array_merge($permissionArray, $extraPermissions);
                $permissionArray['dashboard'] = 5; // Set dashboard as default 5
                $array = [
                    'permission' => json_encode($permissionArray),
                    'updated_by' => Auth::user()->id,
                ];

                $response = Role::UpdateOrCreate(['id' => $id], $array);
                DB::commit();
                return redirect('admin/role/list')->with('success', 'Role permissions updated successfully.');
            }

            $details = Role::where('id', $id)->first();
            if ($details) {

                // dd($details);

                $pageSettings = $this->pageSetting('permission', ['title' => $details->role . ' Permissions']);

                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];

                return view('admin.pages.adminrole.permission', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
            } else {
                return redirect()->back()->withErrors(['Role details not exist.']);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }



    public function delete($id)
    {
        try {
            if ($id) {
                DB::beginTransaction();
                $cat = Patient::find($id);
                if ($cat->delete()) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Patient deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Patient details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function updateStatus($id, $status)
    {
        try {
            if ($id) {
                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Patient::UpdateOrCreate(['id' => $id], $updateArr);
                DB::commit();
                return redirect()->back()->with('success', 'Patient status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Patient details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }




    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Role List';
            $data['page_description'] = 'Edit Role';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Roles',
                    'url' => url('admin/role/list'),
                ]
            ];
            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => '',

                    ];
            }
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Roles';
            $data['page_description'] = 'Add New Role';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Roles',
                    'url' => url('admin/role/list'),
                ],
                [
                    'title' => 'Add a New Role',
                    'url' => '',
                ],
            ];
            return $data;
        }
        if ($action == 'permission') {
            $data['page_title'] = 'Role Permissions';
            $data['page_description'] = 'Update Role Permissions';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Roles',
                    'url' => url('admin/role/list'),
                ]
            ];
            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => '',

                    ];
            }
            return $data;
        }
    }
}
