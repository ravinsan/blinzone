<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\User\UserRequest;
use App\Repository\User\UserInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    Protected $user;
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $records = $this->user->getall();
        return $this->successResponse(
                $records,
                'Users retrieved successfully.'
            );
    }

    public function store(UserRequest $request)
    {
        $data = $request->all();
        $image = '';
        if($request->hasFile('profile_picture'))
        {
            $file = $request->file('profile_picture');
            $filename = $file->getClientOriginalName();
            $extesion = $file->getClientOriginalExtension();
            if($extesion=='jpg' || $extesion=='jpeg' || $extesion=='png')
            {
                $image = uniqid().$filename;
                $destinationPath = public_path('/user/profile/');
                $file->move($destinationPath, $image);
            }
            else
            {
                return redirect()->back()->with(['fail' => 'Image Extension is not valid, Please upload the jpg, jpeg and png file only.']);
            }
        }

        $data['created_by'] = Auth::user()->id;
        $data['password']   = Hash::make($request->password);
        $data['text_password'] = $request->password;
        $data['profile_picture']   = asset('/user/profile/'.$image);
        $sh = $this->user->store($data);

        if($sh) {
            return $this->successResponse(
                $sh,
                'User added successfully.',
                201
            );
        }

        return $this->errorResponse('Unable to add User.', 400);
    }

    public function show(User $User)
    {
        $roles = Role::where('status', 1)->get();
        $roporting_head = Role::where('id', $User->role_id)->value('role_level');
        $reporntings    = Role::where('status',1)->whereIn('id', explode(",",$roporting_head))->get();
        $reportingusers          = User::where('status',1)->where('role_id', $User->reporting_head_id)->get();
        $data = ['User'=>$User, 'roles'=>$roles, 'roporting_head'=>$roporting_head, 'reporntings'=>$reporntings, 'reportingusers'=>$reportingusers];
        return $this->successResponse($User, 'User retrieved successfully.');
    }

    public function update(Request $request, User $User)
    {
        $this->validate($request, [
            'name'=>'required',
        ]);


        $image = '';
        $data = $request->all();

        if($request->hasFile('profile_picture'))
        {
            $oldimage = User::where('id', $User->id)->value('profile_picture');

            $arr = explode("/", $oldimage);

            File::delete('user/profile/' . $arr[count($arr) - 1]);

            $file = $request->file('profile_picture');
            $filename = $file->getClientOriginalName();
            $extesion = $file->getClientOriginalExtension();
            if($extesion=='jpg' || $extesion=='jpeg' || $extesion=='png')
            {
                $image = uniqid().$filename;
                $destinationPath = public_path('/user/profile/');
                $file->move($destinationPath, $image);
            }
            else
            {
                return $this->errorResponse('Image Extension is not valid, Please upload the jpg, jpeg and png file only.', 400);
            }
        }else{
            $imagenull = User::where('id', $User->id)->value('profile_picture');
        }



        $data['created_by']      = Auth::user()->id;
        $data['profile_picture'] = !empty($image) ? asset('user/profile/'.$image) : $imagenull;

        if(!empty($data['password'])){

            $data['password'] = Hash::make($data['password']);
            $data['text_password'] = $request->password;

        }else{

            $data = Arr::except($data,array('password'));

        }

        $sh = $this->user->update($data, $User->id);

        if($sh) {

            return $this->successResponse(
                $sh,
                'User updated successfully.',
                200
            );
        }

        return $this->errorResponse('Unable to update User.', 400);
    }

    public function destroy($id)
    {
        $user = $this->user->delete($id);

        if($user) {
            return $this->successResponse(null, 'User deleted successfully.', 200);
        }

        return $this->errorResponse('Unable to delete User.', 400);
    }

    public function statusChange($id)
    {
        $user = $this->user->statusChange($id);

        if($user) {
            return $this->successResponse(null, 'User status successfully changed.', 200);
        }

        return $this->errorResponse('Unable to change user status.', 400);
    }
}
