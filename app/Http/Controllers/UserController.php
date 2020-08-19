<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');

        $query = User::where("id", "LIKE", "%$searchValue%")
            ->orWhere("name", "LIKE", "%$searchValue%")
            ->orWhere("username", "LIKE", "%$searchValue%")
            ->orderBy($sortBy, $orderBy);

        $data = $query->paginate($length);

        return new DataTableCollectionResource($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only([
            'name',
            'username',
            'password'
        ]), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            User::create($validator->validated());
            return $this->message->success('Usuário' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->only([
            'name',
            'username',
            'password'
        ]), [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'password' => 'nullable|min:6'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $user = User::findOrFail($id);
            $user->update($validator->validated());
            return $this->message->success('Usuário' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function delete(Request $request, int $id)
    {
        if (Auth::id() == $id) {
            return $this->message->error('Você não pode deletar o próprio usuário!')
                ->getResponse();
        }
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return $this->message->success('Usuário' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }
}
