<?php

namespace App\Http\Controllers\Api\Admin;

use App\Repository\Role\RoleInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponseTrait;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\RoleMenu;
use App\Models\Role;
use App\Models\Menu;
use App\Models\User;
use Exception;

class RoleController extends Controller
{

    use ApiResponseTrait;
    Protected $role;
    public function __construct(RoleInterface $role)
    {
        $this->role = $role;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->role->getall();
        return $this->successResponse(
                $data,
                'Role list fetched successfully.'
            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = (isset($request->id) && !empty($request->id))?$request->id:'';
        $post = $request->all();

        if(isset($id) && !empty($id)){
            request()->validate([
                'role' => 'required',
            ]);
        }else{
            request()->validate([
                'role' => 'required|unique:roles,name',
            ]);
        }

        $post = $request->all();

        $role_level = (isset($post['role_level']) && !empty($post['role_level']))?implode(',', $post['role_level']):"";

        $data['name']       = $request->role;
        $data['guard_name'] = 'web';
        $data['role_level'] = $role_level;
        $data['created_at'] = Auth::user()->id;

        try{

          $role = Role::addRoles($id,$data);
          RolePermission::where('role_id',$role->id)->delete();
          RoleMenu::where('role_id',$role->id)->delete();

           if(isset($post['menu']) &&  !empty($post['menu'])){

              foreach($post['menu'] as $k => $m){
                  $menuData['role_id'] = $role->id;
                  $menuData['menu_id'] = $m;
                  $menuData['is_parent'] = 1;
                  RoleMenu::addRoleMenu($role->id,$m,$menuData);
              }
          }


            if(isset($post['subMenu']) &&  !empty($post['subMenu'])){
              foreach($post['subMenu'] as $k => $sm){
                  $subMenuData['role_id'] = $role->id;
                  $subMenuData['menu_id'] = $sm;
                  $subMenuData['is_parent'] = 0;
                  RoleMenu::addRoleMenu($role->id,$sm,$subMenuData);
              }
          }

          if(isset($post['permission']) &&  !empty($post['permission'])){
              foreach($post['permission'] as $k => $p){
                  $roleData['role_id'] = $role->id;
                  $roleData['permission_id'] = $p;
                  $roleData['access'] = (isset($post['access'][$p]) && !empty($post['access'][$p]))?implode(',', $post['access'][$p]):"";
                  RolePermission::addRolePermissions($role->id,$p,$roleData);
              }
          }
          return $this->successResponse(
                $role,
                'Role added successfully.',
                201
            );

        }catch (\Throwable $e){

            Log::error('Something else', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    
    public function show(Role $Role)
    {
        $role = Role::where('id', '!=', $Role->id)->where('status',1)->get();
        $menu = Menu::where('status', 1)->where('parent_id', 0)->get();

        $checkedPermission = RolePermission::getAllCheckedPermissions($Role->id)->toArray();
        $roleMenu          = RoleMenu::where('role_id',$Role->id)->pluck('menu_id')->toArray();
        $data = [$Role, $role, $menu, $checkedPermission, $roleMenu];

        return $this->successResponse(
            $data,
            'Role fetched successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $Role)
    {

        $post = $request->all();
        request()->validate([
                'role' => 'required|unique:roles,name,'.$Role->id
            ]);

        $post = $request->all();
        $role_level = (isset($post['role_level']) && !empty($post['role_level']))?implode(',', $post['role_level']):"";
        $data['name'] = $request->role;
        $data['role_level'] = $role_level;
        $data['status'] = 1;
        $data['created_at'] = Auth::user()->id;

        try{
          $role = Role::addRoles($Role->id,$data);

         RoleMenu::where('role_id',$Role->id)->delete();

           if(isset($post['menu']) &&  !empty($post['menu'])){
              foreach($post['menu'] as $k => $m){
                  $menuData['role_id'] = $Role->id;
                  $menuData['menu_id'] = $m;
                  $menuData['is_parent'] = 1;
                  RoleMenu::addRoleMenu($Role->id,$m,$menuData);
              }
          }


            if(isset($post['subMenu']) &&  !empty($post['subMenu'])){
              foreach($post['subMenu'] as $k => $sm){
                  $subMenuData['role_id'] = $Role->id;
                  $subMenuData['menu_id'] = $sm;
                  $subMenuData['is_parent'] = 0;
                  RoleMenu::addRoleMenu($role->id,$sm,$subMenuData);
              }
          }

          RolePermission::where('role_id',$Role->id)->delete();
          if(isset($post['permission']) &&  !empty($post['permission'])){
              foreach($post['permission'] as $k => $p){
                  $roleData['role_id'] = $Role->id;
                  $roleData['permission_id'] = $p;
                  $roleData['access'] = (isset($post['access'][$p]) && !empty($post['access'][$p]))?implode(',', $post['access'][$p]):"";
                  RolePermission::addRolePermissions($Role->id,$p,$roleData);
              }
          }

           return $this->successResponse(
                $role,
                'Role updated successfully.'
            );

        }catch (\Throwable $e){

            Log::error('Something else', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = $this->role->delete($id);

        if (!$role) 
        {
            return $this->errorResponse(
                'Role not found.',
                404
            );
        }

        return $this->successResponse(
            null,
            'Role deleted successfully.'
        );
    }

    public function statusChange($id)
    {
        $role = $this->role->statusChange($id);

        if($role) {
            return $this->successResponse(
                $role->fresh(),
                'Role status updated successfully.'
            );
        }

        if (!$role) {
                return $this->errorResponse(
                    'Role not found.',
                    404
                );
            }
    }

    // public function reportingRole(Request $request)
    // {
    //     $roles  = Role::where('status',1)->where('id', $request->role_id)->value('role_level');

    //     $records = Role::where('status',1)->whereIn('id', explode(",",$roles))->get();

    //     return view('admin.roles.reporting_head', compact('records'));
    // }

    // public function reportingUser(Request $request)
    // {
    //     $records = User::where('status',1)->where('role_id', $request->reporting_head_id)->get();

    //     return view('admin.roles.reporting_head', compact('records'));
    // }
}
