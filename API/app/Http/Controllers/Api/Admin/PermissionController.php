<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Permission\PermissionRequest;
use App\Repository\Permission\PermissionInterface;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Menu;

class PermissionController extends Controller
{

    Protected $permission;
    public function __construct(PermissionInterface $permission)
    {
        $this->permission = $permission;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->permission->getall();  
        return $this->successResponse($records, 'Permissions retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $sh = $this->permission->store($data);  

        if($sh) {
           return $this->successResponse(
                $sh,
                'Permission added successfully.',
                201
            );
        }

        return $this->errorResponse('Unable to add Permission.', 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
    
        $records  = Menu::where('parent_id', 0)->where('status', 1)->get();
        $submenus = Menu::where('parent_id', '>', 0)->where('status', 1)->get();
        $data = ['records'=>$records, 'submenus'=>$submenus];
        return $this->successResponse($data, 'Permission retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, Permission $Permission)
    {
        
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $sh = $this->permission->update($data, $Permission->id);

        if($sh) {
            return $this->successResponse(
                $sh,
                'Permission updated successfully.',
                200
            );
        }

        return $this->errorResponse('Unable to update Permission.', 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Permission = $this->permission->delete($id);

        if($Permission) {
            return $this->successResponse(null, 'Permission deleted successfully.', 200);
        }

        return $this->errorResponse('Unable to delete Permission.', 400);
    }

    public function statusChange($id)
    {
        $Permission = $this->permission->statusChange($id);

        if($Permission) {
            return $this->successResponse(null, 'Permission Status Successfully Changed.', 200);
        }

        return $this->errorResponse('Unable to change Permission status.', 400);
    }
}

