<?php

namespace App\Repositories\Admin;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;


class UserRepository extends BaseRepository
{
    protected $role;

    function model()
    {
        $this->role = new Role();
        return User::class;
    }

    public function index($userTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('admin.user.index', ['tableConfig' => $userTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Crear usuario
            |--------------------------------------------------------------------------
            */

            $user = $this->model->create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'country_code' => $request->country_code,
                'phone' => (string) $request->phone,
                'status' => $request->status,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'postal_code' => $request->postal_code,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'location' => $request->location,
                'skills' => $request->skills,
                'about_me' => $request->about_me,
                'bio' => $request->bio,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Crear Persona relacionada
            |--------------------------------------------------------------------------
            */

            $user->persona()->create([
                'tipo_documento' => $request->input('persona.tipo_documento'),
                'numero_documento' => $request->input('persona.numero_documento'),
                'parentesco' => $request->input('persona.parentesco'),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Imagen
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile('image') &&
                $request->file('image')->isValid()
            ) {
                $user
                    ->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }

            /*
            |--------------------------------------------------------------------------
            | Rol
            |--------------------------------------------------------------------------
            */

            if ($request->role_id) {

                $role = $this->role->findOrFail(
                    $request->role_id
                );

                $user->assignRole($role);
            }

            DB::commit();

            return redirect()
                ->route('admin.user.index')
                ->with(
                    'success',
                    __('User Created Successfully')
                );

        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Buscar usuario
            |--------------------------------------------------------------------------
            */

            $user = $this->model->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Usuario reservado
            |--------------------------------------------------------------------------
            */

            if ($user->system_reserve) {

                DB::rollback();

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        __('This user cannot be update, It is system reserved.')
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Datos del usuario
            |--------------------------------------------------------------------------
            */

            $data = [
                'country_code' => $request->country_code,
                'email' => $request->email,
                'phone' => (string) $request->phone,
                'status' => $request->status,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'postal_code' => $request->postal_code,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'location' => $request->location,
                'skills' => $request->skills,
                'about_me' => $request->about_me,
                'bio' => $request->bio,
            ];

            /*
            |--------------------------------------------------------------------------
            | Actualizar contraseña solamente si fue ingresada
            |--------------------------------------------------------------------------
            */

            if ($request->filled('password')) {
                $data['password'] = Hash::make(
                    $request->password
                );
            }

            $user->update($data);

            /*
            |--------------------------------------------------------------------------
            | Crear o actualizar Persona
            |--------------------------------------------------------------------------
            |
            | updateOrCreate es importante porque pueden existir usuarios antiguos
            | de Cuba que todavía no tengan registro en personas.
            |
            */

            $user->persona()->updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'tipo_documento' =>
                        $request->input('persona.tipo_documento'),

                    'numero_documento' =>
                        $request->input('persona.numero_documento'),

                    'parentesco' =>
                        $request->input('persona.parentesco'),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Rol
            |--------------------------------------------------------------------------
            */

            if ($request->filled('role_id')) {

                $role = $this->role->findOrFail(
                    $request->role_id
                );

                $user->syncRoles($role);
            }

            /*
            |--------------------------------------------------------------------------
            | Imagen
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile('image') &&
                $request->file('image')->isValid()
            ) {

                $user->clearMediaCollection('image');

                $user
                    ->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }

            DB::commit();

            return redirect()
                ->route('admin.user.index')
                ->with(
                    'success',
                    __('User Updated Successfully')
                );

        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function status($id, $status)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->update(['status' => $status]);

            return json_encode(["resp" => $user]);

        } catch (Exception $e) {

            throw $e;
        }
    }

    public function destroy($id)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->destroy($id);
            return redirect()->back()->with('success', __('User Deleted Successfully'));

        } catch (Exception $e) {

            throw $e;
        }
    }
}
