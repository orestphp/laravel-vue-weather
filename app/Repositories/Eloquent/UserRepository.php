<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param $googleUser
     * @return User
     */
    public function createUpdateViaGoogle($googleUser): User
    {
        // Check if User exists
        $saveUser = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ],[
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'password' => Hash::make($googleUser->name.'@'.$googleUser->id),
            'token' => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
            'nickname' => $googleUser->nickname,
            'avatar' => $googleUser->avatar,
            'expires_in' => $googleUser->expiresIn,
        ]);

        Auth::login($saveUser);

        return $saveUser;
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @return Model|null
     */
    public function find($id): ?Model
    {
        if (null == $user = $this->model->find($id)) {
            throw new ModelNotFoundException("User not found");
        }

        return $user;
    }

    public function getToken(User $user)
    {
        return $user->token;
    }
}
