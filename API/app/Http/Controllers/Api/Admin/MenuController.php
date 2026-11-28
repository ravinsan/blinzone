<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Menu\MenuRequest;
use App\Repository\Menu\MenuInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{

    Protected $menu;
    public function __construct(MenuInterface $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->menu->getall(); 
        return $this->successResponse($records, 'Menu retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $request)
    {
        $data = $request->all();
        $data['parent_id']   = !empty($request->parent_id) ? $request->parent_id : 0;
        $data['order_id']    = !empty($request->order_id) ? $request->order_id : 0;
        $data['created_by']  = Auth::user()->id;
       
        $mn = $this->menu->store($data);  

        if($mn) {
            return $this->successResponse(
                $mn,
                'Menu added successfully.',
                201
            );
        }

        return $this->errorResponse('Unable to add Menu.', 400);  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        $records = $this->menu->getall(); 
        $data = ['menu'=>$menu, 'records'=>$records];
        return $this->successResponse($data, 'Menu retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(MenuRequest $request, Menu $Menu)
    {
        $data = $request->all();
        $data['parent_id']   = !empty($request->parent_id) ? $request->parent_id : 0;
        $data['order_id']    = !empty($request->order_id) ? $request->order_id : 0;
        $data['created_by']  = Auth::user()->id;
        $mn = $this->menu->update($data, $Menu->id);

        if($mn) {
            return $this->successResponse(
                $mn,
                'Menu updated successfully.',
                200
            );
        }

        return $this->errorResponse('Unable to update Permission.', 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mn = $this->menu->delete($id);

        if($mn) {
            return $this->successResponse(null, 'Menu deleted successfully.', 200);
        }

        return $this->errorResponse('Unable to delete menu.', 400);
    }

    public function statusChange($id)
    {
        $mn = $this->menu->statusChange($id);

        if($mn) {
            return $this->successResponse(null, 'Menu Status Successfully Changed.', 200);
        }

        return $this->errorResponse('Unable to change menu status.', 400);
    }

    public function fetchSubmenu(Request $request)
    {
        $data['submenu'] = Menu::where("parent_id", $request->menu_id)
                                    ->get(["name", "id"]);
                                      
        return response()->json($data, 'Successfully get sub menu');
    }
}

